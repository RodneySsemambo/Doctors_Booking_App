<div class="container mx-auto px-4 py-8">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Welcome back, Dr. {{ $doctor->first_name }} {{$doctor->last_name}}!</h1>
            <p class="text-gray-600 mt-2">Here's your practice overview for today.</p>
        </div>
       
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Today's Appointments -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Today's Appointments</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['today_appointments'] }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Patients</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_patients'] }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Month Earnings -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">This Month Earnings</p>
                    <p class="text-3xl font-bold mt-2">UGX {{ number_format($stats['this_month_earnings']) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Upcoming</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['upcoming_appointments'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Appointments -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Appointments</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_appointments'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Earnings</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">UGX {{ number_format($stats['total_earnings']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Today's Appointments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Today's Schedule -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Today's Schedule</h2>
                    <p class="text-blue-100 text-sm">{{ now()->format('l, F d, Y') }}</p>
                </div>
                
                <div class="p-6">
                    @forelse($todayAppointments as $appointment)
                    <div class="flex items-start space-x-4 p-4 hover:bg-gray-50 rounded-lg transition mb-4 border border-gray-200">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-lg">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                </p>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $appointment->patient->user->email }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $appointment->patient->user->phone }}
                            </p>
                            @if($appointment->reason)
                            <p class="text-sm text-gray-500 mt-2 italic">
                                "{{ Str::limit($appointment->reason, 100) }}"
                            </p>
                            @endif
                            
                            <div class="flex space-x-2 mt-3">
                                @if($appointment->status === 'pending')
                                <button wire:click="confirmAppointment({{ $appointment->id }})" 
                                        class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                    Confirm
                                </button>
                                @endif
                                
                                @if($appointment->status === 'confirmed')
                                <button wire:click="completeAppointment({{ $appointment->id }})" 
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                    Mark Complete
                                </button>
                                @endif
                                
                                <a href="{{ route('doctor.appointments') }}" 
                                   class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 text-lg">No appointments scheduled for today</p>
                        <p class="text-gray-400 text-sm">Enjoy your day off!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Upcoming Appointments</h2>
                        <a href="{{ route('doctor.appointments') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            View All →
                        </a>
                    </div>
                </div>
                
                <div class="p-6">
                    @forelse($upcomingAppointments as $appointment)
                    <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition {{ !$loop->last ? 'border-b border-gray-100 mb-3' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                            </p>
                            <p class="text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }} at 
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-8">No upcoming appointments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column - Notifications & Stats -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Notifications -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
                            @if($unreadNotificationsCount > 0)
                            <span class="ml-2 px-2 py-1 text-xs font-semibold bg-red-500 text-white rounded-full">
                                {{ $unreadNotificationsCount }}
                            </span>
                            @endif
                        </div>
                        @if($unreadNotificationsCount > 0)
                        <button wire:click="markAllNotificationsAsRead" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">
                            Mark all read
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="p-6 max-h-96 overflow-y-auto">
                    @forelse($recentNotifications as $notification)
                    <div wire:click="markNotificationAsRead({{ $notification->id }})" 
                         class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition {{ !$loop->last ? 'border-b border-gray-100 mb-3' : '' }} {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                @if($notification->type === 'payment_confirmation') bg-green-100
                                @elseif($notification->type === 'appointment_confirmation') bg-blue-100
                                @elseif($notification->type === 'refund') bg-red-100
                                @else bg-gray-100
                                @endif">
                                <svg class="w-4 h-4 
                                    @if($notification->type === 'payment_confirmation') text-green-600
@elseif($notification->type === 'appointment_confirmation') text-blue-600
@elseif($notification->type === 'refund') text-red-600
@else text-gray-600
@endif"
fill="currentColor" viewBox="0 0 20 20">
<path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
</svg>
</div>
</div>
<div class="flex-1 min-w-0">
<p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
<p class="text-xs text-gray-600 mt-1">{{ Str::limit($notification->message, 80) }}</p>
<p class="text-xs text-gray-400 mt-1">{{ $notification->sent_at->diffForHumans() }}</p>
</div>
@if(is_null($notification->read_at))
<div class="flex-shrink-0">
<div class="w-2 h-2 bg-blue-600 rounded-full"></div>
</div>
@endif
</div>
@empty
<div class="text-center py-8">
<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
</svg>
<p class="mt-2 text-gray-500 text-sm">No notifications</p>
</div>
@endforelse
</div>
</div>
        <!-- Appointment Statistics -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Appointment Statistics</h3>
            <div class="space-y-3">
                @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $status => $label)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $appointmentStats[$status] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
