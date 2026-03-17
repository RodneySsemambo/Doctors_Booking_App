<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:leave.duration.300ms>
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
                    <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage and track all your appointments</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <div class="relative">
                        <input 
                            type="date" 
                            wire:model.live="dateFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
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
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('appointment_date')">
                                <div class="flex items-center">
                                    Date & Time
                                    @if($sortBy === 'appointment_date')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                <div class="flex items-center">
                                    Status
                                    @if($sortBy === 'status')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                <div class="flex items-center">
                                    Created
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-bold">
                                                    {{ substr($appointment->patient->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->patient->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $appointment->service ?? 'Consultation' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="viewDetails({{ $appointment->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            View
                                        </button>
                                        @if($appointment->status === 'pending')
                                            <button wire:click="confirmAppointment({{ $appointment->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                Confirm
                                            </button>
                                        @endif
                                        @if($appointment->status === 'confirmed')
                                            <button wire:click="completeAppointment({{ $appointment->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                Complete
                                            </button>
                                        @endif
                                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                                            <button wire:click="cancelAppointment({{ $appointment->id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                Cancel
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-4 text-gray-500">No appointments found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($appointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>

        <!-- Appointment Details Modal -->
        @if($showModal && $selectedAppointment)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
                <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10" wire:click.stop>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Appointment Details</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Patient Info -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $selectedAppointment->patient->first_name }} {{ $selectedAppointment->patient->last_name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-medium text-gray-900">{{ $selectedAppointment->patient->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <p class="font-medium text-gray-900">{{ $selectedAppointment->patient->user->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date of Birth</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $selectedAppointment->patient->date_of_birth ? \Carbon\Carbon::parse($selectedAppointment->patient->date_of_birth)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($selectedAppointment->appointment_date)->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Time</p>
                                    <p class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($selectedAppointment->appointment_time)->format('h:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($selectedAppointment->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($selectedAppointment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($selectedAppointment->status === 'completed') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($selectedAppointment->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Service</p>
                                    <p class="font-medium text-gray-900">{{ $selectedAppointment->service ?? 'Consultation' }}</p>
                                </div>
                            </div>

                            @if($selectedAppointment->reason)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">Reason for Visit</p>
                                    <p class="mt-1 text-gray-900">{{ $selectedAppointment->reason }}</p>
                                </div>
                            @endif

                            @if($selectedAppointment->notes)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">Doctor's Notes</p>
                                    <p class="mt-1 text-gray-900">{{ $selectedAppointment->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Payment Information -->
                        @if($selectedAppointment->payments->count() > 0)
                            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h4>
                                <div class="space-y-3">
                                    @foreach($selectedAppointment->payments as $payment)
                                        <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $payment->payment_number }}</p>
                                                <p class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-green-600">UGX {{ number_format($payment->amount) }}</p>
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        @if($selectedAppointment->status === 'pending')
                            <button wire:click="confirmAppointment({{ $selectedAppointment->id }})" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Confirm Appointment
                            </button>
                        @endif
                        @if($selectedAppointment->status === 'confirmed')
                            <button wire:click="completeAppointment({{ $selectedAppointment->id }})" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Mark as Complete
                            </button>
                        @endif
                        @if(in_array($selectedAppointment->status, ['pending', 'confirmed']))
                            <button wire:click="cancelAppointment({{ $selectedAppointment->id }})" 
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Cancel Appointment
                            </button>
                        @endif
                        <button wire:click="closeModal" 
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-down {
        animation: fade-in-down 0.3s ease-out;
    }
</style>
</div>

