<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Show the admin profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request, HtmlSanitizer $sanitizer)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $payload = [
            'name' => is_string($request->name) ? $sanitizer->clean($request->name) : $request->name,
            'email' => is_string($request->email) ? $sanitizer->clean($request->email) : $request->email,
            'phone_number' => is_string($request->phone_number) ?
                $sanitizer->clean($request->phone_number) : $request->phone_number,
        ];
        $user->update($payload);

        return redirect()->route('admin.profile.edit')->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.edit')
            ->with('success', __('Password updated successfully'));
    }

    public function updateSettings(Request $request, HtmlSanitizer $sanitizer)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
        ]);

        // Here you would save settings to database or config
        $siteName = $sanitizer->clean($request->input('site_name'));
        $siteDescription = $request->filled('site_description') ?
            $sanitizer->clean($request->input('site_description')) : null;

        // Persist to Setting model (lightweight)
        $s = \App\Models\Setting::first();
        if (! $s) {
            $s = new \App\Models\Setting();
        }
        $s->site_name = $siteName;
        $s->site_description = $siteDescription;
        $s->timezone = $request->input('timezone');
        $s->date_format = $request->input('date_format');
        $s->save();

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings updated successfully'));
    }

    /**
     * Show the admin settings page.
     */
    public function settings()
    {
        return view('admin.profile.settings');
    }
}
