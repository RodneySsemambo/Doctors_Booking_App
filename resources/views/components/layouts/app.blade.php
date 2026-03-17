<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    @vite('resources/css/app.css') <!-- if using Vite + Tailwind -->
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
