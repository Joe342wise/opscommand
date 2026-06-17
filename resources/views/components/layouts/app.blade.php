<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'OpsCommand') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .fill-icon {
            font-variation-settings: 'FILL' 1;
        }
        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #0b1326;
        }
        ::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md overflow-hidden selection:bg-primary/30">
    @include('layouts.sidebar')

    @include('layouts.topbar')

    <main class="ml-64 px-margin-page pt-8 pb-margin-page h-[calc(100vh-3.5rem)] overflow-y-auto">
        <div class="max-w-[1600px] mx-auto space-y-margin-page">
            {{ $slot }}
        </div>
    </main>

    <!-- Background Atmospheric Effect -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-danger-rose/5 blur-[120px] rounded-full translate-y-1/2 -translate-x-1/4"></div>
    </div>

    @livewireScripts
</body>
</html>
