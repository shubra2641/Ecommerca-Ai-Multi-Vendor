<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesErrors;
use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    use HandlesErrors;

    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'nullable|string|max:255',
            'keys.auth' => 'nullable|string|max:255',
        ]);
        $endpoint = $data['endpoint'];
        $p256dh = $data['keys']['p256dh'] ?? null;
        $auth = $data['keys']['auth'] ?? null;
        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'user_id' => $request->user()?->id,
                'p256dh' => $p256dh,
                'auth' => $auth,
                'agent' => substr($request->userAgent() ?? '', 0, 255),
                'locale' => app()->getLocale(),
            ]
        );

        return response()->json(['ok' => true, 'id' => $sub->id]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['endpoint' => 'required|string']);
        PushSubscription::where('endpoint', $data['endpoint'])
            ->when($request->user(), fn ($q) => $q->where('user_id', $request->user()->id))
            ->delete();

        return response()->json(['ok' => true]);
    }
}
