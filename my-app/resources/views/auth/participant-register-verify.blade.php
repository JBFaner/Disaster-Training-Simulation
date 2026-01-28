<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Verify Registration</title>
        @vite(['resources/css/app.css', 'resources/js/auth.jsx'])
    </head>
    <body>
        <div
            id="participant-register-verify-root"
            data-verification-method="{{ $verification_method ?? 'email' }}"
            data-contact="{{ $contact ?? '' }}"
            data-errors='@json($errors->getMessages() ?: [])'
        ></div>
    </body>
</html>

