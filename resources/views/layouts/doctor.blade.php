<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Doctor Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .shadow-soft {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }
        
        .shadow-sidebar {
            box-shadow: 4px 0 20px -5px rgba(0, 0, 0, 0.05);
        }
        
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .active-glow {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-data="{ mobileMenuOpen: false }" class="lg:hidden">
            <!-- Overlay -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75"
                 @click="mobileMenuOpen = false">
            </div>
            
            <!-- Mobile Sidebar -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 z-50 w-72">
                <!-- Mobile sidebar content goes here -->
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <div class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow sidebar-gradient border-r border-gray-200/50 shadow-sidebar overflow-y-auto custom-scrollbar">
                
                <!-- Logo Section -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200/50">
                    <a href="{{ route('doctor.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gradient">HealthCare</h1>
                            <p class="text-xs text-gray-500 font-medium">Doctor Portal</p>
                        </div>
                    </a>
                </div>
                
                <!-- Doctor Info Card -->
                <div class="px-6 py-5 border-b border-gray-200/50">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                <span class="text-white font-bold text-lg">
                                    {{ substr(Auth::user()->first_name ?? 'D', 0, 1) }}
                                </span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-green-500 border-2 border-white shadow-sm"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">Dr. {{ Auth::user()->first_name ?? '' }}</h3>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium">
                                    {{ $doctor->specialization ?? 'General Practitioner' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation (rest of your nav stays the same) -->
                <!-- ... -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:pl-72 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <div class="glass-effect border-b border-gray-200/50">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Left side: Mobile menu + Title -->
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                    class="lg:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            
                            <!-- Breadcrumb & Title -->
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <h1 class="text-lg font-semibold text-gray-900">
                                        @yield('title', 'Dashboard')
                                    </h1>
                                    @hasSection('subtitle')
                                    <span class="mx-2 text-gray-400">/</span>
                                    <span class="text-sm text-gray-600">@yield('subtitle')</span>
                                    @endif
                                </div>
                                @hasSection('description')
                                <p class="text-xs text-gray-500 mt-1">@yield('description')</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side content stays the same -->
                        <!-- ... -->
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>