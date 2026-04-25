<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm your subscription</title>
</head>
<body>
    <p>Hi {{ $subscriber->name ?? $subscriber->email }},</p>

    <p>Thank you for subscribing! Please confirm your subscription by clicking the link below:</p>

    <p>
        <a href="{{ $confirmationUrl }}">Confirm my subscription</a>
    </p>

    <p>If you didn't subscribe, please ignore this email.</p>

    <p>
        Or copy this link into your browser:<br>
        {{ $confirmationUrl }}
    </p>
</body>
</html>
