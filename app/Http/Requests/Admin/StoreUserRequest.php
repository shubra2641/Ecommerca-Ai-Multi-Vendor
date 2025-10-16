<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,vendor,user',
            'balance' => 'nullable|numeric|min:0',
            'approved' => 'nullable|boolean',
        ];
    }
}
