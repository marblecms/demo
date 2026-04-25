<?php

namespace MarbleCms\Newsletter\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MarbleCms\Newsletter\Events\SubscriberConfirmed;
use MarbleCms\Newsletter\Events\SubscriberUnsubscribed;
use MarbleCms\Newsletter\Mail\ConfirmationMail;
use MarbleCms\Newsletter\Models\Subscriber;
use MarbleCms\Newsletter\Models\SubscriberList;

class SubscribeController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'name'     => 'nullable|string|max:255',
            'list_id'  => 'nullable|exists:newsletter_lists,id',
            'redirect' => 'nullable|string',
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => strtolower(trim($request->email))],
            [
                'name'               => $request->name,
                'status'             => config('newsletter.double_opt_in', true) ? 'pending' : 'confirmed',
                'confirmation_token' => Str::random(64),
                'unsubscribe_token'  => Str::random(64),
                'confirmed_at'       => config('newsletter.double_opt_in', true) ? null : now(),
            ]
        );

        // Attach to list if provided
        if ($request->list_id) {
            $subscriber->lists()->syncWithoutDetaching([
                $request->list_id => ['subscribed_at' => now()],
            ]);
        }

        // Send confirmation email for double opt-in
        if (config('newsletter.double_opt_in', true) && $subscriber->status === 'pending') {
            Mail::to($subscriber->email)->send(new ConfirmationMail($subscriber));
        }

        // If already confirmed, re-attach to list silently
        if ($subscriber->status === 'confirmed') {
            SubscriberConfirmed::dispatch($subscriber);
        }

        $redirectTo = $request->input('redirect', '/');

        // Only allow relative paths to prevent open redirects
        if (!is_string($redirectTo) || !str_starts_with($redirectTo, '/') || str_starts_with($redirectTo, '//')) {
            $redirectTo = '/';
        }

        return redirect($redirectTo)->with(
            'newsletter_subscribed',
            config('newsletter.double_opt_in', true)
                ? 'Please check your email to confirm your subscription.'
                : 'You have been subscribed successfully.'
        );
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('confirmation_token', $token)->firstOrFail();

        if ($subscriber->status !== 'confirmed') {
            $subscriber->update([
                'status'             => 'confirmed',
                'confirmed_at'       => now(),
                'confirmation_token' => null,
            ]);

            SubscriberConfirmed::dispatch($subscriber);
        }

        return view('newsletter::public.confirm', compact('subscriber'));
    }

    public function showUnsubscribe(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        return view('newsletter::public.unsubscribe', compact('subscriber'));
    }

    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        $subscriber->update(['status' => 'unsubscribed']);

        SubscriberUnsubscribed::dispatch($subscriber);

        return view('newsletter::public.unsubscribe', ['subscriber' => $subscriber, 'done' => true]);
    }
}
