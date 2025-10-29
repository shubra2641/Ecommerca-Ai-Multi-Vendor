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
            // shipping selection (required)
            'shipping_zone_id' => 'required|integer|exists:shipping_zones,id',
            'shipping_price' => 'required|numeric|min:0',
            'selected_address_id' => 'nullable|integer|exists:addresses,id',
            'shipping_estimated_days' => 'nullable|integer|min:0',
            // payment gateway
            'gateway' => 'required|string|in:cod,paypal,stripe,tap,paymob,hyperpay,kashier,fawry,thawani,opay,paymob_wallet,paytabs,binance,nowpayments,payeer,perfect_money,telr,clickpay',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'country.required' => __('Please select a country'),
        ];
    }
}
