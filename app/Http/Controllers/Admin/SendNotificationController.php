<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\Request;

class SendNotificationController extends Controller
{
    public function create()
    {
        $languages = \App\Models\Language::where('is_active', 1)->orderByDesc('is_default')->get();

        return view('admin.notifications.send', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:user,vendor',
            'title' => 'nullable|array',
            'message' => 'nullable|array',
            'url' => 'nullable|url',
        ]);

        $role = $request->input('role');
        $title = $request->input('title', []);
        $message = $request->input('message', []);
        $url = $request->input('url');

        // Narrow target: all users with role
        $users = User::where('role', $role)->get();

        foreach ($users as $u) {
            $u->notify(new AdminBroadcastNotification($title, $message, $url));
        }

        return redirect()->route('admin.notifications.index')->with('status', __('Notifications sent'));
    }
}
