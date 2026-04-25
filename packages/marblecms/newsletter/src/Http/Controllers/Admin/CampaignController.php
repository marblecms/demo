<?php

namespace MarbleCms\Newsletter\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MarbleCms\Newsletter\Models\Campaign;
use MarbleCms\Newsletter\Models\SubscriberList;
use MarbleCms\Newsletter\Services\MailingService;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('list')->latest()->paginate(30);

        return view('newsletter::admin.newsletter.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $lists = SubscriberList::orderBy('name')->get();

        return view('newsletter::admin.newsletter.campaigns.form', compact('lists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'subject'  => 'required|string|max:500',
            'reply_to' => 'nullable|email|max:255',
            'body'     => 'required|string',
            'list_id'  => 'nullable|exists:newsletter_lists,id',
        ]);

        $campaign = Campaign::create($request->only('name', 'subject', 'reply_to', 'body', 'list_id'));

        return redirect()->route('marble.newsletter.campaigns.show', $campaign)
            ->with('success', 'Campaign created.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('list');

        $sentCount  = $campaign->sentCount();
        $openCount  = $campaign->openCount();
        $clickCount = $campaign->clickCount();

        return view('newsletter::admin.newsletter.campaigns.show', compact(
            'campaign', 'sentCount', 'openCount', 'clickCount'
        ));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return redirect()->route('marble.newsletter.campaigns.show', $campaign)
                ->with('error', 'Cannot edit a campaign that has already been sent.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'subject'  => 'required|string|max:500',
            'reply_to' => 'nullable|email|max:255',
            'body'     => 'required|string',
            'list_id'  => 'nullable|exists:newsletter_lists,id',
        ]);

        $campaign->update($request->only('name', 'subject', 'reply_to', 'body', 'list_id'));

        return redirect()->route('marble.newsletter.campaigns.show', $campaign)
            ->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('marble.newsletter.campaigns.index')
            ->with('success', 'Campaign deleted.');
    }

    public function send(Campaign $campaign, MailingService $mailer)
    {
        if ($campaign->status === 'sent') {
            return redirect()->route('marble.newsletter.campaigns.show', $campaign)
                ->with('error', 'Campaign has already been sent.');
        }

        $count = $mailer->send($campaign);

        return redirect()->route('marble.newsletter.campaigns.show', $campaign)
            ->with('success', "Campaign sent to {$count} subscriber(s).");
    }
}
