<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
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

        @if (session()->has('error'))
            <div class="mb-6 animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Withdrawals</h1>
                    <p class="mt-2 text-sm text-gray-600">Withdraw your earnings and track payment requests</p>
                </div>
            </div>
        </div>

        <!-- Balance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Available Balance -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-700">Available Balance</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">UGX {{ number_format($availableBalance) }}</p>
                        <p class="text-xs text-green-600 mt-1">Ready to withdraw</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Withdrawals -->
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-700">Pending Withdrawals</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">UGX {{ number_format($pendingWithdrawals) }}</p>
                        <p class="text-xs text-yellow-600 mt-1">Awaiting processing</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-700">Total Earnings</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">UGX {{ number_format($totalEarnings) }}</p>
                        <p class="text-xs text-blue-600 mt-1">All time</p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Withdraw Button -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 flex items-center justify-center">
                <button wire:click="openRequestModal" 
                        @if($availableBalance <= 0) disabled @endif
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed w-full flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Withdraw Funds
                </button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <p class="text-sm text-gray-600">Total Withdrawn</p>
                <p class="text-xl font-bold text-gray-900">UGX {{ number_format($withdrawalStats['total_withdrawn']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <p class="text-sm text-gray-600">Pending Requests</p>
                <p class="text-xl font-bold text-yellow-600">{{ number_format($withdrawalStats['pending_count']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <p class="text-sm text-gray-600">Completed</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($withdrawalStats['completed_count']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <p class="text-sm text-gray-600">Total Fees</p>
                <p class="text-xl font-bold text-red-600">UGX {{ number_format($withdrawalStats['total_fees']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Withdrawals</h3>
                        <div class="flex items-center space-x-3">
                            <select wire:model.live="statusFilter" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                                <option value="all">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentTransactions as $withdrawal)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            #{{ $withdrawal->withdrawal_number }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-bold text-gray-900">UGX {{ number_format($withdrawal->amount) }}</div>
                                            <div class="text-xs text-gray-500">Fee: UGX {{ number_format($withdrawal->fee) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $withdrawal->method) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $withdrawal->status_color }}">
                                                {{ ucfirst($withdrawal->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $withdrawal->requested_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($withdrawal->status === 'pending')
                                                <button wire:click="cancelWithdrawal({{ $withdrawal->id }})" 
                                                        onclick="return confirm('Cancel this withdrawal request?')"
                                                        class="text-red-600 hover:text-red-900 text-sm">
                                                    Cancel
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            No withdrawal history yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($withdrawals->hasPages())
                        <div class="mt-6">
                            {{ $withdrawals->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Withdrawal Information</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Minimum Amount</span>
                            <span class="text-sm font-medium text-gray-900">UGX 50,000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Processing Fee</span>
                            <span class="text-sm font-medium text-red-600">{{ $withdrawalFeeRate }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Processing Time</span>
                            <span class="text-sm font-medium text-gray-900">3-5 Business Days</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Hold Period</span>
                            <span class="text-sm font-medium text-gray-900">3 Days</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h4>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Bank Transfer</p>
                                <p class="text-xs text-gray-500">2-3 business days</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Mobile Money</p>
                                <p class="text-xs text-gray-500">Instant</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal Request Modal -->
        @if($showRequestModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-10 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Request Withdrawal</h3>
                        <button wire:click="$set('showRequestModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="requestWithdrawal">
                        <!-- Amount -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Withdraw (UGX)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">UGX</span>
                                <input type="number" wire:model="requestAmount" 
                                       min="50000" 
                                       max="{{ $availableBalance }}"
                                       step="1000"
                                       class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">Min: UGX 50,000</span>
                                <span class="text-xs text-gray-500">Max: UGX {{ number_format($availableBalance) }}</span>
                            </div>
                            @error('requestAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Calculation -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Amount</span>
                                    <span class="text-sm font-medium">UGX {{ number_format($requestAmount) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Fee ({{ $withdrawalFeeRate }}%)</span>
                                    <span class="text-sm font-medium text-red-600">UGX {{ number_format(($requestAmount * $withdrawalFeeRate) / 100) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-900">You Receive</span>
                                        <span class="text-lg font-bold text-green-600">
                                            UGX {{ number_format($requestAmount - (($requestAmount * $withdrawalFeeRate) / 100)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative">
                                    <input type="radio" wire:model="withdrawalMethod" value="bank_transfer" class="sr-only peer">
                                    <div class="p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-500 peer-checked:text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            <span class="text-sm">Bank Transfer</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" wire:model="withdrawalMethod" value="mobile_money" class="sr-only peer">
                                    <div class="p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-500 peer-checked:text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm">Mobile Money</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('withdrawalMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Method Details -->
                        @if($withdrawalMethod === 'bank_transfer')
                            <div class="mb-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                                    <input type="text" wire:model="bankName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                                    <input type="text" wire:model="accountNumber" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                                    <input type="text" wire:model="accountName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        @elseif($withdrawalMethod === 'mobile_money')
                            <div class="mb-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                                    <select wire:model="mobileProvider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="mtn">MTN Mobile Money</option>
                                        <option value="airtel">Airtel Money</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" wire:model="mobileNumber" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                                    <input type="text" wire:model="mobileAccountName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        @endif

                        <div class="flex space-x-3">
                            <button type="button" 
                                    wire:click="$set('showRequestModal', false)"
                                    class="flex-1 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="flex-1 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                <span wire:loading.remove wire:target="requestWithdrawal">
                                    Request Withdrawal
                                </span>
                                <span wire:loading wire:target="requestWithdrawal" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>