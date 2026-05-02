<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEFAS Analytics - @yield('title', 'Hoşgeldiniz')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E1E7EF] text-text-main antialiased min-h-screen flex flex-col items-center justify-center p-4">
    @yield('content')
</body>
</html>