<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Address Confirmation</title>
    <link rel="stylesheet" type="text/css" href="{{ url('css/app.css') }}">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-flow: column wrap;
            row-gap: 0.5em;

            margin: 1em;
            font-family: serif;
        }

        a {
            background-color: #2665c3;
            padding: 0.5em 1em;
            border-radius: 0.5em;
            color: white;
            text-decoration: none;
        }

        a:hover {
            background-color: #0250C4;
        }
    </style>
</head>

<body>
    <h2>Welcome, {{ $user->name }}!</h2>
    <div>Thank you for signing up for {{ env('APP_NAME') }}</div>
    <div>Verify your email address by clicking the button below.</div>
    <a href="{{ route('verify-email', ['code' => $user->email_verification_code]) }}">Confirm My Account</a>
    <div>If you didn't request this, then please ignore this email.</div>
</body>

</html>
