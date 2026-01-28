<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Participant Login</title>
        @vite(['resources/css/app.css', 'resources/js/auth.jsx'])
    </head>
    <body>
        <div
            id="participant-login-root"
            data-errors='@json($errors->all() ?: [])'
            data-old-email="{{ old('email', '') }}"
        ></div>
    </body>
</html>
