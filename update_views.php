<?php

$platformWallet = <<<'BLADE'
<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">UGX {{ number_format($this->getWalletBalance()['total_revenue']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Doctor Earnings</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">UGX {{ number_format($this->getWalletBalance()['doctor_earnings']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Platform Earnings</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">UGX {{ number_format($this->getWalletBalance()['platform_earnings']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Available Balance</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">UGX {{ number_format($this->getWalletBalance()['available_balance']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        -->
        <div <!-- Withdraw Section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Withdraw Funds</h3>
                    <p class="text-sm text-gray-500">Min: UGX {{ number_format($minimumWithdrawal) }}</p>
                </div>
                <button wire:click="openWithdrawalModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    Request Withdrawal
                </button>
            </div>
            <div class="p-4 grid grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded p-3">
                    <p class="text-xs text-gray-500">Total Withdrawn</p>
                    <p class="font-semibold">UGX {{ number_format($this->getWalletBalance()['total_withdrawn']) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded p-3">
                    <p class="text-xs text-gray-500">Pending</p>
                    <p class="font-semibold text-yellow-600">UGX {{ number_format($this->getWalletBalance()['pending_withdrawals']) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded p-3">
                    <p class="text-xs text-gray-500">This Month</p>
                    <p class="font-semibold text-green-600">UGX {{ number_format($this->getStats()['completed_amount']) }}</p>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Withdrawal History</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Amount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Fee</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Net</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Method</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->getRecentWithdrawals() as $w)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium">{{ $w->withdrawal_number }}</td>
                        <td class="px-4 py-3">UGX {{ number_format($w->amount) }}</td>
                        <td class="px-4 py-3">UGX {{ number_format($w->fee) }}</td>
                        <td class="px-4 py-3 font-semibold">UGX {{ number_format($w->net_amount) }}</td>
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $w->method)) }}</td>
                        <td class="px-4 py-3">
                            @if($w->status == 'completed')
                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">Completed</span>
                            @elseif($w->status == 'pending')
                            <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($w->status == 'processing')
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Processing</span>
                            @else
                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">{{ ucfirst($w->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $w->requested_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            @if(in_array($w->status, ['pending','processing']))
                            <button wire:click="processWithdrawal({{ $w->id }})" class="text-green-600 hover:underline text-xs mr-2">Process</button>
                            @if($w->status == 'pending')
                            <button wire:click="markProcessing({{ $w->id }})" class="text-blue-600 hover:underline text-xs mr-2">Start</button>
                            @endif
                            <button wire:click="cancelWithdrawal({{ $w->id }})" class="text-red-600 hover:underline text-xs">Cancel</button>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No withdrawals</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showWithdrawalModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50" wire:click="closeWithdrawalModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-4 py-3 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold">Request Withdrawal</h3>
                <button wire:click="closeWithdrawalModal" class="text-gray-400 hover">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <p class="text-sm text-gray-500">Available: UGX {{ number_format($this->getWalletBalance()['available_balance']) }}</p>
                <div>
                    <label class="block text-sm font-medium mb-1">Amount (UGX)</label>
                    <input type="number" wire:model="withdrawalAmount" min="{{ $minimumWithdrawal }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Method</label>
                    <select wire:model="withdrawalMethod" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                @if($withdrawalMethod === 'mobile_money')
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Provider</label>
                        <select wire:model="mobileProvider" class="w-full rounded border-gray-300">
                            <option value="mtn">MTN</option>
                            <option value="airtel">Airtel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" wire:model="mobileNumber" class="w-full rounded border-gray-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1">Account Name</label>
                        <input type="text" wire:model="mobileAccountName" class="w-full rounded border-gray-300">
                    </div>
                </div>
                @endif
                @if($withdrawalMethod === 'bank_transfer')
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank</label>
                        <input type="text" wire:model="bankName" class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Account #</label>
                        <input type="text" wire:model="accountNumber" class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" wire:model="accountName" class="w-full rounded border-gray-300">
                    </div>
                </div>
                @endif
                @if($withdrawalMethod === 'paypal')
                <div>
                    <label class="block text-sm font-medium mb-1">PayPal Email</label>
                    <input type="email" wire:model="paypalEmail" class="w-full rounded border-gray-300">
                </div>
                @endif
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-end gap-2 rounded-b-xl">
                <button wire:click="closeWithdrawalModal" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 rounded text-sm">Cancel</button>
                <button wire:click="proceedToConfirmation" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">Continue</button>
            </div>
        </div>
    </div>
    @endif

    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50" wire:click="closeWithdrawalModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm">
            <div class="px-4 py-3 border-b dark:border-gray-700">
                <h3 class="font-semibold text-red-600">Confirm Withdrawal</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="bg-yellow-50 p-2 rounded text-sm text-yellow-800">Warning: Cannot be undone</div>
                <div class="flex justify-between text-sm"><span>Amount:</span><span class="font-bold">UGX {{ number_format($withdrawalAmount) }}</span></div>
                <div>
                    <label class="block text-sm font-medium mb-1">Type amount to confirm</label>
                    <input type="number" wire:model="confirmedAmount" class="w-full rounded border-gray-300">
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-end gap-2 rounded-b-xl">
                <button wire:click="$set('showConfirmModal', false)" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 rounded text-sm">Back</button>
                <button wire:click="confirmWithdrawal" class="px-3 py-1.5 bg-red-600 text-white rounded text-sm">Confirm</button>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
BLADE;

$manageWithdrawals = <<<'BLADE'
<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Withdrawn</p>
                        <p class="font-semibold">UGX {{ number_format($this->getStats()['total_withdrawn']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pending</p>
                        <p class="font-semibold">{{ $this->getStats()['pending_count'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Processing</p>
                        <p class="font-semibold">{{ $this->getStats()['processing_count'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Monthly</p>
                        <p class="font-semibold">UGX {{ number_format($this->getStats()['monthly_withdrawn']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select wire:model="selectedStatus" class="w-full rounded text-sm border-gray-300">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs text-gray-500 mb-1">Doctor</label>
                    <select wire:model="selectedDoctor" class="w-full rounded text-sm border-gray-300">
                        <option value="">All</option>
                        @foreach($this->getDoctors() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs text-gray-500 mb-1">From</label>
                    <input type="date" wire:model="dateFrom" class="w-full rounded text-sm border-gray-300">
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs text-gray-500 mb-1">To</label>
                    <input type="date" wire:model="dateTo" class="w-full rounded text-sm border-gray-300">
                </div>
                <div class="flex-1 min-w-[100px]">
                    <label class="block text-xs text-gray-500 mb-1">Month</label>
                    <select wire:model="selectedMonth" class="w-full rounded text-sm border-gray-300">
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format("M") }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 min-w-[80px]">
                    <label class="block text-xs text-gray-500 mb-1">Year</label>
                    <select wire:model="selectedYear" class="w-full rounded text-sm border-gray-300">
                        <option value="{{ now()->year }}">{{ now()->year }}</option>
                        <option value="{{ now()->subYear(1)->year }}">{{ now()->subYear(1)->year }}</option>
                    </select>
                </div>
                <button wire:click="clearFilters" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 rounded text-sm">Clear</button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b dark:border-gray-700">
                <h3 class="font-semibold">Doctor Withdrawal Requests</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Doctor</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Amount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Fee</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Net</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Method</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->getWithdrawals() as $w)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium">{{ $w->withdrawal_number }}</td>
                        <td class="px-4 py-3">{{ $w->doctor->first_name ?? 'N/A' }} {{ $w->doctor->last_name ?? '' }}</td>
                        <td class="px-4 py-3">UGX {{ number_format($w->amount) }}</td>
                        <td class="px-4 py-3">UGX {{ number_format($w->fee) }}</td>
                        <td class="px-4 py-3 font-semibold">UGX {{ number_format($w->net_amount) }}</td>
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $w->method)) }}</td>
                        <td class="px-4 py-3">
                            @if($w->status == 'completed')
                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">Completed</span>
                            @elseif($w->status == 'pending')
                            <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($w->status == 'processing')
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Processing</span>
                            @elseif($w->status == 'failed')
                            <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">Failed</span>
                            @else
                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">{{ ucfirst($w->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $w->requested_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            @if(in_array($w->status, ['pending','processing']))
                            <button wire:click="startProcessing({{ $w->id }})" class="text-blue-600 hover:underline text-xs mr-2">Start</button>
                            @endif
                            <button wire:click="viewWithdrawal({{ $w->id }})" class="text-gray-600 hover:underline text-xs mr-2">View</button>
                            @if(in_array($w->status, ['pending','processing']))
                            <button wire:click="cancelWithdrawal({{ $w->id }})" class="text-red-600 hover:underline text-xs">Cancel</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">No withdrawals found</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t">{{ $this->getWithdrawals()->links() }}</div>
        </div>
    </div>

    @if($viewingWithdrawal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50" wire:click="closeViewModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-4 py-3 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold">Withdrawal Details</h3>
                <button wire:click="closeViewModal" class="text-gray-400 hover">&times;</button>
            </div>
            <div class="p-4 space-y-3 text-sm">
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Withdrawal #</p><p class="font-medium">{{ $viewingWithdrawal->withdrawal_number }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Doctor</p><p class="font-medium">{{ $viewingWithdrawal->doctor->first_name ?? 'N/A' }} {{ $viewingWithdrawal->doctor->last_name ?? '' }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Amount</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->amount) }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Fee</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->fee) }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Net</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->net_amount) }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Method</p><p class="font-medium">{{ ucwords(str_replace('_', ' ', $viewingWithdrawal->method)) }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Status</p><p class="font-medium">{{ ucfirst($viewingWithdrawal->status) }}</p></div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded"><p class="text-gray-500">Date</p><p class="font-medium">{{ $viewingWithdrawal->requested_at->format('M d, Y') }}</p></div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-end rounded-b-xl">
                <button wire:click="closeViewModal" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 rounded text-sm">Close</button>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
BLADE;

file_put_contents('resources/views/filament/admin/pages/platform-wallet.blade.php', $platformWallet);
file_put_contents('resources/views/filament/admin/pages/manage-withdrawals.blade.php', $manageWithdrawals);

echo "Updated";
