<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Confirmed</title>
</head>
<body>
    <div>
        <h1>You're subscribed!</h1>
        <p>Thank you, {{ $subscriber->name ?? $subscriber->email }}. Your subscription has been confirmed.</p>
        <p><a href="{{ url('/') }}">Return to homepage</a></p>
    </div>
</body>
</html>
