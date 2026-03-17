<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical History</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage your medical records</p>
                </div>
                <button 
                    wire:click="openUploadModal"
                    class="mt-4 sm:mt-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Record
                    </span>
                </button>
            </div>
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
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Records</p>
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
                        <p class="text-sm font-medium text-gray-600">Lab Results</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['lab_results'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Consultation Notes</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['consultation_notes'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Vaccinations</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['vaccinations'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Imaging</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['imaging'] }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & Filters Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Timeline (1/3 width on large screens) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        @forelse ($timeline as $record)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-8 h-8 rounded-full {{ $record['type_color'] }} flex items-center justify-center">
                                        @if($record['type_icon'] === 'flask')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                            </svg>
                                        @elseif($record['type_icon'] === 'file-text')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @elseif($record['type_icon'] === 'shield')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        @elseif($record['type_icon'] === 'image')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $record['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $record['formatted_date'] }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ Str::limit($record['description'], 60) }}</p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full {{ $record['type_color'] }}">
                                        {{ $record['type_label'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500 text-sm">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Filters & Records (2/3 width on large screens) -->
            <div class="lg:col-span-2">
                <!-- Filters Card -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="searchTerm"
                                        placeholder="Search records..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Record Type Filter -->
                            <div>
                                <select 
                                    wire:model.live="recordTypeFilter"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all">All Types</option>
                                    @foreach($recordTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <button 
                                    wire:click="$set('dateFrom', ''); $set('dateTo', '');"
                                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                    Clear Dates
                                </button>
                            </div>
                        </div>

                        <!-- Date Range Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input 
                                    type="date" 
                                    wire:model.live="dateFrom"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input 
                                    type="date" 
                                    wire:model.live="dateTo"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Records List -->
                <div class="space-y-4">
                    @forelse ($medicalRecords as $record)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                                    <!-- Record Info -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        {{ $record->title }}
                                                    </h3>
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $record->record_type_color }}">
                                                        {{ $record->record_type_label }}
                                                    </span>
                                                </div>

                                                <p class="text-sm text-gray-600 mb-1">
                                                    Recorded by: <span class="font-medium text-gray-900">{{ $record->recordedBy->name ?? 'Unknown' }}</span>
                                                </p>

                                                <p class="text-sm text-gray-600">
                                                    Date: {{ $record->recorded_date->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">{{ Str::limit($record->description, 200) }}</p>
                                        </div>

                                        <!-- File Info -->
                                        @if($record->file_path)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>File attached ({{ $record->file_type }})</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col space-y-2 lg:w-48">
                                        <button 
                                            wire:click="viewRecord({{ $record->id }})"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            View Details
                                        </button>

                                        @if($record->file_path)
                                            <button 
                                                wire:click="downloadFile({{ $record->id }})"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Download File
                                                </span>
                                            </button>
                                        @endif

                                        <button 
                                            wire:click="deleteRecord({{ $record->id }})"
                                            wire:confirm="Are you sure you want to delete this medical record?"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                            Delete Record
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Medical Records Found</h3>
                            <p class="text-gray-600">
                                @if ($searchTerm || $recordTypeFilter !== 'all' || $dateFrom || $dateTo)
                                    Try adjusting your filters or search terms.
                                @else
                                    You don't have any medical records yet.
                                @endif
                            </p>
                            <button 
                                wire:click="openUploadModal"
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Upload Your First Record
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    @if ($showViewModal && $selectedRecord)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeViewModal">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Medical Record Details</h3>
                    <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
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
                                <p class="text-sm text-gray-600">Title</p>
                                <p class="font-semibold text-gray-900">{{ $selectedRecord->title }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Type</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $selectedRecord->record_type_color }}">
                                    {{ $selectedRecord->record_type_label }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Recorded Date</p>
                                <p class="font-semibold text-gray-900">{{ $selectedRecord->recorded_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Recorded By</p>
                                <p class="font-semibold text-gray-900">{{ $selectedRecord->recordedBy->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Description</h4>
                        <p class="text-gray-700 bg-gray-50 rounded-lg p-4 whitespace-pre-line">{{ $selectedRecord->description }}</p>
                    </div>

                    <!-- File Info -->
                    @if($selectedRecord->file_path)
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Attached File</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-gray-900">File attached</p>
                                            <p class="text-sm text-gray-600">Type: {{ $selectedRecord->file_type }}</p>
                                            @if($selectedRecord->file_size)
                                                <p class="text-sm text-gray-600">Size: {{ $selectedRecord->file_size }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <button 
                                        wire:click="downloadFile({{ $selectedRecord->id }})"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Related Appointment -->
                    @if($selectedRecord->appointment)
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Related Appointment</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">
                                    Appointment on {{ $selectedRecord->appointment->appointment_date->format('M d, Y') }}
                                    at {{ $selectedRecord->appointment->appointment_time }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    With Dr. {{ $selectedRecord->appointment->doctor->full_name ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        wire:click="closeViewModal"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Modal -->
    @if ($showUploadModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeUploadModal">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white mb-10" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Upload Medical Record</h3>
                    <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="uploadRecord">
                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="uploadTitle"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter record title">
                            @error('uploadTitle') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                wire:model="uploadDescription"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter record description"></textarea>
                            @error('uploadDescription') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Record Type & Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Record Type <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model="uploadRecordType"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach($recordTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('uploadRecordType') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Record Date <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    wire:model="uploadRecordDate"
                                    max="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('uploadRecordDate') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                File (Optional)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload a file</span>
                                            <input 
                                                id="file-upload" 
                                                type="file" 
                                                wire:model="uploadFile"
                                                class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, JPG, PNG up to 10MB
                                    </p>
                                    @if($uploadFile)
                                        <p class="text-sm text-green-600">
                                            Selected: {{ $uploadFile->getClientOriginalName() }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @error('uploadFile') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            wire:click="closeUploadModal"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Upload Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>