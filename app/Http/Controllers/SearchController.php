<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marble\Admin\Models\Item;
use Marble\Admin\Models\ItemValue;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query   = trim($request->input('q', ''));
        $results = collect();

        if (strlen($query) >= 2) {
            // Find published items whose name or any item value contains the search term
            $itemIds = DB::table('item_values')
                ->where('value', 'LIKE', '%' . $query . '%')
                ->pluck('item_id')
                ->unique();

            $results = Item::where('status', 'published')
                ->where(function ($q) use ($query, $itemIds) {
                    $q->whereIn('id', $itemIds);
                })
                ->with('blueprint')
                ->limit(30)
                ->get()
                ->filter(function (Item $item) {
                    // Exclude items whose blueprint marks them as not public
                    $bp = $item->blueprint;
                    return $bp && ($bp->api_public ?? true) !== false;
                })
                ->values();
        }

        return view('marble-pages.search', compact('query', 'results'));
    }
}
