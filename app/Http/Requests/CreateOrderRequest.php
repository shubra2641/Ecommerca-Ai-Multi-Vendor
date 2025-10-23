<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'shipping_zone_id' => 'nullable|integer|exists:shipping_zones,id',
            'shipping_price' => 'nullable|numeric|min:0',
            'shipping_country' => 'nullable|integer',
            'shipping_governorate' => 'nullable|integer',
            'shipping_city' => 'nullable|integer',
        ];
    }
}
