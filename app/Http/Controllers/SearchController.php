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
            // Find published items whose name or any item value (including variant values) contains the search term
            $fromValues = DB::table('item_values')
                ->where('value', 'LIKE', '%' . $query . '%')
                ->pluck('item_id');

            $fromVariants = DB::table('item_variant_values')
                ->join('item_variants', 'item_variants.id', '=', 'item_variant_values.variant_id')
                ->where('item_variant_values.value', 'LIKE', '%' . $query . '%')
                ->pluck('item_variants.item_id');

            $itemIds = $fromValues->concat($fromVariants)->unique();

            $results = Item::where('status', 'published')
                ->where(function ($q) use ($query, $itemIds) {
                    $q->whereIn('id', $itemIds);
                })
                ->with('blueprint')
                ->limit(30)
                ->get()
                ->filter(function (Item $item) {
                    // Exclude items whose blueprint explicitly marks them as not public
                    $bp = $item->blueprint;
                    return !$bp || ($bp->api_public ?? true) !== false;
                })
                ->values();
        }

        return view('marble-pages.search', compact('query', 'results'));
    }
}
