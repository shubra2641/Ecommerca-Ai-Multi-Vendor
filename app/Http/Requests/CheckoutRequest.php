<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'customer_name' => 'required|string|max:191',
            'customer_email' => 'required|email|max:191',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'required|string|max:1000',
            'country' => 'required|integer|exists:countries,id',
            'governorate' => 'nullable|integer',
            'city' => 'nullable|integer',
            'notes' => 'nullable|string|max:2000',
            'gateway' => 'required|string',
            // shipping selection (required)
            'shipping_zone_id' => 'required|integer|exists:shipping_zones,id',
            'shipping_price' => 'required|numeric|min:0',
            'selected_address_id' => 'nullable|integer|exists:addresses,id',
            'shipping_estimated_days' => 'nullable|integer|min:0',
        ];

        // Conditional: if selected gateway requires transfer_image, enforce upload
        try {
            $gw = $this->input('gateway');
            if ($gw) {
                $g = \App\Models\PaymentGateway::where('slug', $gw)->first();
                if ($g && $g->requires_transfer_image) {
                    $rules['transfer_image'] = 'required|image|mimes:jpg,jpeg,png|max:5120'; // max 5MB
                }
            }
        } catch (\Throwable $e) {
            /* fail silently; no extra rule applied */
            null;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'country.required' => __('Please select a country'),
        ];
    }
}
