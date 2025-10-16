<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\Payments\PaymentGatewayService;
use App\Services\SerialAssignmentService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // Legacy third-party payment layer removed – now only core gateways (stripe, offline)
    // New form based checkout (server-rendered)
    public function showForm()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty'));
        }
        $vm = app(\App\Services\CheckoutViewBuilder::class)->build(
            $cart,
            session('currency_id'),
            session('applied_coupon_id'),
            auth()->user()
        );
        // Remove invalid coupon id if builder invalidated it
        if (! $vm['coupon'] && session()->has('applied_coupon_id')) {
            session()->forget('applied_coupon_id');
        }

        return view('front.checkout.index', $vm);
    }

    /**
     * Handle the main server-rendered checkout form submission.
     * Responsibilities:
     *  - Validate + normalise cart & address data
     *  - Re-run coupon & shipping validation server-side (trust nothing from client)
     *  - Persist Order + OrderItems atomically, adjusting / reserving stock
     *  - Kick off payment flow for offline or Stripe gateways
     *  - Emit user/admin/vendor notifications (best-effort, non-fatal)
     *
     * Failure strategy: early returns with redirect+flash for user-fixable issues
     * (empty cart, invalid gateway/shipping). Hard failures inside the DB transaction
     * will bubble and revert stock changes.
     */
    public function submitForm(CheckoutRequest $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty'));
        }
        // validated by CheckoutRequest
        $validated = $request->validated();
        $data = $validated;

        // optional shipping selection from frontend (hidden inputs). For robustness, if hidden location
        // inputs are empty (new user scenario), fall back to visible select names (country/governorate/city)
        $shippingData = $request->only([
            'shipping_zone_id', 'shipping_price', 'shipping_country', 'shipping_governorate', 'shipping_city',
        ]);
        // Fallback population to avoid "Invalid shipping selection" when user selected location but hidden inputs didn't sync yet
        if (empty($shippingData['shipping_country']) && $request->filled('country')) {
            $shippingData['shipping_country'] = $request->input('country');
        }
        if (empty($shippingData['shipping_governorate']) && $request->filled('governorate')) {
            $shippingData['shipping_governorate'] = $request->input('governorate');
        }
        if (empty($shippingData['shipping_city']) && $request->filled('city')) {
            $shippingData['shipping_city'] = $request->input('city');
        }
        $selectedAddressId = $request->input('selected_address_id');
        $selectedAddress = null;
        if ($selectedAddressId) {
            $selectedAddress = \App\Models\Address::where('id', $selectedAddressId)->where('user_id', $request->user()?->id)->first();
        }
        // Capture inline address fields (support multiple naming conventions)
        // Map form fields (customer_name, customer_phone, customer_address) to generic inline variables
        $inlineName = $request->input('customer_name')
            ?: $request->input('name')
            ?: $request->input('full_name')
            ?: $request->input('shipping_name');
        $inlineLine1 = $request->input('customer_address')
            ?: $request->input('line1')
            ?: $request->input('address_line1')
            ?: $request->input('shipping_line1')
            ?: $request->input('address1');
        $inlineLine2 = $request->input('line2') ?: $request->input('address_line2') ?: $request->input('shipping_line2') ?: $request->input('address2');
        $inlinePhone = $request->input('customer_phone')
            ?: $request->input('phone')
            ?: $request->input('shipping_phone');
        $inlinePostal = $request->input('postal_code') ?: $request->input('zip') ?: $request->input('postcode');
        $inlineCountry = $shippingData['shipping_country'] ?? $request->input('country');
        $inlineGov = $shippingData['shipping_governorate'] ?? $request->input('governorate');
        $inlineCity = $shippingData['shipping_city'] ?? $request->input('city');
        // Create an address record if none selected and inline fields present (always store)
        if (! $selectedAddress && $request->user() && ($inlineName || $inlineLine1 || $inlineCity)) {
            try {
                $hasDefault = $request->user()->addresses()->where('is_default', true)->exists();
                $addrPayload = [
                    'user_id' => $request->user()->id,
                    'title' => $request->input('title') ?: ($hasDefault ? 'Address' : 'Default'),
                    'name' => $inlineName,
                    'phone' => $inlinePhone,
                    'country_id' => $inlineCountry,
                    'governorate_id' => $inlineGov,
                    'city_id' => $inlineCity,
                    'line1' => $inlineLine1,
                    'line2' => $inlineLine2,
                    'postal_code' => $inlinePostal,
                    'is_default' => ! $hasDefault,
                ];
                $selectedAddress = \App\Models\Address::create($addrPayload);
                $selectedAddressId = $selectedAddress->id;
            } catch (\Throwable $e) {
                logger()->warning('Failed creating inline checkout address: ' . $e->getMessage());
            }
        }
        $gateway = PaymentGateway::where('slug', $data['gateway'])->where('enabled', true)->first();
        if (! $gateway) {
            return back()->with('error', __('Selected payment method is not available'));
        }
        // Log selected gateway for diagnostics (slug + driver)
        try {
            $logPayload = [
                'selected' => $data['gateway'] ?? null,
                'gateway_id' => $gateway->id ?? null,
                'slug' => $gateway->slug ?? null,
                'driver' => $gateway->driver ?? null,
            ];

            \Log::info('gateway.selected', $logPayload);
        } catch (\Throwable $_) {
        }
        // Build items array
        $user = $request->user();
        $total = 0;
        $itemRows = [];
        foreach ($cart as $pid => $row) {
            $product = Product::find($pid);
            if (! $product) {
                continue;
            }
            $qty = $row['qty'];
            $price = $row['price'];
            $total += $price * $qty;
            // Preserve variant (id or model) from cart so we can deduct correct variation stock
            $variant = $row['variant'] ?? null;
            if ($variant && is_int($variant)) {
                $variant = ProductVariation::find($variant);
            }
            $itemRows[] = ['product' => $product, 'qty' => $qty, 'price' => $price, 'variant' => $variant];
        }
        if (! $itemRows) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty'));
        }
        // Determine order currency based on session selection (store totals in that currency)
        $currentCurrency = session('currency_id') ? \App\Models\Currency::find(session('currency_id')) : \App\Models\Currency::getDefault();
        $defaultCurrency = \App\Models\Currency::getDefault();
        $orderTotalToStore = $total;
        $orderCurrencyCode = $defaultCurrency->code ?? config('app.currency', 'USD');
        try {
            if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                $orderTotalToStore = $defaultCurrency->convertTo($total, $currentCurrency, 2);
                $orderCurrencyCode = $currentCurrency->code;
            } else {
                $orderCurrencyCode = $defaultCurrency->code ?? config('app.currency', 'USD');
            }
        } catch (\Throwable $e) {
            $orderTotalToStore = $total;
            $orderCurrencyCode = $defaultCurrency->code ?? config('app.currency', 'USD');
        }

        // Server-side verify shipping selection if provided. Allow missing zone if user didn't choose yet.
        $verifiedShipping = $this->verifyShippingSelection($shippingData);
        // Only hard fail if a zone id was submitted but verification failed AND we have a country value (user thought they selected one)
        if (($shippingData['shipping_zone_id'] ?? null) && $shippingData['shipping_country'] && ! $verifiedShipping) {
            return back()->withInput()->withErrors(['shipping' => __('Invalid shipping selection. Please re-select your country & city.')]);
        }
        $finalShippingPrice = $verifiedShipping['price'] ?? null;
        $finalShippingZoneId = $verifiedShipping['zone_id'] ?? null;
        $finalShippingEta = $verifiedShipping['estimated_days'] ?? null;

        // apply coupon (if any) stored in session — validate again server-side
        $items_subtotal = $total; // base subtotal before discount
        $coupon = null;
        $discount = 0;
        if (session()->has('applied_coupon_id')) {
            $coupon = \App\Models\Coupon::find(session('applied_coupon_id'));
            if ($coupon) {
                // validate using a simple server-side check against base total
                if ($coupon->isValidForTotal($total)) {
                    $discounted_items_total = $coupon->applyTo($total);
                    $discount = round($total - $discounted_items_total, 2);
                    $total = $discounted_items_total; // reduce item total for order
                } else {
                    // invalid now, forget it
                    session()->forget('applied_coupon_id');
                    $coupon = null;
                }
            }
        }

        // include shipping in stored total
        if ($finalShippingPrice !== null) {
            $orderTotalToStore += $finalShippingPrice; // include shipping in stored total
        }

        // Supported payment drivers (expanded)
        $supportedDrivers = ['stripe', 'offline', 'paytabs', 'tap', 'weaccept', 'paypal', 'payeer'];
        // Support either driver or slug naming in gateway record (defensive)
        $gwKey = $gateway->driver ?? $gateway->slug;
        $isSupportedGateway = in_array($gwKey, $supportedDrivers, true) || in_array($gateway->slug, $supportedDrivers, true);

        if (! $isSupportedGateway) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected payment gateway is not supported. Please choose a different payment method.',
                ], 400);
            }

            $unsupportedMsg = __('Selected payment gateway is not supported. Please select another payment method.');

            return redirect()->route('cart.index')->with('error', $unsupportedMsg);
        }

        // If gateway is a redirect-based external gateway (PayPal, Tap) we should not persist
        // the Order until payment is confirmed. Build a checkout snapshot and initialize
        // the external payment which creates a Payment record without an Order.
        $redirectDrivers = ['paypal', 'tap', 'paytabs', 'weaccept', 'payeer'];
        $gwKey = $gateway->driver ?? $gateway->slug;
        if (in_array($gwKey, $redirectDrivers, true) || in_array($gateway->slug, $redirectDrivers, true)) {
            // prefer gateway-specific currency if available. For WeAccept default to EGP when not configured
            $preferredCurrency = $gateway->getGatewayConfig('weaccept_currency') ?? ($gateway->getGatewayConfig('currency') ?? null);
            // If gateway is WeAccept and no preferred currency set, force EGP to avoid PayMob currency errors
            if ((($gateway->driver ?? $gateway->slug) === 'weaccept') && empty($preferredCurrency)) {
                $snapshotCurrency = 'EGP';
            } else {
                $snapshotCurrency = $preferredCurrency ? strtoupper($preferredCurrency) : $orderCurrencyCode;
            }

            // build snapshot items separately to avoid very long inline closures and long single lines
            $snapshotItems = [];
            foreach ($itemRows as $it) {
                $snapshotItems[] = [
                    'product_id' => $it['product']->id ?? null,
                    'name' => $it['product']->name ?? null,
                    'qty' => $it['qty'],
                    'price' => $it['price'],
                    'variant' => is_object($it['variant']) ? ($it['variant']->id ?? null) : ($it['variant'] ?? null),
                ];
            }

            $snapshot = [
                'user_id' => $user?->id ?? null,
                'total' => $orderTotalToStore,
                'currency' => $snapshotCurrency,
                'items' => $snapshotItems,
                'shipping' => $finalShippingPrice ? ['price' => $finalShippingPrice, 'zone_id' => $finalShippingZoneId, 'eta' => $finalShippingEta] : null,
                'customer_name' => $inlineName ?? null,
                'customer_email' => $user?->email ?? null,
                // Include customer phone and common billing fields so gateways like WeAccept have required data
                'customer_phone' => $inlinePhone ?? null,
                'billing_city' => $inlineCity ?? null,
                'billing_country' => $inlineCountry ?? null,
                'billing_postal_code' => $inlinePostal ?? null,
                'billing_street' => $inlineLine1 ?? null,
                'billing_building' => $request->input('billing_building') ?? null,
                'billing_floor' => $request->input('billing_floor') ?? null,
                'billing_apartment' => $request->input('billing_apartment') ?? null,
                'shipping_address' => $shippingAddressPayload ?? null,
            ];
            try {
                $svc = app(\App\Services\Payments\PaymentGatewayService::class);
                // Log gateway selection and snapshot for debugging
                $initStartLog = [
                    'driver' => $gateway->driver,
                    'gateway_id' => $gateway->id,
                    'snapshot_items' => count($snapshot['items'] ?? []),
                ];

                \Log::info('gateway.init.start', $initStartLog);
                // Switch on driver if present, otherwise fallback to slug
                $initKey = $gateway->driver ?? $gateway->slug;
                switch ($initKey) {
                    case 'paypal':
                        $res = $svc->initPayPalFromSnapshot($snapshot, $gateway);
                        break;
                    case 'tap':
                        $res = $svc->initTapFromSnapshot($snapshot, $gateway);
                        break;
                    case 'paytabs':
                        $res = $svc->initPaytabsFromSnapshot($snapshot, $gateway);
                        break;
                    case 'weaccept':
                        $res = $svc->initWeacceptFromSnapshot($snapshot, $gateway);
                        break;
                    case 'payeer':
                        $res = $svc->initPayeerFromSnapshot($snapshot, $gateway);
                        break;
                    default:
                        // Fallback: attempt a generic init if available
                        if (method_exists($svc, 'initGenericRedirectGatewayFromSnapshot')) {
                            $res = $svc->initGenericRedirectGatewayFromSnapshot($snapshot, $gateway, $gateway->slug);
                        } else {
                            throw new \RuntimeException('Unsupported redirect gateway: ' . $gateway->driver);
                        }
                }
                $logSuccess = [
                    'driver' => $gateway->driver,
                    'payment_id' => $res['payment']->id ?? null,
                ];

                \Log::info('gateway.init.success', $logSuccess);

                // Log the redirect URL we will send to the browser so we can confirm server-side value
                $logRedirect = [
                    'driver' => $gateway->driver,
                    'redirect_url' => $res['redirect_url'] ?? null,
                    'payment_id' => $res['payment']->id ?? null,
                ];

                \Log::info('gateway.init.redirect', $logRedirect);
                // Normalize redirect: optionally wrap PayMob/WeAccept URL in local iframe host if explicitly enabled
                $originalRedirect = $res['redirect_url'] ?? null;
                $fallbackUrl = $res['fallback_url'] ?? null;
                $sentRedirect = $originalRedirect;
                $useLocalIframe = (bool) data_get($gateway->config ?? [], 'weaccept_use_local_iframe', env('PAYMOB_USE_LOCAL_IFRAME', false));
                try {
                    $isWeacceptDriver = (($gateway->driver ?? '') === 'weaccept');
                    $redirectLooksLikePayMob = $originalRedirect ? str_contains($originalRedirect, 'accept.paymob.com') : false;
                    $shouldUseLocalIframe = $useLocalIframe && ($isWeacceptDriver || $redirectLooksLikePayMob);

                    if ($shouldUseLocalIframe) {
                        if (! empty($res['payment']?->id)) {
                            $sentRedirect = route('payments.iframe.payment', ['payment' => $res['payment']->id]);
                        } else {
                            $iframeBase = url('/payments/iframe') . '?redirect=' . urlencode($originalRedirect);
                            if ($fallbackUrl) {
                                $iframeBase .= '&fallback=' . urlencode($fallbackUrl);
                            }

                            $sentRedirect = $iframeBase;
                        }
                    }
                } catch (\Throwable $_) {
                    /* ignore and use original redirect */
                }

                // If the request expects JSON (AJAX), return redirect_url JSON, otherwise redirect to our host or provider
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'redirect_url' => $sentRedirect, 'payment_id' => $res['payment']->id ?? null]);
                }

                return redirect()->away($sentRedirect);
            } catch (\Throwable $e) {
                $err = $e->getMessage();
                logger()->error('gateway.init_failed', ['driver' => $gateway->driver, 'error' => $err]);
                \Log::error('gateway.init.exception', ['driver' => $gateway->driver, 'error' => $err]);
                $initFailedMsg = __('Payment initialization failed. Please try another method.');

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => $initFailedMsg], 500);
                }

                return back()->with('error', $initFailedMsg);
            }
        }

        // Build order + items inside a closure (without yet finalizing payment external call)
        $txContext = [
            'user' => $user,
            'gateway' => $gateway,
            'total' => $total,
            'itemRows' => $itemRows,
            'orderTotalToStore' => $orderTotalToStore,
            'orderCurrencyCode' => $orderCurrencyCode,
            'finalShippingPrice' => $finalShippingPrice,
            'finalShippingZoneId' => $finalShippingZoneId,
            'finalShippingEta' => $finalShippingEta,
            'selectedAddress' => $selectedAddress,
        ];

        $order = DB::transaction(function () use ($txContext) {
            $user = $txContext['user'];
            $gateway = $txContext['gateway'];
            $total = $txContext['total'];
            $itemRows = $txContext['itemRows'];
            $orderTotalToStore = $txContext['orderTotalToStore'];
            $orderCurrencyCode = $txContext['orderCurrencyCode'];
            $finalShippingPrice = $txContext['finalShippingPrice'];
            $finalShippingZoneId = $txContext['finalShippingZoneId'];
            $finalShippingEta = $txContext['finalShippingEta'];
            $selectedAddress = $txContext['selectedAddress'];
            $shippingAddressPayload = null;
            $shippingAddressId = null;
            if ($selectedAddress) {
                $shippingAddressId = $selectedAddress->id;
                $shippingAddressPayload = [
                    'id' => $selectedAddress->id,
                    'title' => $selectedAddress->title ?? null,
                    'name' => $selectedAddress->name ?? null,
                    'phone' => $selectedAddress->phone ?? null,
                    'country_id' => $selectedAddress->country_id ?? null,
                    'governorate_id' => $selectedAddress->governorate_id ?? null,
                    'city_id' => $selectedAddress->city_id ?? null,
                    'line1' => $selectedAddress->line1 ?? null,
                    'line2' => $selectedAddress->line2 ?? null,
                    'postal_code' => $selectedAddress->postal_code ?? null,
                ];
            } else {
                // fallback: inline only (store minimal JSON even if we didn't create record)
                if ($inlineName || $inlineLine1 || $inlineCity) {
                    $shippingAddressPayload = [
                        'title' => 'Inline',
                        'name' => $inlineName,
                        'phone' => $inlinePhone,
                        'country_id' => $inlineCountry,
                        'governorate_id' => $inlineGov,
                        'city_id' => $inlineCity,
                        'line1' => $inlineLine1,
                        'line2' => $inlineLine2,
                        'postal_code' => $inlinePostal,
                    ];
                }
            }

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total' => $orderTotalToStore,
                'items_subtotal' => $total,
                'currency' => $orderCurrencyCode,
                'shipping_address' => $shippingAddressPayload,
                'shipping_address_id' => $shippingAddressId,
                'shipping_price' => $finalShippingPrice,
                'shipping_zone_id' => $finalShippingZoneId,
                'shipping_estimated_days' => $finalShippingEta,
                'payment_method' => $gateway->slug,
                'payment_status' => 'pending',
            ]);
            $orderHasBackorder = false;
            foreach ($itemRows as $it) {
                $variant = $it['variant'] ?? null;
                $variantName = null;
                $variantId = null;
                $variationModel = null;
                $variantAttributes = null;
                if ($variant) {
                    if (is_object($variant)) {
                        $variantName = $variant->name ?? null;
                        $variantId = $variant->id ?? null;
                        $variationModel = $variant instanceof \App\Models\ProductVariation ? $variant : null;
                        if ($variationModel) {
                            $variantAttributes = $variationModel->attribute_data ?? null;
                        }
                    } else {
                        try {
                            $variationModel = \App\Models\ProductVariation::find($variant);
                        } catch (\Exception $e) {
                            $variationModel = null;
                        }
                        if ($variationModel) {
                            $variantName = $variationModel->name ?? null;
                            $variantId = $variationModel->id;
                            $variantAttributes = $variationModel->attribute_data ?? null;
                        }
                    }
                }
                $itemName = $it['product']->name_translations['en'] ?? $it['product']->name ?? '';
                if ($variantName) {
                    $itemName = trim($itemName . ' - ' . $variantName);
                } elseif ($variantAttributes && is_array($variantAttributes) && count($variantAttributes)) {
                    $label = collect($variantAttributes)->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)->join(', ');
                    if ($label) {
                        $itemName = trim($itemName . ' - ' . $label);
                    }
                }
                $meta = null;
                if ($variantId || $variantName || $variantAttributes) {
                    $meta = ['variant_id' => $variantId];
                    if ($variantName) {
                        $meta['variant_name'] = $variantName;
                    }
                    if ($variantAttributes) {
                        $meta['attribute_data'] = $variantAttributes;
                    }
                }
                $product = $it['product'];
                $qty = (int) $it['qty'];
                $isBackorder = false;
                $committedNow = false;
                try {
                    if ($variantId && $variationModel && $variationModel->manage_stock) {
                        $available = $variationModel->stock_qty - $variationModel->reserved_qty;
                        $isBackorder = ($available < $qty && $variationModel->backorder);
                        if (! StockService::consumeVariation($variationModel, $qty)) {
                            throw new \Exception(__('out_of_stock', ['name' => $product->name]));
                        }
                        $committedNow = true;
                    } elseif ($product->manage_stock) {
                        $available = $product->stock_qty - $product->reserved_qty;
                        $isBackorder = ($available < $qty && $product->backorder);
                        if (! StockService::consume($product, $qty)) {
                            throw new \Exception(__('out_of_stock', ['name' => $product->name]));
                        }
                        $committedNow = true;
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
                if ($isBackorder) {
                    $orderHasBackorder = true;
                }
                $purchasedAt = now();
                $refundDays = (int) ($it['product']->refund_days ?? 0);
                $refundExpires = $refundDays > 0 ? $purchasedAt->clone()->addDays($refundDays) : null;
                $commissionData = ['rate' => null, 'commission' => null, 'vendor_earnings' => null];
                if ($product->vendor_id) {
                    $commissionData = \App\Services\CommissionService::breakdown($product, $qty, (float) $it['price']);
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product']->id,
                    'sku' => $it['product']->sku ?? null,
                    'name' => $itemName,
                    'qty' => $it['qty'],
                    'price' => $it['price'],
                    'vendor_commission_rate' => $commissionData['rate'],
                    'vendor_commission_amount' => $commissionData['commission'],
                    'vendor_earnings' => $commissionData['vendor_earnings'],
                    'meta' => $meta,
                    'is_backorder' => $isBackorder,
                    'committed' => $committedNow,
                    'purchased_at' => $purchasedAt,
                    'refund_expires_at' => $refundExpires,
                    'restocked' => false,
                ]);
            }
            if ($orderHasBackorder) {
                $order->has_backorder = true;
                $order->save();
            }

            return $order;
        });

        // Notify admins and user about new order (server-driven)
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            if ($admins && $admins->count()) {
                // send now to create DB notification immediately for admin header
                \Illuminate\Support\Facades\Notification::sendNow($admins, new \App\Notifications\AdminNewOrderNotification($order));
            }
            if ($order->user) {
                $order->user->notify(new \App\Notifications\UserOrderCreatedNotification($order));
            }
        } catch (\Throwable $e) {
            logger()->warning('Order notification failed: ' . $e->getMessage());
        }

        // Notify vendors: collect order items per vendor and notify each vendor about items belonging to them
        try {
            $order->load('items.product');
            $itemsByVendor = [];
            foreach ($order->items as $it) {
                $vendorId = $it->product?->vendor_id;
                if (! $vendorId) {
                    continue;
                }
                $itemsByVendor[$vendorId][] = $it;
            }
            foreach ($itemsByVendor as $vendorId => $items) {
                $vendor = \App\Models\User::find($vendorId);
                if ($vendor) {
                    $vendor->notify(new \App\Notifications\VendorNewOrderNotification($order, $items));
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Vendor new order notifications failed: ' . $e->getMessage());
        }

        // Gateway handling (offline / stripe / external placeholders)
        if ($gateway->driver === 'offline') {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'offline',
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);
            // Notify admins about new offline payment pending
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::sendNow($admins, new \App\Notifications\AdminPaymentNotification($payment, 'created'));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin payment notification failed: ' . $e->getMessage());
            }
            // If transfer image uploaded, store and attach
            try {
                if ($request->hasFile('transfer_image') && $request->file('transfer_image')->isValid()) {
                    $file = $request->file('transfer_image');
                    $path = $file->store('payments', 'public');
                    \App\Models\PaymentAttachment::create([
                        'payment_id' => $payment->id,
                        'path' => $path,
                        'mime' => $file->getClientMimeType(),
                        'user_id' => $payment->user_id,
                    ]);
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed storing transfer image for payment ' . ($payment->id ?? 'n/a') . ': ' . $e->getMessage());
            }
            // Clear cart
            session()->forget('cart');

            $orderRedirect = redirect()->route('orders.show', $order);
            $orderRedirect = $orderRedirect->with('success', __('Order created. Follow the payment instructions.'));
            $orderRedirect = $orderRedirect->with('refresh_admin_notifications', true);

            return $orderRedirect;
        }
        if ($gateway->driver === 'stripe') {
            $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
            $secret = $stripeCfg['secret_key'] ?? null;
            $publishable = $stripeCfg['publishable_key'] ?? null;
            if (! $secret || ! $publishable || ! class_exists(\Stripe\Stripe::class)) {
                // Stripe not configured: avoid leaving a half-created order and do not clear the cart
                try {
                    if (! empty($order) && $order->exists) {
                        $order->delete();
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Failed removing order after stripe misconfig: ' . $e->getMessage());
                }

                return redirect()->route('cart.index')->with('error', __('Stripe not configured'));
            }
            try {
                \Stripe\Stripe::setApiKey($secret);
                // Use StripeCheckoutBuilder to assemble payload (testable)
                $builder = new \App\Services\Stripe\StripeCheckoutBuilder($order, $coupon);
                $payload = $builder->build();

                // If the coupon exists in our DB but lacks a stripe_coupon_id, try to create one in Stripe
                if ($coupon && empty($coupon->stripe_coupon_id) && ! empty($secret) && class_exists(\Stripe\Coupon::class)) {
                    try {
                        // Create a Stripe coupon matching our coupon type (basic percent/fixed mapping)
                        $stripeCoupon = null;
                        if ($coupon->type === 'percent' && $coupon->value > 0) {
                            $stripeCoupon = \Stripe\Coupon::create([
                                'percent_off' => (float) $coupon->value,
                                'duration' => 'once',
                                'name' => $coupon->code,
                            ]);
                        } elseif ($coupon->type === 'fixed' && $coupon->value > 0) {
                            // Stripe fixed amount coupons require a currency and amount_off in cents
                            $stripeCoupon = \Stripe\Coupon::create([
                                'amount_off' => (int) round($coupon->value * 100),
                                'currency' => strtolower($order->currency ?? 'usd'),
                                'duration' => 'once',
                                'name' => $coupon->code,
                            ]);
                        }
                        if ($stripeCoupon && ! empty($stripeCoupon->id)) {
                            $coupon->stripe_coupon_id = $stripeCoupon->id;
                            $coupon->save();
                            // Add the stripe_coupon_id into payload metadata for reference
                            $payload['metadata']['stripe_coupon_id'] = $stripeCoupon->id;
                        }
                    } catch (\Exception $e) {
                        // Non-fatal: fallback to metadata only
                        logger()->warning('Failed creating stripe coupon for local coupon ' . ($coupon->id ?? 'n/a') . ': ' . $e->getMessage());
                    }
                }

                // Create checkout session using payload
                $session = \Stripe\Checkout\Session::create($payload);
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'method' => 'stripe',
                    'amount' => $order->total,
                    'currency' => $order->currency,
                    'status' => 'pending',
                    'payload' => ['stripe_session_id' => $session->id],
                ]);
                // Notify admins about pending stripe payment
                try {
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    if ($admins && $admins->count()) {
                        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'created'));
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Admin payment notification failed: ' . $e->getMessage());
                }
                // Store cart in session temporarily for restoration if payment fails
                session()->put('stripe_pending_cart', session('cart'));
                // Clear cart only after we successfully created a checkout session
                session()->forget('cart');
                // stripe redirect away: set a short-lived session flag so header in admin (if open) can refresh when admin views
                session()->flash('refresh_admin_notifications', true);

                return redirect()->away($session->url);
            } catch (\Exception $e) {
                return redirect()->route('orders.show', $order)->with('error', $e->getMessage())->with('refresh_admin_notifications', true);
            }
        }

        // External gateways real redirect implementations (incremental)
        if (in_array($gateway->driver, ['paytabs', 'tap', 'weaccept', 'paypal', 'payeer'], true)) {
            if (! $gateway->hasValidCredentials()) {
                // Rollback: mark order as cancelled & restore stock? Simpler: delete order entirely.
                $order->delete();

                return redirect()->route('cart.index')->with('error', __('Gateway credentials invalid'));
            }

            $svc = app(PaymentGatewayService::class);
            if ($gateway->driver === 'paypal') {
                try {
                    $result = $svc->initPayPal($order, $gateway);
                    session()->forget('cart');
                    session()->flash('refresh_admin_notifications', true);
                    $sent = $result['redirect_url'];
                    $useLocalIframe = (bool) data_get($gateway->config ?? [], 'weaccept_use_local_iframe', env('PAYMOB_USE_LOCAL_IFRAME', false));

                    $sentFallback = $sent;
                    $isWeacceptDriver = (($gateway->driver ?? '') === 'weaccept');
                    $looksLikePayMob = (! empty($sent) && str_contains($sent, 'accept.paymob.com'));
                    $shouldIframeWrap = $useLocalIframe && ($isWeacceptDriver || $looksLikePayMob);
                    if ($shouldIframeWrap) {
                        if (! empty($result['payment']?->id)) {
                            $sent = route('payments.iframe.payment', ['payment' => $result['payment']->id]);
                        } else {
                            $iframeUrl = url('/payments/iframe') . '?redirect=' . urlencode($sentFallback);
                            if (isset($result['fallback_url'])) {
                                $iframeUrl .= '&fallback=' . urlencode($result['fallback_url']);
                            }
                            $sent = $iframeUrl;
                        }
                    }

                    return redirect()->away($sent);
                } catch (\Throwable $e) {
                    Log::error('paypal.init.failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    try {
                        $order->delete();
                    } catch (\Throwable) {
                    }

                    return redirect()->route('cart.index')->with('error', __('PayPal init error: ') . $e->getMessage());
                }
            }
            if ($gateway->driver === 'tap') {
                try {
                    $result = $svc->initTap($order, $gateway);
                    session()->forget('cart');
                    session()->flash('refresh_admin_notifications', true);
                    $sent = $result['redirect_url'];
                    $useLocalIframe = (bool) data_get($gateway->config ?? [], 'weaccept_use_local_iframe', env('PAYMOB_USE_LOCAL_IFRAME', false));

                    $sentFallback = $sent;
                    $isWeacceptDriver = (($gateway->driver ?? '') === 'weaccept');
                    $looksLikePayMob = (! empty($sent) && str_contains($sent, 'accept.paymob.com'));
                    $shouldIframeWrap = $useLocalIframe && ($isWeacceptDriver || $looksLikePayMob);
                    if ($shouldIframeWrap) {
                        if (! empty($result['payment']?->id)) {
                            $sent = route('payments.iframe.payment', ['payment' => $result['payment']->id]);
                        } else {
                            $iframeUrl = url('/payments/iframe') . '?redirect=' . urlencode($sentFallback);
                            if (isset($result['fallback_url'])) {
                                $iframeUrl .= '&fallback=' . urlencode($result['fallback_url']);
                            }
                            $sent = $iframeUrl;
                        }
                    }

                    return redirect()->away($sent);
                } catch (\Throwable $e) {
                    Log::error('tap.init.failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    try {
                        $order->delete();
                    } catch (\Throwable) {
                    }

                    return redirect()->route('cart.index')->with('error', __('Tap init error: ') . $e->getMessage());
                }
            }

            // Payeer gateway implementation (form redirect)
            if ($gateway->driver === 'payeer') {
                $cfg = $gateway->config ?? [];
                $merchant = $cfg['payeer_merchant_id'] ?? null;
                $secret = $cfg['payeer_secret_key'] ?? null;
                $currency = strtoupper($order->currency ?? 'USD');
                try {
                    if (! $merchant || ! $secret) {
                        throw new \Exception('Missing merchant credentials');
                    }
                    $payment = null;
                    DB::beginTransaction();
                    $payment = Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'method' => 'payeer',
                        'amount' => $order->total,
                        'currency' => $currency,
                        'status' => 'pending',
                        'payload' => ['order_reference' => $order->id],
                    ]);
                    $m_shop = $merchant;
                    $m_orderid = $payment->id;
                    $m_amount = number_format($order->total, 2, '.', '');
                    $m_curr = $currency;
                    $m_desc = base64_encode('Order #' . $order->id);
                    $sign = strtoupper(hash('sha256', implode(':', [$m_shop, $m_orderid, $m_amount, $m_curr, $m_desc, $secret])));
                    DB::commit();
                    session()->forget('cart');
                    session()->flash('refresh_admin_notifications', true);

                    // Return auto-submit form
                    return response()->view('payments.auto_redirect', [
                        'action' => 'https://payeer.com/merchant/',
                        'fields' => [
                            'm_shop' => $m_shop,
                            'm_orderid' => $m_orderid,
                            'm_amount' => $m_amount,
                            'm_curr' => $m_curr,
                            'm_desc' => $m_desc,
                            'm_sign' => $sign,
                        ],
                    ]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::error('payeer.init.failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    try {
                        $order->delete();
                    } catch (\Throwable) {
                    }

                    return redirect()->route('cart.index')->with('error', __('Payeer init error: ') . $e->getMessage());
                }
            }

            // Other gateways not yet implemented
            $order->delete();

            return redirect()->route('cart.index')->with('error', __('Gateway not yet implemented'));
        }

        return redirect()->route('orders.show', $order)->with('error', __('Unsupported gateway'))->with('refresh_admin_notifications', true);
    }

    /**
     * Start external payment - redirect directly to gateway's actual website
     */
    // startExternalPayment legacy method removed with deprecated external integration purge

    // Create an order (simple cart structure expected)
    /**
     * API endpoint: create an order from a lightweight items array.
     * Stock for managed products is reserved/consumed immediately; payment stays pending.
     * Returns JSON (order_id, payment_id) on success.
     */
    public function create(\App\Http\Requests\CreateOrderRequest $request)
    {
        $data = $request->validated();
        $shippingData = $request->only([
            'shipping_zone_id', 'shipping_price', 'shipping_country', 'shipping_governorate', 'shipping_city',
        ]);

        $user = $request->user();

        $total = 0;
        $items = [];
        foreach ($data['items'] as $it) {
            $product = Product::findOrFail($it['product_id']);
            $qty = (int) $it['qty'];
            $price = $product->price ?? 0;
            $total += $price * $qty;
            $committedNow = false;
            $isBackorder = false;
            $variantModel = null;
            $variantId = $it['variant_id'] ?? null;
            if ($variantId) {
                $variantModel = ProductVariation::find($variantId);
                if (! $variantModel || $variantModel->product_id !== $product->id) {
                    abort(422, __('Invalid variant'));
                }
            }
            if ($variantModel && $variantModel->manage_stock) {
                $available = $variantModel->stock_qty - $variantModel->reserved_qty;
                $isBackorder = ($available < $qty && $variantModel->backorder);
                if (! StockService::consumeVariation($variantModel, $qty)) {
                    abort(422, __('out_of_stock', ['name' => $product->name]));
                }
                $committedNow = true;
            } elseif ($product->manage_stock) {
                $available = $product->stock_qty - $product->reserved_qty;
                $isBackorder = ($available < $qty && $product->backorder);
                if (! StockService::consume($product, $qty)) {
                    abort(422, __('out_of_stock', ['name' => $product->name]));
                }
                $committedNow = true;
            }
            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'committed_now' => $committedNow,
                'is_backorder' => $isBackorder,
                'variant' => $variantModel,
            ];
        }

        $verifiedShipping = $this->verifyShippingSelection($shippingData);
        $finalShippingPrice = $verifiedShipping['price'] ?? null;
        $finalShippingZoneId = $verifiedShipping['zone_id'] ?? null;
        $finalShippingEta = $verifiedShipping['estimated_days'] ?? null;
        $grand = $total + ($finalShippingPrice ?? 0);

        return DB::transaction(function () use ($user, $data, $total, $items, $grand, $finalShippingPrice, $finalShippingZoneId, $finalShippingEta) {
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total' => $grand,
                'items_subtotal' => $total,
                'currency' => config('app.currency', 'USD'),
                'shipping_address' => null,
                'shipping_price' => $finalShippingPrice,
                'shipping_zone_id' => $finalShippingZoneId,
                'shipping_estimated_days' => $finalShippingEta,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);

            foreach ($items as $it) {
                $variant = $it['variant'] ?? null;
                $variantName = null;
                $variantId = null;
                if ($variant) {
                    if (is_object($variant)) {
                        $variantName = $variant->name ?? null;
                        $variantId = $variant->id ?? null;
                    } else {
                        try {
                            $v = \App\Models\ProductVariation::find($variant);
                            if ($v) {
                                $variantName = $v->name ?? null;
                                $variantId = $v->id;
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }
                $itemName = $it['product']->name_translations['en'] ?? $it['product']->name ?? '';
                if ($variantName) {
                    $itemName = trim($itemName . ' - ' . $variantName);
                }
                $meta = null;
                if ($variantId || $variantName) {
                    $meta = ['variant_id' => $variantId, 'variant_name' => $variantName];
                }

                $purchasedAt = now();
                $refundDays = (int) ($it['product']->refund_days ?? 0);
                $refundExpires = $refundDays > 0 ? $purchasedAt->clone()->addDays($refundDays) : null;
                $commissionData = ['rate' => null, 'commission' => null, 'vendor_earnings' => null];
                if ($it['product']->vendor_id) {
                    $commissionData = \App\Services\CommissionService::breakdown($it['product'], (int) $it['qty'], (float) $it['price']);
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product']->id,
                    'sku' => $it['product']->sku ?? null,
                    'name' => $itemName,
                    'qty' => $it['qty'],
                    'price' => $it['price'],
                    'vendor_commission_rate' => $commissionData['rate'],
                    'vendor_commission_amount' => $commissionData['commission'],
                    'vendor_earnings' => $commissionData['vendor_earnings'],
                    'meta' => $meta,
                    'is_backorder' => $it['is_backorder'] ?? false,
                    'committed' => $it['committed_now'] ?? false,
                    'purchased_at' => $purchasedAt,
                    'refund_expires_at' => $refundExpires,
                    'restocked' => false,
                ]);
            }

            // For offline payments, create a pending payment record; for online gateways
            // you would create a payment intent here.
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => $order->payment_method,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            // Notify admins about created payment for API create flow
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'created'));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin payment notification failed: ' . $e->getMessage());
            }

            // Notify admins and user
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNewOrderNotification($order));
                }
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\UserOrderCreatedNotification($order));
                }
            } catch (\Throwable $e) {
                logger()->warning('Order notification failed: ' . $e->getMessage());
            }

            return response()->json(['order_id' => $order->id, 'payment_id' => $payment->id], 201);
        });
    }

    // Endpoint to submit an offline payment proof / mark as paid by user
    /**
     * Attach an offline payment proof / mark order as paid via manual transfer.
     */
    public function submitOfflinePayment(\App\Http\Requests\SubmitOfflinePaymentRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $data = $request->validated();

        // Payment gateway system removed; do not enforce gateway-specific checks here.

        return DB::transaction(function () use ($order, $data) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'offline',
                'amount' => $data['amount'],
                'currency' => $order->currency,
                'status' => 'received',
                'payload' => ['note' => $data['note'] ?? null],
            ]);

            // Notify admins that an offline payment proof was submitted / received
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'received'));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin payment notification failed: ' . $e->getMessage());
            }

            // Handle optional transfer image
            if ($request->hasFile('transfer_image')) {
                $file = $request->file('transfer_image');
                $path = $file->store('payment-attachments', 'public');
                $payment->attachments()->create([
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'user_id' => $order->user_id,
                ]);
            }

            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->save();

            // Assign serials for order items if needed
            try {
                app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
            } catch (\Exception $e) {
                // Log and continue; admin can re-run assignment later
                logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
            }

            return response()->json(['ok' => true, 'payment_id' => $payment->id]);
        });
    }

    // Simple webhook endpoint to mark payment as completed by gateway
    /**
     * Generic (very simplified) callback for non-Stripe gateways.
     * Real integrations should verify signatures / HMAC.
     */
    public function gatewayCallback(Request $request)
    {
        // This is a simplified placeholder. Real gateways require signature verification.
        $data = $request->all();
        $orderId = $data['order_id'] ?? $request->query('order_id');
        $status = $data['status'] ?? 'failed';

        // PayPal support removed: webhook/callback handling removed.

        if (! $orderId) {
            return response()->json(['error' => 'order_id required'], 400);
        }

        $order = Order::find($orderId);
        if (! $order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        if ($status === 'paid') {
            return DB::transaction(function () use ($order, $data) {
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'method' => $data['method'] ?? 'gateway',
                    'amount' => $data['amount'] ?? $order->total,
                    'currency' => $data['currency'] ?? $order->currency,
                    'status' => 'completed',
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'payload' => $data,
                ]);

                $order->payment_status = 'paid';
                $order->status = 'completed';
                $order->save();

                try {
                    app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                } catch (\Exception $e) {
                    logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                }

                return response()->json(['ok' => true]);
            });
        }

        return response()->json(['ok' => false]);
    }

    // Start a gateway payment (e.g., Stripe) and return a redirect URL
    /**
     * Start a payment for an existing order and return JSON with redirection
     * data (Stripe Checkout URL, or offline payment instructions).
     */
    public function startGatewayPayment(\App\Http\Requests\StartGatewayPaymentRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        // Determine gateway (prefer specified slug, else stripe if enabled, else offline)
        $gatewayQuery = PaymentGateway::query()->where('enabled', true);
        if (! empty($data['gateway'])) {
            $gatewayQuery->where('slug', $data['gateway']);
        }
        $gateway = $gatewayQuery->first();
        if (! $gateway) {
            return response()->json(['error' => 'no_enabled_gateway'], 422);
        }

        if ($gateway->driver === 'offline') {
            // Create pending payment (user will upload proof separately) and return instructions
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'offline',
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
            ]);

            return response()->json([
                'type' => 'offline',
                'payment_id' => $payment->id,
                'instructions' => $gateway->transfer_instructions,
                'requires_transfer_image' => $gateway->requires_transfer_image,
            ]);
        }

        if ($gateway->driver === 'stripe') {
            // Basic Stripe Checkout Session creation using stored keys (config)
            $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
            $secret = $stripeCfg['secret_key'] ?? null;
            $publishable = $stripeCfg['publishable_key'] ?? null;
            if (! $secret || ! $publishable) {
                return response()->json(['error' => 'stripe_not_configured'], 422);
            }
            try {
                // Lazy require stripe-php if installed via composer (not in current require list; provide lightweight fallback)
                if (! class_exists(\Stripe\Stripe::class)) {
                    return response()->json(['error' => 'stripe_library_missing'], 500);
                }
                \Stripe\Stripe::setApiKey($secret);
                $currency = strtolower($order->currency ?? 'usd');
                // Build a single line item for the full order total so Stripe charges exactly order->total
                $lineItems = [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => ['name' => 'Order #' . $order->id],
                        'unit_amount' => (int) round(($order->total ?? 0) * 100),
                    ],
                    'quantity' => 1,
                ]];
                $session = \Stripe\Checkout\Session::create([
                    'mode' => 'payment',
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'success_url' => url('/checkout/success?order=' . $order->id),
                    'cancel_url' => url('/checkout/cancel?order=' . $order->id),
                    'metadata' => ['order_id' => $order->id],
                ]);

                // Ensure we have a single pending stripe payment row we will later update via webhook
                $payment = Payment::where('order_id', $order->id)
                    ->where('method', 'stripe')
                    ->where('status', 'pending')
                    ->first();
                if (! $payment) {
                    $payment = Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'method' => 'stripe',
                        'amount' => $order->total,
                        'currency' => $order->currency,
                        'status' => 'pending',
                        'payload' => [],
                    ]);
                }
                $payload = $payment->payload ?: [];
                $payload['stripe_session_id'] = $session->id;
                $payment->payload = $payload;
                $payment->save();

                return response()->json([
                    'type' => 'stripe',
                    'publishable_key' => $publishable,
                    'checkout_url' => $session->url,
                    'session_id' => $session->id,
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'stripe_error', 'message' => $e->getMessage()], 500);
            }
        }

        // Unsupported gateway driver
        return response()->json(['error' => 'unsupported_gateway_driver'], 422);
    }

    // Stripe webhook handler
    /**
     * Stripe webhook (checkout.session.completed) -> finalize payment & order state.
     * Idempotency: looks for existing pending payment first before creating a new row.
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $event = json_decode($payload, true);
        if (! $event) {
            return response()->json(['error' => 'invalid_payload'], 400);
        }

        if (($event['type'] ?? '') === 'checkout.session.completed') {
            $session = $event['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    // Locate enabled stripe gateway
                    $gateway = PaymentGateway::where('driver', 'stripe')->where('enabled', true)->first();
                    // (Optional) verify signature if gateway webhook secret exists and stripe-php installed
                    if ($gateway && $gateway->stripe_webhook_secret && class_exists(\Stripe\Webhook::class)) {
                        try {
                            \Stripe\Webhook::constructEvent($payload, $sig, $gateway->stripe_webhook_secret);
                        } catch (\Exception $e) {
                            return response()->json(['error' => 'signature_verification_failed'], 400);
                        }
                    }

                    // Try updating existing pending payment (avoid duplicates)
                    $payment = Payment::where('order_id', $order->id)
                        ->where('method', 'stripe')
                        ->where('status', 'pending')
                        ->orderBy('id')
                        ->first();
                    $amount = ($session['amount_total'] ?? ($order->total * 100)) / 100;
                    if ($payment) {
                        $payment->status = 'completed';
                        $payment->amount = $amount; // sync final amount
                        $payment->currency = strtolower($session['currency'] ?? $order->currency);
                        $payment->transaction_id = $session['payment_intent'] ?? ($session['id'] ?? null);
                        $payload = $payment->payload ?: [];
                        $payload['webhook_event'] = $session;
                        $payment->payload = $payload;
                        $payment->save();
                        // Notify admins that payment completed via webhook
                        try {
                            $admins = \App\Models\User::where('role', 'admin')->get();
                            if ($admins && $admins->count()) {
                                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminPaymentNotification($payment, 'completed'));
                            }
                        } catch (\Throwable $e) {
                            logger()->warning('Admin payment notification failed: ' . $e->getMessage());
                        }
                    } else {
                        Payment::create([
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'method' => 'stripe',
                            'amount' => $amount,
                            'currency' => strtolower($session['currency'] ?? $order->currency),
                            'status' => 'completed',
                            'transaction_id' => $session['payment_intent'] ?? null,
                            'payload' => $session,
                        ]);
                        // If we created a fresh payment record for a completed webhook, notify admins
                        try {
                            $latest = \App\Models\Payment::where('order_id', $order->id)->orderByDesc('id')->first();
                            if ($latest) {
                                $admins = \App\Models\User::where('role', 'admin')->get();
                                if ($admins && $admins->count()) {
                                    $notification = new \App\Notifications\AdminPaymentNotification($latest, 'completed');
                                    \Illuminate\Support\Facades\Notification::send($admins, $notification);
                                }
                            }
                        } catch (\Throwable $e) {
                            logger()->warning('Admin payment notification failed: ' . $e->getMessage());
                        }
                    }

                    $order->payment_status = 'paid';
                    $order->status = 'completed';
                    $order->save();

                    // Ensure any OrderPaid listeners run (stock commit, vendor distribution, etc.)
                    try {
                        event(new \App\Events\OrderPaid($order));
                    } catch (\Throwable $e) {
                        logger()->error('OrderPaid event dispatch failed for order ' . ($order->id ?? 'n/a') . ': ' . $e->getMessage());
                    }

                    try {
                        app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                    } catch (\Exception $e) {
                        logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                    }
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    // Handle redirect back from Stripe Checkout (success)
    /**
     * User returned from Stripe success page. We attempt to confirm payment status
     * client-side (helpful before webhook arrives) and commit reserved stock.
     */
    public function checkoutSuccess(Request $request)
    {
        $orderId = $request->query('order');
        $order = $orderId ? Order::find($orderId) : null;
        // If no order provided (redirect from external gateway before order creation),
        // restore any pending cart backup and show the failure/cancel page without requiring auth.
        if (! $order) {
            // prefer stripe_pending_cart then tap_pending_cart
            if (session()->has('stripe_pending_cart')) {
                session()->put('cart', session('stripe_pending_cart'));
                session()->forget('stripe_pending_cart');
            } elseif (session()->has('tap_pending_cart')) {
                session()->put('cart', session('tap_pending_cart'));
                session()->forget('tap_pending_cart');
            }

            return view('payments.failure')
                ->with('order', null)
                ->with('payment', null)
                ->with('error_message', __('Payment was canceled. Your cart has been restored.'));
        }
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        // Attempt to verify session via stored pending stripe payment
        try {
            $payment = Payment::where('order_id', $order->id)->where('method', 'stripe')->where('status', 'pending')->first();
            if ($payment && ! empty($payment->payload['stripe_session_id']) && class_exists(\Stripe\Checkout\Session::class)) {
                $gateway = PaymentGateway::where('driver', 'stripe')->where('enabled', true)->first();
                if ($gateway) {
                    $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
                    if (! empty($stripeCfg['secret_key'])) {
                        \Stripe\Stripe::setApiKey($stripeCfg['secret_key']);
                    }
                    $session = \Stripe\Checkout\Session::retrieve($payment->payload['stripe_session_id']);
                    if ($session && ($session->payment_status ?? '') === 'paid') {
                        $amount = ($session->amount_total ?? ($order->total * 100)) / 100;
                        $payment->status = 'completed';
                        $payment->amount = $amount;
                        $payment->transaction_id = $session->payment_intent ?? ($session->id ?? null);
                        $payload = $payment->payload ?: [];
                        $payload['session'] = $session;
                        $payment->payload = $payload;
                        $payment->save();

                        $order->payment_status = 'paid';
                        $order->status = 'processing';
                        $order->save();
                        event(new \App\Events\OrderPaid($order));
                        // commit reserved stock now that payment is confirmed
                        try {
                            $order->loadMissing('items.product');
                            foreach ($order->items as $item) {
                                $qty = (int) $item->qty;
                                $product = $item->product;
                                if (! $product) {
                                    continue;
                                }
                                $variantId = null;
                                if (is_array($item->meta) && ! empty($item->meta['variant_id'])) {
                                    $variantId = $item->meta['variant_id'];
                                }
                                if ($variantId) {
                                    $variation = \App\Models\ProductVariation::find($variantId);
                                    if ($variation) {
                                        \App\Services\StockService::commitVariation($variation, $qty);
                                    }
                                } else {
                                    \App\Services\StockService::commit($product, $qty);
                                }
                            }
                        } catch (\Exception $e) {
                            logger()->error('Stripe commit stock failed for order ' . $order->id . ': ' . $e->getMessage());
                        }

                        try {
                            app(SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
                        } catch (\Exception $e) {
                            logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
                        }

                        // Remove cart backup since payment was successful
                        session()->forget('stripe_pending_cart');

                        return view('payments.success')
                            ->with('order', $order)
                            ->with('payment', $payment);
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Stripe success verification failed for order ' . ($order->id ?? 'n/a') . ': ' . $e->getMessage());
        }

        // If we couldn't verify, redirect to order page with informational toast that payment is pending verification
        return redirect()->route('orders.show', $order)->with('info', __('Payment completed but verification is pending. Check your order for updates.'));
    }

    /**
     * Stripe cancel return handler – mark order as cancelled if still unpaid.
     */
    public function checkoutCancel(Request $request)
    {
        $gateway = $request->query('gateway');
        // PayPal legacy branch removed entirely
        // Handle case when an order id is provided (Stripe cancel flow)
        $orderId = $request->query('order');
        $order = $orderId ? Order::find($orderId) : null;

        if ($order) {
            if (auth()->id() !== $order->user_id) {
                abort(403);
            }
            // Mark order & related pending payments as cancelled
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'cancelled';
            }
            if (! in_array($order->status, ['completed', 'refunded'])) {
                $order->status = 'cancelled';
            }
            $order->save();
            // Mark any pending payments for this order as cancelled
            try {
                foreach ($order->payments()->whereIn('status', ['pending', 'processing']) as $p) {
                    $p->status = 'cancelled';
                    $p->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed cancelling payments for order ' . $order->id . ': ' . $e->getMessage());
            }
            // Fire cancellation event for stock adjustments
            try {
                event(new \App\Events\OrderCancelled($order));
            } catch (\Throwable $e) {
                logger()->error('Order cancel event failed: ' . $e->getMessage());
            }

            // Restore cart backup if exists (Stripe backup)
            if (session()->has('stripe_pending_cart')) {
                session()->put('cart', session('stripe_pending_cart'));
                session()->forget('stripe_pending_cart');
            }

            $payment = $order->payments()->whereIn('status', ['cancelled', 'failed'])->latest()->first();
            $errorMessage = __('Payment was canceled. Your cart has been restored.');

            return view('payments.failure')
                ->with('order', $order)
                ->with('payment', $payment)
                ->with('error_message', $errorMessage);
        }

        // No order present: this is the common redirect-based gateway cancel flow (Tap/PayPal style)
        // Restore any pending cart backups we stored during init
        if (session()->has('stripe_pending_cart')) {
            session()->put('cart', session('stripe_pending_cart'));
            session()->forget('stripe_pending_cart');
        }
        if (session()->has('tap_pending_cart')) {
            session()->put('cart', session('tap_pending_cart'));
            session()->forget('tap_pending_cart');
        }

        $errorMessage = __('Payment was canceled. Your cart has been restored.');

        return view('payments.failure')
            ->with('order', null)
            ->with('payment', null)
            ->with('error_message', $errorMessage);
    }

    /**
     * Verify (or resolve) shipping selection; returns normalized structure or null.
     */
    private function verifyShippingSelection(array $shippingData): ?array
    {
        // New system: expects optional zone_id plus location (country / governorate / city)
        $zoneId = $shippingData['shipping_zone_id'] ?? null; // optional explicit zone selection
        $country = $shippingData['shipping_country'] ?? null;
        $gov = $shippingData['shipping_governorate'] ?? null;
        $city = $shippingData['shipping_city'] ?? null;
        if (! $country) {
            return null; // must at least have a country for matching
        }
        $resolver = new \App\Services\Shipping\ShippingResolver();
        $resolved = $resolver->resolve($country, $gov, $city, $zoneId);
        if (! $resolved) {
            return null;
        }

        return [
            'zone_id' => $resolved['zone_id'],
            'price' => $resolved['price'],
            'estimated_days' => $resolved['estimated_days'],
        ];
    }
}
