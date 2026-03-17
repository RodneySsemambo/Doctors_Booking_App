<?php

$content = <<<'BLADE'
<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Withdrawn</p>
                <p class="text-2xl font-bold">UGX {{ number_format($this->getStats()['total_withdrawn']) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $this->getStats()['pending_count'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Processing</p>
                <p class="text-2xl font-bold text-blue-600">{{ $this->getStats()['processing_count'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Monthly</p>
                <p class="text-2xl font-bold text-green-600">UGX {{ number_format($this->getStats()['monthly_withdrawn']) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm mb-1">Status</label>
                    <select wire:model="selectedStatus" class="w-full rounded border-gray-300">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Doctor</label>
                    <select wire:model="selectedDoctor" class="w-full rounded border-gray-300">
                        <option value="">All Doctors</option>
                        @foreach($this->getDoctors() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">From</label>
                    <input type="date" wire:model="dateFrom" class="w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-sm mb-1">To</label>
                    <input type="date" wire:model="dateTo" class="w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-sm mb-1">Month</label>
                    <select wire:model="selectedMonth" class="w-full rounded border-gray-300">
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format("F") }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Year</label>
                    <select wire:model="selectedYear" class="w-full rounded border-gray-300">
                        <option value="{{ now()->year }}">{{ now()->year }}</option>
                        <option value="{{ now()->subYear(1)->year }}">{{ now()->subYear(1)->year }}</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="clearFilters" class="px-4 py-2 bg-gray-200 rounded">Clear</button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Doctor Withdrawal Requests</h3></div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Doctor</th>
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
                    @forelse($this->getWithdrawals() as $w)
                    <tr>
                        <td class="px-6 py-4">{{ $w->withdrawal_number }}</td>
                        <td class="px-6 py-4">{{ $w->doctor->first_name ?? "N/A" }} {{ $w->doctor->last_name ?? "" }}</td>
                        <td class="px-6 py-4">UGX {{ number_format($w->amount) }}</td>
                        <td class="px-6 py-4">UGX {{ number_format($w->fee) }}</td>
                        <td class="px-6 py-4 font-bold">UGX {{ number_format($w->net_amount) }}</td>
                        <td class="px-6 py-4">{{ ucwords(str_replace('_', ' ', $w->method)) }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs @if($w->status=='completed')bg-green-100 @elseif($w->status=='pending')bg-yellow-100 @elseif($w->status=='processing')bg-blue-100 @endif">{{ ucfirst($w->status) }}</span></td>
                        <td class="px-6 py-4">{{ $w->requested_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if(in_array($w->status, ['pending','processing']))
                            <button wire:click="startProcessing({{ $w->id }})" class="text-blue-600 mr-2">Start</button>
                            <button wire:click="viewWithdrawal({{ $w->id }})" class="text-gray-600 mr-2">View</button>
                            <button wire:click="cancelWithdrawal({{ $w->id }})" class="text-red-600">Cancel</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">No withdrawals</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $this->getWithdrawals()->links() }}</div>
        </div>
    </div>

    @if($viewingWithdrawal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeViewModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold">Withdrawal Details</h3></div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-gray-500">Withdrawal #</p><p class="font-medium">{{ $viewingWithdrawal->withdrawal_number }}</p></div>
                        <div><p class="text-gray-500">Doctor</p><p class="font-medium">{{ $viewingWithdrawal->doctor->first_name ?? "N/A" }} {{ $viewingWithdrawal->doctor->last_name ?? "" }}</p></div>
                        <div><p class="text-gray-500">Amount</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->amount) }}</p></div>
                        <div><p class="text-gray-500">Fee</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->fee) }}</p></div>
                        <div><p class="text-gray-500">Net</p><p class="font-medium">UGX {{ number_format($viewingWithdrawal->net_amount) }}</p></div>
                        <div><p class="text-gray-500">Method</p><p class="font-medium">{{ ucwords(str_replace('_', ' ', $viewingWithdrawal->method)) }}</p></div>
                        <div><p class="text-gray-500">Status</p><p class="font-medium">{{ ucfirst($viewingWithdrawal->status) }}</p></div>
                        <div><p class="text-gray-500">Date</p><p class="font-medium">{{ $viewingWithdrawal->requested_at->format('M d, Y h:i A') }}</p></div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end"><button wire:click="closeViewModal" class="px-4 py-2 bg-gray-200 rounded">Close</button></div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
BLADE;

file_put_contents('resources/views/filament/admin/pages/manage-withdrawals.blade.php', $content);
echo "File fixed";
