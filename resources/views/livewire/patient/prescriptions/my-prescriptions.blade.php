<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Prescriptions</h1>
            <p class="mt-2 text-sm text-gray-600">View and manage your medical prescriptions</p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Dispensed</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['dispensed'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Expired</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="searchTerm"
                                placeholder="Search by prescription number, doctor, or diagnosis..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:w-48">
                        <select 
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Prescriptions</option>
                            <option value="active">Active</option>
                            <option value="dispensed">Dispensed</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions List -->
        <div class="space-y-4">
            @forelse ($prescriptions as $prescription)
                @php
                    $medications = json_decode($prescription->medications, true);
                    $isExpired = \Carbon\Carbon::parse($prescription->valid_until)->isPast();
                    $daysUntilExpiry = \Carbon\Carbon::now()->diffInDays($prescription->valid_until, false);
                @endphp

                <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                            <!-- Prescription Info -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $prescription->prescription_number }}
                                            </h3>
                                            @if ($prescription->is_dispensed)
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                    Dispensed
                                                </span>
                                            @elseif ($isExpired)
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Expired
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-600 mb-1">
                                            Prescribed by: <span class="font-medium text-gray-900">Dr. {{ $prescription->doctor->full_name ?? 'Unknown' }}</span>
                                        </p>

                                        <p class="text-sm text-gray-600">
                                            Date: {{ \Carbon\Carbon::parse($prescription->created_at)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Diagnosis -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Diagnosis:</p>
                                    <p class="text-sm text-gray-600">{{ $prescription->diagnosis }}</p>
                                </div>

                                <!-- Medications Preview -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Medications:</p>
                                    <div class="space-y-2">
                                        @foreach (array_slice($medications, 0, 2) as $medication)
                                            <div class="flex items-start space-x-2 text-sm">
                                                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ $medication['name'] }}</span>
                                                    <span class="text-gray-600"> - {{ $medication['dosage'] }}</span>
                                                    @if (isset($medication['frequency']))
                                                        <span class="text-gray-500"> ({{ $medication['frequency'] }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @if (count($medications) > 2)
                                            <p class="text-sm text-blue-600">+{{ count($medications) - 2 }} more medications</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Validity -->
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-1 {{ $isExpired ? 'text-red-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="{{ $isExpired ? 'text-red-600' : 'text-gray-600' }}">
                                        Valid until: {{ \Carbon\Carbon::parse($prescription->valid_until)->format('M d, Y') }}
                                        @if (!$isExpired && $daysUntilExpiry <= 7)
                                            <span class="text-orange-600 font-medium">({{ $daysUntilExpiry }} days left)</span>
                                        @endif
                                    </span>
                                </div>

                                @if ($prescription->is_dispensed && $prescription->dispensed_at)
                                    <div class="flex items-center text-sm text-gray-600 mt-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Dispensed on: {{ \Carbon\Carbon::parse($prescription->dispensed_at)->format('M d, Y h:i A') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col space-y-2 lg:w-48">
                                <button 
                                    wire:click="viewDetails({{ $prescription->id }})"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                    View Full Details
                                </button>

                                <button 
                                    wire:click="openPrintModal({{ $prescription->id }})"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Print
                                    </span>
                                </button>

                                <button 
                                    wire:click="downloadPrescription({{ $prescription->id }})"
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download PDF
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Prescriptions Found</h3>
                    <p class="text-gray-600">
                        @if ($searchTerm || $statusFilter !== 'all')
                            Try adjusting your filters or search terms.
                        @else
                            You don't have any prescriptions yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- View Details Modal -->
    @if ($showDetailsModal && $selectedPrescription)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeDetailsModal">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Prescription Details</h3>
                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Header Info -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Prescription Number</p>
                                <p class="font-mono font-semibold text-gray-900">{{ $selectedPrescription->prescription_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date Issued</p>
                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedPrescription->created_at)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Valid Until</p>
                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedPrescription->valid_until)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                @if ($selectedPrescription->is_dispensed)
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Dispensed</span>
                                @elseif (\Carbon\Carbon::parse($selectedPrescription->valid_until)->isPast())
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Expired</span>
                                @else
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Info -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Prescribed By</h4>
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-semibold text-blue-600">
                                    {{ substr($selectedPrescription->doctor->first_name ?? 'Dr', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Dr. {{ $selectedPrescription->doctor->first_name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600">{{ $selectedPrescription->doctor->specialization ?? 'General Practitioner' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Diagnosis</h4>
                        <p class="text-gray-700 bg-gray-50 rounded-lg p-4">{{ $selectedPrescription->diagnosis }}</p>
                    </div>

                    <!-- Medications -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Medications</h4>
                        <div class="space-y-3">
                            @foreach (json_decode($selectedPrescription->medications, true) as $index => $medication)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ $medication['name'] }}</p>
                                            <div class="mt-2 space-y-1 text-sm text-gray-600">
                                                <p><span class="font-medium">Dosage:</span> {{ $medication['dosage'] }}</p>
                                                @if (isset($medication['frequency']))
                                                    <p><span class="font-medium">Frequency:</span> {{ $medication['frequency'] }}</p>
                                                @endif
                                                @if (isset($medication['duration']))
                                                    <p><span class="font-medium">Duration:</span> {{ $medication['duration'] }}</p>
                                                @endif
                                                @if (isset($medication['instructions']))
                                                    <p><span class="font-medium">Instructions:</span> {{ $medication['instructions'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- General Instructions -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">General Instructions</h4>
                        <p class="text-gray-700 bg-gray-50 rounded-lg p-4 whitespace-pre-line">{{ $selectedPrescription->instructions }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        wire:click="closeDetailsModal"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Print Modal -->
    @if ($showPrintModal && $selectedPrescription)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white mb-10">
                <div class="flex items-center justify-between mb-6 no-print">
                    <h3 class="text-xl font-bold text-gray-900">Print Prescription</h3>
                    <div class="flex space-x-2">
                        <button 
                            onclick="window.print()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Print
                        </button>
                        <button 
                            wire:click="closePrintModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Close
                        </button>
                    </div>
                </div>

                <!-- Printable Content -->
                <div id="printable-prescription" class="bg-white p-8 print:p-0">
                    <div class="border-2 border-gray-300 rounded-lg p-8">
                        <!-- Header -->
                        <div class="text-center mb-8 border-b-2 border-gray-300 pb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Medical Prescription</h1>
                            <p class="text-gray-600">{{ $selectedPrescription->prescription_number }}</p>
                        </div>

                        <!-- Patient & Doctor Info -->
                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Patient Information</h3>
                                <p class="text-gray-700">{{ $selectedPrescription->patient->first_name?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $selectedPrescription->patient->email ?? '' }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Prescribed By</h3>
                                <p class="text-gray-700">Dr. {{ $selectedPrescription->doctor->first_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $selectedPrescription->doctor->specialization ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Date Info -->
                        <div class="mb-8">
                            <p class="text-sm text-gray-600">Date Issued: {{ \Carbon\Carbon::parse($selectedPrescription->created_at)->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-600">Valid Until: {{ \Carbon\Carbon::parse($selectedPrescription->valid_until)->format('F d, Y') }}</p>
                        </div>

                        <!-- Diagnosis -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-2">Diagnosis:</h3>
                            <p class="text-gray-700">{{ $selectedPrescription->diagnosis }}</p>
                        </div>

                        <!-- Medications -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Medications:</h3>
                            @foreach (json_decode($selectedPrescription->medications, true) as $index => $medication)
                                <div class="mb-4 pl-4 border-l-4 border-blue-500">
                                    <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ $medication['name'] }}</p>
                                    <p class="text-sm text-gray-700">Dosage: {{ $medication['dosage'] }}</p>
                                    @if (isset($medication['frequency']))
                                        <p class="text-sm text-gray-700">Frequency: {{ $medication['frequency'] }}</p>
                                    @endif
                                    @if (isset($medication['duration']))
                                        <p class="text-sm text-gray-700">Duration: {{ $medication['duration'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Instructions -->
                        <div class="mb-8">
                            <h3 class="font-semibold text-gray-900 mb-2">Instructions:</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $selectedPrescription->instructions }}</p>
                        </div>

                        <!-- Footer -->
                        <div class="border-t-2 border-gray-300 pt-6 mt-8">
                            <div class="text-center">
                                <p class="text-sm text-gray-600">This prescription is valid until {{ \Carbon\Carbon::parse($selectedPrescription->valid_until)->format('F d, Y') }}</p>
                                <p class="text-xs text-gray-500 mt-2">For any questions, please contact your healthcare provider</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Styles -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                #printable-prescription,
                #printable-prescription * {
                    visibility: visible;
                }
                #printable-prescription {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endif

    @script
    <script>
        $wire.on('print-prescription', () => {
            window.print();
        });
    </script>
    @endscript
</div>