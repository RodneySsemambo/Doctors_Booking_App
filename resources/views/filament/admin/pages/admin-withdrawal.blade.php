<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Balance Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Available Balance
            </x-slot>
            <x-slot name="description">
                Your current platform wallet balance
            </x-slot>
            <x-slot name="headerEnd">
                <x-filament::badge color="success" icon="heroicon-m-check-circle">
                    Active
                </x-filament::badge>
            </x-slot>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        UGX {{ number_format($this->getWalletBalance()) }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Minimum withdrawal:
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            UGX {{ number_format($minimumWithdrawal) }}
                        </span>
                    </p>
                </div>

                <x-filament::icon-button
                    icon="heroicon-o-arrow-path"
                    wire:click="$refresh"
                    color="gray"
                    tooltip="Refresh balance"
                    size="lg"
                />
            </div>

        
            {{-- Quick Stats --}}
            <x-filament::section>
            <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Pending</p>
                    <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mt-0.5">
                        {{ $pendingCount ?? 0 }}
                    </p>
                </div>
                </x-filament::section>
                            <x-filament::section>

                <div class="text-center border-x border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Processing</p>
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5">
                        {{ $processingCount ?? 0 }}
                    </p>
                </div>
                            </x-filament::section>
                                        <x-filament::section>

                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium">Completed</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-0.5">
                        {{ $completedCount ?? 0 }}
                    </p>
                </div>
                                        </x-filament::section>
            </div>
        </x-filament::section>

        {{-- Minimum Warning --}}
        @if($this->getWalletBalance() < $minimumWithdrawal)
            <x-filament::section>
                <div class="flex items-start gap-3">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        class="w-5 h-5 text-warning-500 flex-shrink-0 mt-0.5"
                    />
                    <div>
                         <p class="text-sm font-medium text-red-700 dark:text-warning-400 text-center text-lg">
                            Warning
                        </p>
                        <p class="text-sm font-medium text-red-700 dark:text-warning-400">
                            Insufficient Balance
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Your balance is below the minimum withdrawal amount of
                            <span class="font-medium">UGX {{ number_format($minimumWithdrawal) }}</span>.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Withdrawal Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Request Withdrawal
            </x-slot>
            <x-slot name="description">
                Enter amount and payment details below
            </x-slot>

            <form wire:submit.prevent="submit" class="space-y-6">

                {{ $this->form }}

                

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">

                    {{-- View History Link --}}
                    <x-filament::link
                        href="{{ url('/admin/admin-withdrawal-resource/admin-withdrawals') }}"
                        icon="heroicon-o-arrow-right"
                        icon-position="after"
                        color="gray"
                        size="sm">
                        View Withdrawal History
                    </x-filament::link>

                    {{-- Submit Button --}}
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-paper-airplane"
                        wire:loading.attr="disabled"
                        wire:target="submit">
                        <span wire:loading.remove wire:target="submit">
                            Submit Request
                        </span>
                        <span wire:loading wire:target="submit">
                            Submitting...
                        </span>
                    </x-filament::button>

                </div>
            </form>
        </x-filament::section>

        {{-- Recent Withdrawals Preview --}}
        @if(isset($recentWithdrawals) && $recentWithdrawals->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                Recent Requests
            </x-slot>
            <x-slot name="headerEnd">
                <x-filament::link
                    href="{{ url('/admin/admin-withdrawal-resource/admin-withdrawals') }}"
                    color="primary"
                    size="sm">
                    View All
                </x-filament::link>
            </x-slot>

            <div class="space-y-3">
                @foreach($recentWithdrawals->take(3) as $w)
                <div class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            @php
                                $iconConfig = match($w->method) {
                                    'mobile_money' => ['heroicon-o-device-phone-mobile', 'text-green-600 dark:text-green-400', 'bg-green-50 dark:bg-green-900/20'],
                                    'bank_transfer' => ['heroicon-o-building-library', 'text-blue-600 dark:text-blue-400', 'bg-blue-50 dark:bg-blue-900/20'],
                                    'paypal' => ['heroicon-o-globe-alt', 'text-indigo-600 dark:text-indigo-400', 'bg-indigo-50 dark:bg-indigo-900/20'],
                                    default => ['heroicon-o-banknotes', 'text-gray-600', 'bg-gray-100'],
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $iconConfig[2] }}">
                                <x-filament::icon :icon="$iconConfig[0]" class="w-4 h-4 {{ $iconConfig[1] }}" />
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                UGX {{ number_format($w->net_amount) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $w->requested_at->diffForHumans() }} · {{ ucwords(str_replace('_', ' ', $w->method)) }}
                            </p>
                        </div>
                    </div>

                    @php
                        $badgeColor = match($w->status) {
                            'completed'  => 'success',
                            'pending'    => 'warning',
                            'processing' => 'info',
                            'failed'     => 'danger',
                            'cancelled'  => 'gray',
                            default      => 'gray',
                        };
                    @endphp
                    <x-filament::badge :color="$badgeColor" size="sm">
                        {{ ucfirst($w->status) }}
                    </x-filament::badge>
                </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>