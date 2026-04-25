<?php

namespace MarbleCms\Newsletter\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MarbleCms\Newsletter\Models\SubscriberList;

class ListController extends Controller
{
    public function index()
    {
        $lists = SubscriberList::withCount('subscribers')->latest()->get();

        return view('newsletter::admin.newsletter.lists.index', compact('lists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        SubscriberList::create($request->only('name', 'description'));

        return redirect()->route('marble.newsletter.lists.index')
            ->with('success', 'List created.');
    }

    public function destroy(SubscriberList $list)
    {
        $list->delete();

        return redirect()->route('marble.newsletter.lists.index')
            ->with('success', 'List deleted.');
    }
}
