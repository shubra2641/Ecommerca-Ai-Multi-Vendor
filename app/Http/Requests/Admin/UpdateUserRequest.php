<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user')?->id ?? null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($userId ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,vendor,user',
            'balance' => 'nullable|numeric|min:0',
            'approved' => 'nullable|boolean',
        ];
    }
}
