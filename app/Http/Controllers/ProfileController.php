<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('addresses'); // for profile completion
        $role = $this->getUserRole($user);

        return $this->getProfileView($role, $user);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $role = $this->getUserRole($user);

        $data = $this->validateProfileData($request, $user, $role);
        $this->updateUserProfile($user, $data, $role);

        return $this->getRedirectResponse($role);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $role = $this->getUserRole($user);

        $this->validatePassword($request);
        $this->updateUserPassword($user, $request->password);

        return $this->getPasswordRedirectResponse($role);
    }

    /**
     * Update admin settings.
     */
    public function updateSettings(Request $request, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $this->validateSettings($request);
        $this->saveSettings($request, $sanitizer);

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings updated successfully'));
    }

    /**
     * Show admin settings page.
     */
    public function settings(): View
    {
        return view('admin.profile.settings');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->validateDeletion($request);
        $this->deleteUser($request->user());

        return Redirect::to('/');
    }

    /**
     * Get user role.
     */
    private function getUserRole($user): string
    {
        $role = $user->role ?? 'user';

        return match ($role) {
            'admin' => 'admin',
            'vendor' => 'vendor',
            default => 'user',
        };
    }

    /**
     * Get profile view based on role.
     */
    private function getProfileView(string $role, $user): View
    {
        return match ($role) {
            'admin' => view('admin.profile.edit', compact('user')),
            'vendor' => view('vendor.profile.edit', compact('user')),
            default => view('front.account.profile', compact('user')),
        };
    }

    /**
     * Validate profile data based on role.
     */
    private function validateProfileData(Request $request, $user, string $role): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if ($role === 'user') {
            $rules['phone_number'] = ['nullable', 'string', 'max:50'];
            $rules['whatsapp_number'] = ['nullable', 'string', 'max:50'];
            $rules['password'] = ['nullable', 'string', 'min:6', 'confirmed'];
        } else {
            $rules['phone_number'] = ['nullable', 'string', 'max:20'];
        }

        return $request->validate($rules);
    }

    /**
     * Update user profile.
     */
    private function updateUserProfile($user, array $data, string $role): void
    {
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (isset($data['phone_number'])) {
            $user->phone_number = $data['phone_number'];
        }

        if ($role === 'user' && isset($data['whatsapp_number'])) {
            $user->whatsapp_number = $data['whatsapp_number'];
        }

        if ($role === 'user' && ! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }

    /**
     * Validate password update.
     */
    private function validatePassword(Request $request): void
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    }

    /**
     * Update user password.
     */
    private function updateUserPassword($user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }

    /**
     * Validate settings.
     */
    private function validateSettings(Request $request): void
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
        ]);
    }

    /**
     * Save settings.
     */
    private function saveSettings(Request $request, HtmlSanitizer $sanitizer): void
    {
        $setting = \App\Models\Setting::first() ?? new \App\Models\Setting();

        $setting->site_name = $sanitizer->clean($request->input('site_name'));
        $setting->site_description = $request->filled('site_description')
            ? $sanitizer->clean($request->input('site_description'))
            : null;
        $setting->timezone = $request->input('timezone');
        $setting->date_format = $request->input('date_format');
        $setting->save();
    }

    /**
     * Validate account deletion.
     */
    private function validateDeletion(Request $request): void
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
    }

    /**
     * Delete user account.
     */
    private function deleteUser($user): void
    {
        DB::transaction(function () use ($user): void {
            $user->delete();
        });

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Get redirect response based on role.
     */
    private function getRedirectResponse(string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.profile.edit')->with('success', __('Profile updated successfully.')),
            'vendor' => redirect()->route('vendor.profile.edit')->with('success', __('Profile updated successfully.')),
            default => Redirect::route('profile.edit')->with('status', 'profile-updated'),
        };
    }

    /**
     * Get password redirect response based on role.
     */
    private function getPasswordRedirectResponse(string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.profile.edit')->with('success', __('Password updated successfully')),
            'vendor' => redirect()->route('vendor.profile.edit')->with('success', __('Password updated successfully')),
            default => redirect()->route('profile.edit')->with('success', __('Password updated successfully')),
        };
    }
}
