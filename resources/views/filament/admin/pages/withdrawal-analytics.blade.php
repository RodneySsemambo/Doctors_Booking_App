<?php

use App\Models\Withdrawal;
?>

<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Withdrawals</div>
            <div class="text-2xl font-bold">{{ $this->getStats()['total_withdrawals'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Amount</div>
            <div class="text-2xl font-bold">${{ number_format($this->getStats()['total_amount'], 2) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $this->getStats()['pending_count'] }}</div>
            <div class="text-sm">${{ number_format($this->getStats()['pending_amount'], 2) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            <div class="text-2xl font-bold text-green-600">{{ $this->getStats()['completed_count'] }}</div>
            <div class="text-sm">${{ number_format($this->getStats()['completed_amount'], 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">By Method</h3>
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500">
                        <th class="pb-2">Method</th>
                        <th class="pb-2">Count</th>
                        <th class="pb-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getByMethod() as $method)
                    <tr class="border-t">
                        <td class="py-2 capitalize">{{ $method['method'] }}</td>
                        <td class="py-2">{{ $method['count'] }}</td>
                        <td class="py-2">${{ number_format($method['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">By Status</h3>
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500">
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Count</th>
                        <th class="pb-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getByStatus() as $status)
                    <tr class="border-t">
                        <td class="py-2 capitalize">{{ $status['status'] }}</td>
                        <td class="py-2">{{ $status['count'] }}</td>
                        <td class="py-2">${{ number_format($status['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Withdrawals</h3>
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500">
                    <th class="pb-2">Withdrawal #</th>
                    <th class="pb-2">Doctor</th>
                    <th class="pb-2">Amount</th>
                    <th class="pb-2">Fee</th>
                    <th class="pb-2">Net</th>
                    <th class="pb-2">Method</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->getRecentWithdrawals() as $withdrawal)
                <tr class="border-t">
                    <td class="py-2">{{ $withdrawal->withdrawal_number }}</td>
                    <td class="py-2">{{ $withdrawal->doctor->first_name ?? 'N/A' }} {{ $withdrawal->doctor->last_name ?? '' }}</td>
                    <td class="py-2">${{ number_format($withdrawal->amount, 2) }}</td>
                    <td class="py-2">${{ number_format($withdrawal->fee, 2) }}</td>
                    <td class="py-2">${{ number_format($withdrawal->net_amount, 2) }}</td>
                    <td class="py-2 capitalize">{{ $withdrawal->method }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $withdrawal->status_color }}">
                            {{ $withdrawal->status }}
                        </span>
                    </td>
                    <td class="py-2">{{ $withdrawal->requested_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
