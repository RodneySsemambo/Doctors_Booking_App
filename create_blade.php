<?php

$content = <<<'BLADE'
<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-lg shadow text-white">
                <p class="text-blue-100">Total Revenue</p>
                <p class="text-3xl font-bold">UGX {{ number_format($this->getWalletBalance()['total_revenue']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-lg shadow text-white">
                <p class="text-green-100">Doctor Earnings</p>
                <p class="text-3xl font-bold">UGX {{ number_format($this->getWalletBalance()['doctor_earnings']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-lg shadow text-white">
                <p class="text-purple-100">Platform Earnings</p>
                <p class="text-3xl font-bold">UGX {{ number_format($this->getWalletBalance()['platform_earnings']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-lg shadow text-white">
                <p class="text-indigo-100">Available Balance</p>
                <p class="text-3xl font-bold">UGX {{ number_format($this->getWalletBalance()['available_balance']) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Withdraw Funds</h3>
                    <p class="text-sm text-gray-500">Minimum: UGX {{ number_format($minimumWithdrawal) }}</p>
                </div>
                <button wire:click="openWithdrawalModal" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Request Withdrawal</button>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded"><p class="text-sm text-gray-500">Total Withdrawn</p><p class="text-xl font-bold">UGX {{ number_format($this->getWalletBalance()['total_withdrawn']) }}</p></div>
                <div class="bg-gray-50 p-4 rounded"><p class="text-sm text-gray-500">Pending</p><p class="text-xl font-bold text-yellow-600">UGX {{ number_format($this->getWalletBalance()['pending_withdrawals']) }}</p></div>
                <div class="bg-gray-50 p-4 rounded"><p class="text-sm text-gray-500">This Month</p><p class="text-xl font-bold text-green-600">UGX {{ number_format($this->getStats()['completed_amount']) }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Withdrawal History</h3></div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Fee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Net</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($this->getRecentWithdrawals() as $w)
                    <tr>
                        <td class="px-6 py-4">{{ $w->withdrawal_number }}</td>
                        <td class="px-6 py-4">UGX {{ number_format($w->amount) }}</td>
                        <td class="px-6 py-4">UGX {{ number_format($w->fee) }}</td>
                        <td class="px-6 py-4 font-bold">UGX {{ number_format($w->net_amount) }}</td>
                        <td class="px-6 py-4">{{ ucwords(str_replace('_', ' ', $w->method)) }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs @if($w->status=='completed')bg-green-100 text-green-800 @elseif($w->status=='pending')bg-yellow-100 text-yellow-800 @elseif($w->status=='processing')bg-blue-100 text-blue-800 @endif">{{ ucfirst($w->status) }}</span></td>
                        <td class="px-6 py-4">{{ $w->requested_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if(in_array($w->status, ['pending','processing']))
                            <button wire:click="processWithdrawal({{ $w->id }})" class="text-green-600 hover:underline mr-2">Process</button>
                            @if($w->status=='pending')
                            <button wire:click="markProcessing({{ $w->id }})" class="text-blue-600 hover:underline mr-2">Start</button>
                            @endif
                            <button wire:click="cancelWithdrawal({{ $w->id }})" class="text-red-600 hover:underline">Cancel</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No withdrawals</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showWithdrawalModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeWithdrawalModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Request Withdrawal</h3>
                    <p class="text-sm text-gray-500">Available: UGX {{ number_format($this->getWalletBalance()['available_balance']) }}</p>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Amount (UGX)</label>
                        <input type="number" wire:model="withdrawalAmount" min="{{ $minimumWithdrawal }}" class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Method</label>
                        <select wire:model="withdrawalMethod" class="w-full rounded border-gray-300">
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    @if($withdrawalMethod === 'mobile_money')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Provider</label>
                            <select wire:model="mobileProvider" class="w-full rounded border-gray-300">
                                <option value="mtn">MTN</option>
                                <option value="airtel">Airtel</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone Number</label>
                            <input type="text" wire:model="mobileNumber" class="w-full rounded border-gray-300">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Account Name</label>
                            <input type="text" wire:model="mobileAccountName" class="w-full rounded border-gray-300">
                        </div>
                    </div>
                    @endif
                    @if($withdrawalMethod === 'bank_transfer')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Bank Name</label>
                            <input type="text" wire:model="bankName" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Account Number</label>
                            <input type="text" wire:model="accountNumber" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Account Name</label>
                            <input type="text" wire:model="accountName" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Branch Code</label>
                            <input type="text" wire:model="branchCode" class="w-full rounded border-gray-300">
                        </div>
                    </div>
                    @endif
                    @if($withdrawalMethod === 'paypal')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">PayPal Email</label>
                            <input type="email" wire:model="paypalEmail" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Account Name</label>
                            <input type="text" wire:model="paypalAccountName" class="w-full rounded border-gray-300">
                        </div>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea wire:model="withdrawalNotes" rows="2" class="w-full rounded border-gray-300"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <button wire:click="closeWithdrawalModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                    <button wire:click="proceedToConfirmation" class="px-4 py-2 bg-indigo-600 text-white rounded">Continue</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeWithdrawalModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-red-600">Confirm Withdrawal</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="bg-yellow-50 p-4 rounded">
                        <p class="text-sm text-yellow-800">Warning: This action cannot be undone.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-gray-500">Amount:</p><p class="font-bold">UGX {{ number_format($withdrawalAmount) }}</p></div>
                        <div><p class="text-gray-500">Method:</p><p class="font-bold">{{ ucwords(str_replace('_', ' ', $withdrawalMethod)) }}</p></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Type amount to confirm</label>
                        <input type="number" wire:model="confirmedAmount" class="w-full rounded border-gray-300">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <button wire:click="$set('showConfirmModal', false)" class="px-4 py-2 bg-gray-200 rounded">Back</button>
                    <button wire:click="confirmWithdrawal" class="px-4 py-2 bg-red-600 text-white rounded">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
BLADE;

file_put_contents('resources/views/filament/admin/pages/platform-wallet.blade.php', $content);
echo "File created successfully";
