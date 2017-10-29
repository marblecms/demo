@extends( $node->attributes->layout->value[$locale] ? $node->attributes->layout->processedValue[$locale] : 'layouts.default')

@section("content")

    {!! $node->attributes->content->processedValue[$locale] !!}
    
@endsection