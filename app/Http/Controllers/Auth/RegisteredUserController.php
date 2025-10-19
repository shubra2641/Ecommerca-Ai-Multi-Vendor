<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VendorApprovalNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:user,vendor'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'whatsapp_number' => $request->whatsapp_number,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Address creation during registration is not performed here. Addresses are managed
        // via the Checkout page or the user account addresses page.

        $admins = User::where('role', 'admin')->get();
        // notify admins of any new registration (database + optional mail)
        if ($admins && $admins->count()) {
            // Send synchronously to ensure admin header receives DB notification immediately
            \Illuminate\Support\Facades\Notification::sendNow(
                $admins,
                new \App\Notifications\AdminUserRegisteredNotification($user)
            );
        }

        if ($user->role === 'vendor') {
            if ($admins && $admins->count()) {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $admins,
                    new \App\Notifications\AdminVendorRegisteredNotification($user)
                );
            }
            Notification::send($admins, new VendorApprovalNotification($user));

            // If vendor needs approval, do not auto-login — redirect to login with a pending message
            if (is_null($user->approved_at)) {
                event(new Registered($user));

                return redirect()->route('login')
                    ->with('status', 'Your vendor account is pending approval by an administrator. You will be notified once approved.')
                    ->with('refresh_admin_notifications', true);
            }
        }

    // Log creation for diagnostics (kept minimal)
    // Log::debug('REGISTER: created user', ['id' => $user->id, 'role' => $user->role]);

    // Authenticate user before firing Registered event so listeners that send
    // notifications or render mail views do not interrupt the login flow.
        Auth::login($user);

        event(new Registered($user));

        return redirect(route('dashboard', absolute: false))->with('refresh_admin_notifications', true);
    }
}
