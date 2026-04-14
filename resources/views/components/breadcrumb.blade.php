@php
    use Marble\Admin\Models\Site;
    use Marble\Admin\Models\Item;

    $site       = Site::where('active', true)->where('is_default', true)->first() ?? Site::where('active', true)->first();
    $rootItemId = $site?->root_item_id;

    // Walk ancestors, stop at (and include) the site root item
    $ancestors = collect();
    $check = $item->parent_id ? Item::find($item->parent_id) : null;
    while ($check) {
        $ancestors->prepend($check);
        if ($check->id === $rootItemId) break;
        $check = $check->parent_id ? Item::find($check->parent_id) : null;
    }

    // If we didn't reach the root item (item IS the root), ancestors will be empty
@endphp

@if($item->id !== $rootItemId)
<nav class="breadcrumb">
    <a href="/">{{ $ancestors->first()?->id === $rootItemId ? $ancestors->first()->name() : 'Home' }}</a>
    @foreach($ancestors->slice(1) as $anc)
        <span class="breadcrumb-sep">/</span>
        <a href="{{ \Marble\Admin\Facades\Marble::url($anc) }}">{{ $anc->name() }}</a>
    @endforeach
    <span class="breadcrumb-sep">/</span>
    <span>{{ $item->name() }}</span>
</nav>
@endif
