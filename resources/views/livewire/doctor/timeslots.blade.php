
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
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
                    <h1 class="text-3xl font-bold text-gray-900">Manage Your Schedule</h1>
                    <p class="mt-2 text-sm text-gray-600">Set your available time slots for appointments</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ $editingId ? 'Edit Timeslot' : 'Add New Timeslot' }}
                    </h2>
                    
                    <form wire:submit.prevent="saveTimeslot">
                        <!-- Day of Week -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
                            <select wire:model="day_of_week" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($days as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Time -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                            <input type="time" wire:model="start_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- End Time -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                            <input type="time" wire:model="end_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Slot Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slot Duration (minutes)</label>
                            <select wire:model="slot_duration" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="15">15 minutes</option>
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                                <option value="120">120 minutes</option>
                            </select>
                        </div>

                        <!-- Max Patients per Slot -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Patients per Slot</label>
                            <input type="number" wire:model="max_patients_per_slot" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                {{ $editingId ? 'Update' : 'Add' }} Timeslot
                            </button>
                            @if($editingId)
                                <button type="button" wire:click="resetForm" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Timeslot List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Your Available Timeslots</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Range</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Patients</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($timeslots as $timeslot)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($timeslot->day_of_week) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">
                                                {{ $timeslot->start_time->format('h:i A') }} - {{ $timeslot->end_time->format('h:i A') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ $timeslot->slot_duration }} min</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ $timeslot->max_patients_per_slot }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="toggleStatus({{ $timeslot->id }})" class="text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $timeslot->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $timeslot->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button wire:click="editTimeslot({{ $timeslot->id }})" class="text-blue-600 hover:text-blue-900">
                                                    Edit
                                                </button>
                                                <button wire:click="deleteTimeslot({{ $timeslot->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            No timeslots configured yet. Add your first timeslot above.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Setup Section -->
                <div class="mt-6 bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Setup</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button wire:click="setupWeekdayHours" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                            <h4 class="font-medium text-gray-900">Weekday Hours</h4>
                            <p class="text-sm text-gray-600 mt-1">Set Monday-Friday, 9 AM - 5 PM</p>
                        </button>
                        <button wire:click="setupWeekendHours" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                            <h4 class="font-medium text-gray-900">Weekend Hours</h4>
                            <p class="text-sm text-gray-600 mt-1">Set Saturday-Sunday, 10 AM - 2 PM</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>