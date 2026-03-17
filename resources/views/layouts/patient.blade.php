<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Patient Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- x-cloak hides Alpine elements before Alpine boots to prevent flash --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">

        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('patient.dashboard') }}" class="flex items-center">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-xl shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-xl font-bold text-gray-900">Health Care</span>
                        </a>
                    </div>

                    <!-- Navigation Links - Desktop -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('patient.dashboard') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition {{ request()->routeIs('patient.dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('patient.appointments') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition {{ request()->routeIs('patient.appointments', 'patient.my-appointments') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Appointments
                        </a>
                        <a href="{{ route('patient.prescriptions') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition {{ request()->routeIs('patient.prescriptions') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Prescriptions
                        </a>
                        <a href="{{ route('patient.medical-history') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition {{ request()->routeIs('patient.medical-history') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Medical History
                        </a>
                    </div>

                    <!-- User Menu - Desktop -->
                    <div class="hidden md:flex items-center space-x-4">

                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center space-x-3 p-2 hover:bg-gray-100 rounded-lg transition">
                                <img src="{{ Auth::user()->patient->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->patient->first_name ?? 'U').'&background=3b82f6&color=fff' }}"
                                     alt="{{ Auth::user()->patient->first_name ?? 'User' }}"
                                     class="w-8 h-8 rounded-full object-cover">
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ Auth::user()->patient->first_name ?? '' }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->patient->last_name ?? '' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open"
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                <a href="{{ route('patient.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>
                                <a href="{{ route('patient.medical-history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Medical Records
                                </a>
                                <a href="{{ route('patient.payments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Payments
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div x-data="{ mobileMenuOpen: false }" class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900 p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Mobile Menu (scoped inside same x-data) -->
                        <div x-show="mobileMenuOpen"
                             x-cloak
                             @click.away="mobileMenuOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute top-16 left-0 right-0 bg-white border-t border-gray-200 shadow-md z-40 md:hidden">
                            <div class="px-2 pt-2 pb-3 space-y-1">
                                <a href="{{ route('patient.dashboard') }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('patient.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('patient.appointments') }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('patient.appointments') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                                    Appointments
                                </a>
                                <a href="{{ route('patient.prescriptions') }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('patient.prescriptions') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                                    Prescriptions
                                </a>
                                <a href="{{ route('patient.medical-history') }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('patient.medical-history') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                                    Medical History
                                </a>
                                <a href="{{ route('patient.profile') }}"
                                   class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(isset($header))
                    <header class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $header }}</h2>
                    </header>
                @endif

                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} HealthCare. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    {{-- Chat Widget --}}
    @livewire(\App\Livewire\ChatWidget::class)

    
    @livewireScripts

</body>
</html>
