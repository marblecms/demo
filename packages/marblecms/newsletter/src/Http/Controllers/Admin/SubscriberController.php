<?php

namespace MarbleCms\Newsletter\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use MarbleCms\Newsletter\Models\Subscriber;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::with('lists')
            ->latest()
            ->paginate(50);

        return view('newsletter::admin.newsletter.subscribers.index', compact('subscribers'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('marble.newsletter.subscribers.index')
            ->with('success', 'Subscriber deleted.');
    }
}
