{{-- SEO meta component: drop inside <head> --}}
@if($title)
<title>{{ $title }}</title>
<meta property="og:title" content="{{ $title }}">
@endif

@if($description)
<meta name="description" content="{{ $description }}">
<meta property="og:description" content="{{ $description }}">
@endif

@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif

@if($canonical)
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:url" content="{{ $canonical }}">
@endif

@if($noindex)
<meta name="robots" content="noindex, nofollow">
@endif

<meta property="og:type" content="website">

@if($jsonLd && !empty($jsonLd))
@foreach($jsonLd as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endforeach
@endif
