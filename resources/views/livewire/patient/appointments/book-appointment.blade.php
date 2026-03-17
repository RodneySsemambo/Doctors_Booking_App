<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12">
    <div class="container mx-auto px-4">

        {{-- Page Header --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Book an Appointment</h1>
            <p class="text-gray-600">Schedule your consultation with our expert doctors</p>
        </div>

        {{-- Step Progress --}}
        <div class="max-w-4xl mx-auto mb-8">
            <div class="flex items-center justify-between">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex items-center {{ $i < $totalSteps ? 'flex-1' : '' }}">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold transition-all duration-300
                                {{ $currentStep > $i  ? 'bg-blue-600 text-white' : '' }}
                                {{ $currentStep === $i ? 'bg-blue-600 text-white ring-4 ring-blue-200' : '' }}
                                {{ $currentStep < $i  ? 'bg-gray-200 text-gray-500' : '' }}">
                                @if($currentStep > $i)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $i }}
                                @endif
                            </div>
                            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 whitespace-nowrap">
                                <span class="text-xs font-medium {{ $currentStep >= $i ? 'text-blue-600' : 'text-gray-400' }}">
                                    @if($i === 1) Select Doctor
                                    @elseif($i === 2) Date & Time
                                    @elseif($i === 3) Details
                                    @else Payment
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if($i < $totalSteps)
                            <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300
                                {{ $currentStep > $i ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        {{-- Validation errors (global) --}}
        @if($errors->any())
            <div class="max-w-4xl mx-auto mb-4 mt-16">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-700">Please fix the following:</p>
                        <ul class="mt-1 text-sm text-red-600 list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-4xl mx-auto mb-4 mt-16">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="font-semibold text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden mt-16">
            <div class="p-8">

                {{-- ==============================================================
                     STEP 1 — Select Doctor
                ============================================================== --}}
                @if($currentStep === 1)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Select a Doctor</h2>

                    {{-- Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Specialization</label>
                            <select wire:model.live="selectedSpecialization"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Specializations</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Doctors Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($doctors as $doctor)
                            {{-- wire:click sets the property via Livewire XHR — no form needed --}}
                            <div wire:click="$set('selectedDoctor', {{ $doctor->id }})"
                                 class="p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-lg
                                        {{ $selectedDoctor == $doctor->id
                                            ? 'border-blue-600 bg-blue-50 shadow-md'
                                            : 'border-gray-200 hover:border-blue-300' }}">
                                <div class="flex items-start gap-4">
                                    @if($doctor->profile_photo)
                                        <img src="{{ Storage::url($doctor->profile_photo) }}"
                                             alt="Dr. {{ $doctor->first_name }}"
                                             class="w-16 h-16 rounded-full object-cover border-2 border-blue-200 flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200
                                                    flex items-center justify-center border-2 border-blue-200 flex-shrink-0">
                                            <span class="text-blue-700 font-bold text-xl">
                                                {{ substr($doctor->first_name ?? '', 0, 1) }}{{ substr($doctor->last_name ?? '', 0, 1) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <h3 class="font-bold text-gray-900 truncate">
                                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                            </h3>
                                            @if($selectedDoctor == $doctor->id)
                                                <span class="w-5 h-5 flex-shrink-0 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-blue-600 font-medium">{{ $doctor->specialization->name ?? 'General' }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-500">{{ $doctor->years_of_experience ?? 0 }} yrs exp.</span>
                                            <span class="text-sm font-bold text-green-600">
                                                UGX {{ number_format($doctor->consultation_fee ?? 0) }}
                                            </span>
                                        </div>
                                        @if($doctor->rating)
                                            <div class="flex items-center gap-1 mt-1">
                                                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="text-xs text-gray-600">{{ $doctor->rating }}/5</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">No doctors available for this filter</p>
                            </div>
                        @endforelse
                    </div>

                    @error('selectedDoctor')
                        <p class="mt-3 text-sm text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                @endif

                {{-- ==============================================================
                     STEP 2 — Date & Time
                     FIX: slots now always load (no longer blocked by null available_days)
                ============================================================== --}}
                @if($currentStep === 2)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Choose Date & Time</h2>

                    {{-- Selected doctor banner --}}
                    @if($doctorDetails)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center gap-4">
                            @if($doctorDetails->profile_photo)
                                <img src="{{ Storage::url($doctorDetails->profile_photo) }}"
                                     alt="Dr. {{ $doctorDetails->first_name }}"
                                     class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-700 font-bold">
                                        {{ substr($doctorDetails->first_name ?? '', 0, 1) }}{{ substr($doctorDetails->last_name ?? '', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-900">
                                    Dr. {{ $doctorDetails->first_name }} {{ $doctorDetails->last_name }}
                                </p>
                                <p class="text-sm text-blue-600">{{ $doctorDetails->specialization->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Appointment Type --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Appointment Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div wire:click="$set('appointmentType', 'in-person')"
                                 class="p-4 border-2 rounded-xl cursor-pointer transition
                                        {{ $appointmentType === 'in-person' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 {{ $appointmentType === 'in-person' ? 'text-blue-600' : 'text-gray-400' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">In-Person</p>
                                        <p class="text-xs text-gray-500">Visit the hospital</p>
                                    </div>
                                </div>
                            </div>
                            <div wire:click="$set('appointmentType', 'virtual')"
                                 class="p-4 border-2 rounded-xl cursor-pointer transition
                                        {{ $appointmentType === 'virtual' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 {{ $appointmentType === 'virtual' ? 'text-blue-600' : 'text-gray-400' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Virtual</p>
                                        <p class="text-xs text-gray-500">Online consultation</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Date</label>
                        <input type="date"
                               wire:model.live="selectedDate"
                               min="{{ Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('selectedDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Time Slots --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Available Time Slots
                            @if($selectedTime)
                                <span class="ml-2 text-blue-600 font-normal text-xs">
                                    Selected: {{ Carbon\Carbon::parse($selectedTime)->format('h:i A') }}
                                </span>
                            @endif
                        </label>

                        @if(count($availableTimeSlots) > 0)
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                @foreach($availableTimeSlots as $slot)
                                    {{-- type="button" is critical — prevents browser treating it as form submit --}}
                                    <button type="button"
                                            wire:click="$set('selectedTime', '{{ $slot['value'] }}')"
                                            class="px-4 py-3 rounded-lg font-medium text-sm transition
                                                   {{ $selectedTime === $slot['value']
                                                       ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300'
                                                       : 'bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                                        {{ $slot['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500 font-medium text-sm">No slots available for this date</p>
                                <p class="text-gray-400 text-xs mt-1">
                                    The doctor may not work on this day. Try selecting a different date.
                                </p>
                            </div>
                            {{-- When no slots, still let user proceed if they just want to note it --}}
                            <p class="mt-3 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                                💡 You can still proceed and the hospital will confirm a time with you.
                            </p>
                        @endif

                        @error('selectedTime')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endif

                {{-- ==============================================================
                     STEP 3 — Reason & Summary
                ============================================================== --}}
                @if($currentStep === 3)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Appointment Details</h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Reason for Visit <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="reason"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Please describe your symptoms or reason for consultation…"></textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Additional Notes <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea wire:model="notes"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Any other information you'd like to share…"></textarea>
                        </div>

                        {{-- Summary --}}
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 p-6 rounded-2xl border border-blue-200">
                            <h3 class="font-bold text-gray-900 mb-4">Appointment Summary</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Doctor</span>
                                    <span class="font-semibold">Dr. {{ $doctorDetails->first_name ?? '' }} {{ $doctorDetails->last_name ?? '' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date</span>
                                    <span class="font-semibold">
                                        {{ $selectedDate ? Carbon\Carbon::parse($selectedDate)->format('D, M d Y') : '—' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time</span>
                                    <span class="font-semibold">
                                        {{ $selectedTime ? Carbon\Carbon::parse($selectedTime)->format('h:i A') : 'To be confirmed' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Type</span>
                                    <span class="font-semibold">{{ ucfirst(str_replace('-', ' ', $appointmentType)) }}</span>
                                </div>
                                <div class="flex justify-between pt-3 border-t border-blue-200">
                                    <span class="font-bold text-gray-900">Consultation Fee</span>
                                    <span class="font-bold text-green-600 text-base">UGX {{ number_format($consultationFee) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ==============================================================
                     STEP 4 — Payment
                ============================================================== --}}
                @if($currentStep === 4)
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Method</h2>

                    <div class="space-y-3 mb-6">
                        {{-- MTN --}}
                        <div wire:click="$set('paymentMethod', 'mtn_mobile_money')"
                             class="p-4 border-2 rounded-xl cursor-pointer transition
                                    {{ $paymentMethod === 'mtn_mobile_money' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-yellow-400 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-black text-lg">M</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">MTN Mobile Money</p>
                                    <p class="text-sm text-gray-500">Pay with MTN MoMo</p>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                            {{ $paymentMethod === 'mtn_mobile_money' ? 'border-yellow-500 bg-yellow-500' : 'border-gray-300' }}">
                                    @if($paymentMethod === 'mtn_mobile_money')
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Airtel --}}
                        <div wire:click="$set('paymentMethod', 'airtel_mobile_money')"
                             class="p-4 border-2 rounded-xl cursor-pointer transition
                                    {{ $paymentMethod === 'airtel_mobile_money' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-black text-lg">A</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Airtel Money</p>
                                    <p class="text-sm text-gray-500">Pay with Airtel Money</p>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                            {{ $paymentMethod === 'airtel_mobile_money' ? 'border-red-500 bg-red-500' : 'border-gray-300' }}">
                                    @if($paymentMethod === 'airtel_mobile_money')
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Cash --}}
                        <div wire:click="$set('paymentMethod', 'cash')"
                             class="p-4 border-2 rounded-xl cursor-pointer transition
                                    {{ $paymentMethod === 'cash' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">Cash Payment</p>
                                    <p class="text-sm text-gray-500">Pay at the hospital</p>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                            {{ $paymentMethod === 'cash' ? 'border-green-500 bg-green-500' : 'border-gray-300' }}">
                                    @if($paymentMethod === 'cash')
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($paymentMethod !== 'cash')
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   wire:model="phoneNumber"
                                   placeholder="+256 700 000 000"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('phoneNumber')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox"
                                   wire:model="agreedToTerms"
                                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5 flex-shrink-0">
                            <span class="text-sm text-gray-700">
                                I agree to the
                                <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a>
                                and
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                        @error('agreedToTerms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-2xl border-2 border-green-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg font-bold text-gray-900">Total Amount</span>
                            <span class="text-3xl font-bold text-green-600">UGX {{ number_format($consultationFee) }}</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            @if($paymentMethod === 'cash')
                                Pay this amount at the hospital reception before your appointment.
                            @else
                                You will receive a payment prompt on your phone to complete the transaction.
                            @endif
                        </p>
                    </div>
                </div>
                @endif

            </div>{{-- /p-8 --}}

            {{-- Navigation --}}
            <div class="bg-gray-50 px-8 py-5 flex items-center justify-between border-t border-gray-200">

                @if($currentStep > 1)
                    <button type="button"
                            wire:click="previousStep"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl
                                   font-semibold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </button>
                @else
                    <div></div>
                @endif

                @if($currentStep < $totalSteps)
                    <button type="button"
                            wire:click="nextStep"
                            wire:loading.attr="disabled"
                            wire:target="nextStep"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl
                                   font-semibold transition flex items-center gap-2
                                   disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="nextStep">
                            Continue
                            <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                        <span wire:loading wire:target="nextStep" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Validating…
                        </span>
                    </button>
                @else
                    <button type="button"
                            wire:click="bookAppointment"
                            wire:loading.attr="disabled"
                            wire:target="bookAppointment"
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl
                                   font-bold transition flex items-center gap-2 shadow-lg
                                   disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="bookAppointment">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirm Booking
                        </span>
                        <span wire:loading wire:target="bookAppointment" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Booking…
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>