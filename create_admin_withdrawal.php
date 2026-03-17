<?php

$content = <<<'BLADE'
<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <!-- Wallet Balance Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Available Balance</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">UGX {{ number_format($this->getWalletBalance()) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Minimum withdrawal: UGX {{ number_format($minimumWithdrawal) }}</p>
        </div>

        <!-- Withdrawal Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Request Withdrawal</h2>
                <p class="text-sm text-gray-500">Enter the amount and payment details below</p>
            </div>

            <form wire:submit.prevent="submit" class="p-6 space-y-6">
                {{ $this->form }}

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ url('/admin/admin-withdrawals') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm">
                        View Withdrawal History →
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
BLADE;

file_put_contents('resources/views/filament/admin/pages/admin-withdrawal.blade.php', $content);
echo "Created";
