<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Marble\Admin\Facades\Marble;
use Marble\Admin\Models\Language;

class FrontController extends Controller
{
    public function show(Request $request, string $locale, string $slug = '')
    {
        // Validate locale
        $language = Language::where('code', $locale)->first();
        if (!$language) {
            abort(404);
        }

        // Set Marble locale for this request
        Marble::setLocale($locale);

        // Find item by slug in the given language
        $item = Marble::findItem('simple_page', 'slug', $slug ?: 'home', $locale);

        if (!$item || !$item->isPublished()) {
            abort(404);
        }

        $item->load('blueprint.fields.fieldType');

        return view('front.page', [
            'item' => $item,
            'locale' => $locale,
            'name' => $item->value('name'),
            'content' => $item->value('content'),
            'image' => $item->value('image'),
        ]);
    }

    public function test(int $id)
    {
        $item = Marble::item($id);

        if (!$item) {
            abort(404, "Item #{$id} not found");
        }

        return view('front.page', [
            'item' => $item,
            'locale' => app()->getLocale(),
            'name' => $item->value('name'),
            'content' => $item->value('content'),
            'image' => $item->value('image'),
        ]);
    }
}
