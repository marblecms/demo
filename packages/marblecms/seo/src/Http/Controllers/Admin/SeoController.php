<?php

namespace MarbleCms\Seo\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Marble\Admin\Models\Item;
use Marble\Admin\Models\Language;
use MarbleCms\Seo\Models\SeoMeta;

class SeoController extends Controller
{
    public function index()
    {
        $languages   = Language::orderBy('id')->get();
        $primaryLang = $languages->first();

        $items = Item::with(['blueprint'])
            ->where('status', 'published')
            ->latest()
            ->paginate(50);

        // Load all seo_meta rows for the current page and key by item_id → language_id
        $itemIds  = $items->pluck('id');
        $allMetas = SeoMeta::whereIn('item_id', $itemIds)->get()->groupBy('item_id');

        return view('seo::admin.seo.index', compact('items', 'languages', 'primaryLang', 'allMetas'));
    }

    public function edit(Item $item)
    {
        $languages = Language::orderBy('id')->get();

        // Key metas by language_id for easy access in the view
        $metas = SeoMeta::where('item_id', $item->id)
            ->get()
            ->keyBy('language_id');

        return view('seo::admin.seo.edit', compact('item', 'languages', 'metas'));
    }

    public function update(Request $request, Item $item)
    {
        $languages = Language::orderBy('id')->get();

        foreach ($languages as $lang) {
            $data = $request->input("lang.{$lang->id}", []);

            SeoMeta::updateOrCreate(
                ['item_id' => $item->id, 'language_id' => $lang->id],
                [
                    'title'         => $data['title']         ?? null,
                    'description'   => $data['description']   ?? null,
                    'og_image_url'  => $data['og_image_url']  ?? null,
                    'noindex'       => !empty($data['noindex']),
                    'canonical_url' => $data['canonical_url'] ?? null,
                ]
            );
        }

        return redirect()->route('marble.seo.edit', $item)
            ->with('success', 'SEO metadata saved.');
    }
}
