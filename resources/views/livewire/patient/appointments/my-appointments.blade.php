<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Appointments</h1>
            <p class="mt-2 text-sm text-gray-600">Manage your upcoming and past appointments</p>
        </div>

        {{-- Flash messages --}}
        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabs + Filters --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button type="button"
                            wire:click="switchTab('upcoming')"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors
                                   {{ $activeTab === 'upcoming'
                                       ? 'border-blue-500 text-blue-600'
                                       : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Upcoming
                            <span class="bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $upcomingAppointments->count() }}
                            </span>
                        </span>
                    </button>

                    <button type="button"
                            wire:click="switchTab('history')"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-colors
                                   {{ $activeTab === 'history'
                                       ? 'border-blue-500 text-blue-600'
                                       : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            History
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $appointmentHistory->count() }}
                            </span>
                        </span>
                    </button>
                </nav>
            </div>

            {{-- Filters --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text"
                               wire:model.live.debounce.300ms="searchTerm"
                               placeholder="Search by doctor name or appointment number..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg
                                      focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="sm:w-48">
                        <select wire:model.live="statusFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg
                                       focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
             UPCOMING TAB
        ================================================================ --}}
        @if($activeTab === 'upcoming')
            <div class="space-y-4">
                @forelse($upcomingAppointments as $appointment)
                    @php
                        $doc = $appointment->doctor;
                        $doctorName = 'Doctor';
                        if ($doc) {
                            try { if (!empty($doc->full_name)) $doctorName = $doc->full_name; }
                            catch(\Throwable $e) {}
                            if ($doctorName === 'Doctor' && !empty($doc->first_name))
                                $doctorName = trim($doc->first_name . ' ' . ($doc->last_name ?? ''));
                            if ($doctorName === 'Doctor' && !empty($doc->name))
                                $doctorName = $doc->name;
                        }
                        $initial = strtoupper(substr($doctorName, 0, 1));

                        $specName = $doc?->specialization?->name ?? 'General Practitioner';

                        // ── Status badge ──────────────────────────────────
                        $badge = match($appointment->status) {
                            'confirmed' => 'bg-green-100 text-green-800',
                            'pending'   => 'bg-yellow-100 text-yellow-800',
                            default     => 'bg-gray-100 text-gray-700',
                        };
                    @endphp

                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                {{-- Info --}}
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xl font-semibold text-blue-600">{{ $initial }}</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900">Dr. {{ $doctorName }}</h3>
                                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badge }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3">{{ $specName }}</p>

                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d, Y') }}
                                            </span>

                                            @if($appointment->appointment_time)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                                </span>
                                            @endif

                                            @if($appointment->hospital)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $appointment->hospital->name }}
                                                </span>
                                            @endif

                                            @if($appointment->appointment_type)
                                                <span class="capitalize text-gray-500">
                                                    {{ str_replace('-', ' ', $appointment->appointment_type) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 text-sm">
                                            <span class="text-gray-500">Appointment #:</span>
                                            <span class="font-mono text-gray-900 ml-1">
                                                {{ $appointment->appointment_number ?? '—' }}
                                            </span>
                                        </div>

                                        @if($appointment->reason_for_visit)
                                            <p class="mt-2 text-sm text-gray-600">
                                                <span class="font-medium">Reason:</span>
                                                {{ $appointment->reason_for_visit }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-col gap-2 lg:min-w-[160px]">
                                 

                                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                                        <button type="button"
                                                wire:click="openCancelModal({{ $appointment->id }})"
                                                class="px-4 py-2 bg-white border border-red-300 text-red-600 rounded-lg
                                                       hover:bg-red-50 transition-colors text-sm font-medium">
                                            Cancel Appointment
                                        </button>
                                    @endif

                                   
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Upcoming Appointments</h3>
                        <p class="text-gray-600 mb-6">You don't have any upcoming appointments scheduled.</p>
                        <a href="{{ route('patient.book-appointment') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Book New Appointment
                        </a>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- ================================================================
             HISTORY TAB
        ================================================================ --}}
        @if($activeTab === 'history')
            <div class="space-y-4">
                @forelse($appointmentHistory as $appointment)
                    @php
                        $doc = $appointment->doctor;
                        $doctorName = 'Doctor';
                        if ($doc) {
                            try { if (!empty($doc->full_name)) $doctorName = $doc->full_name; }
                            catch(\Throwable $e) {}
                            if ($doctorName === 'Doctor' && !empty($doc->first_name))
                                $doctorName = trim($doc->first_name . ' ' . ($doc->last_name ?? ''));
                            if ($doctorName === 'Doctor' && !empty($doc->name))
                                $doctorName = $doc->name;
                        }
                        $initial  = strtoupper(substr($doctorName, 0, 1));
                        $specName = $doc?->specialization?->name ?? 'General Practitioner';

                        $badge = match(true) {
                            in_array($appointment->status, ['completed', 'compeleted'])
                                => 'bg-green-100 text-green-800',
                            $appointment->status === 'cancelled'
                                => 'bg-red-100 text-red-800',
                            $appointment->status === 'no_show'
                                => 'bg-orange-100 text-orange-800',
                            default
                                => 'bg-gray-100 text-gray-700',
                        };

                        $statusLabel = match($appointment->status) {
                            'compeleted' => 'Completed',
                            default      => ucfirst($appointment->status),
                        };
                    @endphp

                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                <div class="flex items-start gap-4 flex-1">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xl font-semibold text-gray-600">{{ $initial }}</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900">Dr. {{ $doctorName }}</h3>
                                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badge }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3">{{ $specName }}</p>

                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d, Y') }}
                                            </span>

                                            @if($appointment->appointment_time)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3 text-sm">
                                            <span class="text-gray-500">Appointment #:</span>
                                            <span class="font-mono text-gray-900 ml-1">
                                                {{ $appointment->appointment_number ?? '—' }}
                                            </span>
                                        </div>

                                        @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                                            <p class="mt-2 text-sm text-red-600">
                                                <span class="font-medium">Cancellation Reason:</span>
                                                {{ $appointment->cancellation_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 lg:min-w-[140px]">
                                    @if(in_array($appointment->status, ['completed', 'compeleted']) && !$appointment->review)
                                        <button type="button"
                                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg
                                                       transition-colors text-sm font-medium">
                                            Leave Review
                                        </button>
                                    @endif

                                    <button type="button"
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg
                                                   hover:bg-gray-50 transition-colors text-sm font-medium text-center">
                                        View Details
                                    </button>

                                    @if(in_array($appointment->status, ['completed', 'compeleted']))
                                        <a href="{{ route('patient.book-appointment') }}"
                                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg
                                                  transition-colors text-sm font-medium text-center">
                                            Book Again
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Appointment History</h3>
                        <p class="text-gray-600">You don't have any past appointments yet.</p>
                    </div>
                @endforelse
            </div>
        @endif

    </div>

    {{-- ================================================================
         Cancel Modal
    ================================================================ --}}
    @if($showCancelModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
             wire:click="closeCancelModal">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white"
                 wire:click.stop>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Cancel Appointment</h3>
                        <button type="button" wire:click="closeCancelModal"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        Please provide a reason for cancelling this appointment.
                        This helps us improve our service.
                    </p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Cancellation <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="cancellationReason"
                                  rows="4"
                                  placeholder="Please explain why you need to cancel..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none
                                         focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        @error('cancellationReason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                wire:click="closeCancelModal"
                                class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                            Keep Appointment
                        </button>
                        <button type="button"
                                wire:click="cancelAppointment"
                                wire:loading.attr="disabled"
                                wire:target="cancelAppointment"
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg
                                       transition-colors disabled:opacity-60">
                            <span wire:loading.remove wire:target="cancelAppointment">Cancel Appointment</span>
                            <span wire:loading       wire:target="cancelAppointment">Cancelling…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>