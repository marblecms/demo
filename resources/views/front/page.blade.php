<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name ?? 'Page' }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; color: #333; }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .page-image { max-width: 100%; height: auto; margin: 20px 0; border-radius: 4px; }
        .meta { color: #888; font-size: 0.9em; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>{{ $name }}</h1>

    <div class="meta">
        Blueprint: {{ $item->blueprint->name }} |
        Status: {{ $item->status }} |
        ID: {{ $item->id }}
    </div>

    @if($image)
        <img src="{{ $image }}" alt="{{ $name }}" class="page-image">
    @endif

    @if($content)
        <div class="content">
            {!! $content !!}
        </div>
    @endif

    <hr>
    <p><small>
        All values: <pre>{{ print_r($item->values(), true) }}</pre>
    </small></p>
</body>
</html>
