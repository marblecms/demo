<?php

namespace MarbleCms\Newsletter\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use MarbleCms\Newsletter\Models\Campaign;
use MarbleCms\Newsletter\Models\Subscriber;
use MarbleCms\Newsletter\Models\SubscriberList;

class NewsletterController extends Controller
{
    public function index()
    {
        $stats = [
            'total_subscribers'     => Subscriber::count(),
            'confirmed_subscribers' => Subscriber::confirmed()->count(),
            'pending_subscribers'   => Subscriber::where('status', 'pending')->count(),
            'total_campaigns'       => Campaign::count(),
            'sent_campaigns'        => Campaign::where('status', 'sent')->count(),
            'draft_campaigns'       => Campaign::where('status', 'draft')->count(),
            'total_lists'           => SubscriberList::count(),
        ];

        $recentCampaigns = Campaign::latest()->limit(5)->get();

        return view('newsletter::admin.newsletter.index', compact('stats', 'recentCampaigns'));
    }
}
