<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Patients</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage and track all your patients</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Patients -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Patients</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($statistics['total']) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0a6 6 0 00-9 5.197"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Patients -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Patients</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($statistics['active']) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-green-50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- New Patients -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">New Patients (30 days)</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($statistics['new']) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inactive Patients -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inactive Patients</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($statistics['inactive']) }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-gray-50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search by patient name, email, or phone..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select 
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Patients</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Per Page -->
                    <div>
                        <select 
                            wire:model.live="perPage"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('first_name')">
                                <div class="flex items-center">
                                    Patient
                                    @if($sortBy === 'first_name')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact Info
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                <div class="flex items-center">
                                    First Visit
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Appointments
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($patients as $patient)
                            @php
                                $patientAppointments = $patient->appointments->where('doctor_id', $doctor->id);
                                $lastAppointment = $patientAppointments->sortByDesc('appointment_date')->first();
                                $status = $lastAppointment && $lastAppointment->appointment_date >= now()->subMonths(1) ? 'active' : 'inactive';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-bold text-sm">
                                                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $patient->first_name }} {{ $patient->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($patient->date_of_birth)
                                                    {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years
                                                @else
                                                    Age not specified
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $patient->user->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $patient->user->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patientAppointments->count() > 0)
                                        <div class="text-sm text-gray-900">
                                            {{ $patientAppointments->sortBy('appointment_date')->first()->appointment_date }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $patientAppointments->count() }} visits
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No visits yet</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">
                                        {{ $patientAppointments->where('status', 'completed')->count() }} completed
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $patientAppointments->where('status', 'pending')->count() }} pending
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($status === 'active') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="viewPatient({{ $patient->id }})" 
                                            class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0a6 6 0 00-9 5.197"/>
                                    </svg>
                                    <p class="mt-4 text-gray-500">No patients found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>

        <!-- Patient Details Modal -->
        @if($showPatientModal && $selectedPatient)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
                <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white mb-10" wire:click.stop>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-xl">
                                    {{ substr($selectedPatient->first_name, 0, 1) }}{{ substr($selectedPatient->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</h3>
                                <p class="text-sm text-gray-600">Patient ID: #{{ str_pad($selectedPatient->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Patient Information -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Basic Info -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Full Name</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Date of Birth</p>
                                        <p class="font-medium text-gray-900">
                                            @if($selectedPatient->date_of_birth)
                                                {{ \Carbon\Carbon::parse($selectedPatient->date_of_birth)->format('M d, Y') }}
                                                ({{ \Carbon\Carbon::parse($selectedPatient->date_of_birth)->age }} years)
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email Address</p>
                                        <p class="font-medium text-gray-900">{{ $selectedPatient->user->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Phone Number</p>
                                        <p class="font-medium text-gray-900">{{ $selectedPatient->user->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Gender</p>
                                        <p class="font-medium text-gray-900">{{ $selectedPatient->gender ?? 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Blood Type</p>
                                        <p class="font-medium text-gray-900">{{ $selectedPatient->blood_type ?? 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical History -->
                            @if($selectedPatient->medical_history)
                                <div class="bg-white rounded-lg p-6 border border-gray-200">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Medical History</h4>
                                    <div class="text-gray-700">
                                        {{ $selectedPatient->medical_history }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Statistics & Quick Actions -->
                        <div class="space-y-6">
                            <!-- Statistics -->
                            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Patient Statistics</h4>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-blue-700">Total Appointments</p>
                                        </div>
                                        <span class="text-xl font-bold text-blue-700">{{ $patientStats['total_appointments'] ?? 0 }}</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-green-50 rounded-lg">
                                            <p class="text-xs text-green-600">Completed</p>
                                            <p class="text-lg font-bold text-green-700">{{ $patientStats['completed_appointments'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-yellow-50 rounded-lg">
                                            <p class="text-xs text-yellow-600">Pending</p>
                                            <p class="text-lg font-bold text-yellow-700">{{ $patientStats['pending_appointments'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-red-50 rounded-lg">
                                            <p class="text-xs text-red-600">Cancelled</p>
                                            <p class="text-lg font-bold text-red-700">{{ $patientStats['cancelled_appointments'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-purple-50 rounded-lg">
                                            <p class="text-xs text-purple-600">Total Spent</p>
                                            <p class="text-lg font-bold text-purple-700">UGX {{ number_format($patientStats['total_spent'] ?? 0) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Appointment -->
                            @if($patientStats['last_appointment'] ?? false)
                                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Last Appointment</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Date</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $patientStats['last_appointment']->appointment_date }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Time</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($patientStats['last_appointment']->appointment_time)->format('h:i A') }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Status</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($patientStats['last_appointment']->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($patientStats['last_appointment']->status === 'confirmed') bg-blue-100 text-blue-800
                                                    @elseif($patientStats['last_appointment']->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($patientStats['last_appointment']->status) }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                           
                        </div>
                    </div>

                    <!-- Recent Appointments -->
                    @if($selectedPatient->appointments->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Recent Appointments</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedPatient->appointments->take(5) as $appointment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">{{ $appointment->appointment_date }}</td>
                                                <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $appointment->service ?? 'Consultation' }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                        @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @php
                                                        $payment = $appointment->payments->where('status', 'completed')->first();
                                                    @endphp
                                                    @if($payment)
                                                        UGX {{ number_format($payment->amount) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button wire:click="closeModal" 
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>