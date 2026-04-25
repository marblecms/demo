<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
</head>
<body>
    <div>
        @if(!empty($done))
            <h1>You have been unsubscribed.</h1>
            <p>We've removed {{ $subscriber->email }} from our mailing list.</p>
        @else
            <h1>Unsubscribe</h1>
            <p>Are you sure you want to unsubscribe <strong>{{ $subscriber->email }}</strong>?</p>
            <form method="POST" action="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">
                @csrf
                <button type="submit">Yes, unsubscribe me</button>
            </form>
        @endif
        <p><a href="{{ url('/') }}">Return to homepage</a></p>
    </div>
</body>
</html>
