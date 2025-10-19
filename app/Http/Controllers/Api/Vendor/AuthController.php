<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $r)
    {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $data['email'])->where('role', 'vendor')->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // prevent login if email not verified
        if (empty($user->email_verified_at)) {
            return response()->json(['message' => 'Account not activated'], 403);
        }

        // create token via sanctum
        if (method_exists($user, 'createToken')) {
            $token = $user->createToken('vendor-api-token')->plainTextToken;

            return response()->json(['token' => $token, 'vendor' => ['id' => $user->id, 'name' => $user->name]]);
        }

        // fallback: return basic user info
        return response()->json(['vendor' => ['id' => $user->id, 'name' => $user->name]]);
    }

    public function logout(Request $r)
    {
        $user = $r->user();
        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return response()->json(['ok' => true]);
    }

    public function profile(Request $r)
    {
        $user = $r->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'whatsapp' => $user->whatsapp_number,
            'balance' => $user->balance ?? 0,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function updateProfile(Request $r)
    {
        $user = $r->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $r->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'whatsapp' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Map phone and whatsapp to correct database fields
        if (isset($data['phone'])) {
            $data['phone_number'] = $data['phone'];
            unset($data['phone']);
        }
        if (isset($data['whatsapp'])) {
            $data['whatsapp_number'] = $data['whatsapp'];
            unset($data['whatsapp']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'whatsapp' => $user->whatsapp_number,
                'balance' => $user->balance ?? 0,
            ],
        ]);
    }
}
