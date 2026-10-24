@props(['title' => null, 'description' => 'Marketplace katering untuk kantor: cari katering, pesan makan siang tim, dan kelola invoice dalam satu tempat.'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%2318181b'/%3E%3Ctext x='16' y='22' font-family='system-ui, sans-serif' font-size='18' font-weight='700' fill='white' text-anchor='middle'%3EK%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body {{ $attributes }}>
    {{ $slot }}
</body>
</html>
