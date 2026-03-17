<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livewire Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Livewire Test Page</h1>
        
        {{-- Simple Counter Test --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">1. Simple Counter Test</h2>
            @livewire('counter-test')
        </div>
        
        {{-- Chat Widget Test --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">2. Chat Widget Test</h2>
            <p class="text-sm text-gray-600 mb-4">Look for the chat button in bottom-right corner</p>
            @livewire('chat-widget')
        </div>
        
        {{-- Instructions --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6">
            <h3 class="font-semibold text-blue-900 mb-2">Testing Instructions:</h3>
            <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                <li>Counter should increment when you click the button</li>
                <li>Chat button should appear in bottom-right</li>
                <li>Clicking chat should open the window (not refresh page)</li>
                <li>Open browser console (F12) and check for errors</li>
            </ol>
        </div>
    </div>
    
    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>