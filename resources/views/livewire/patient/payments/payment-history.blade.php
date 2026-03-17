<div class="min-h-screen bg-gray-50 py-8" x-data="{ 
    showPaymentModal: @js($showPaymentModal),
    showDetailsModal: @js($showDetailsModal),
    showReceiptModal: @js($showReceiptModal)
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Payments</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage your payment history</p>
                </div>
                <button x-on:click="showPaymentModal = true" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Make Payment
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            {{ session('error') }}
        </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Total Payments</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Completed</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Failed</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $payment->payment_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">UGX {{ number_format($payment->amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button wire:click="viewDetails({{ $payment->id }})" class="text-blue-600 hover:text-blue-900">View</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Make Payment</h3>
                <button x-on:click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit.prevent="initiatePayment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Appointment ID</label>
                    <input type="text" wire:model="paymentAppointmentId" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount (UGX)</label>
                    <input type="number" wire:model="paymentAmount" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" wire:model="paymentPhone" class="w-full px-4 py-2 border rounded-lg" placeholder="+256 700 000 000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select wire:model="paymentMethod" class="w-full px-4 py-2 border rounded-lg">
                        <option value="mtn_mobile_money">MTN Mobile Money</option>
                        <option value="airtel_mobile_money">Airtel Money</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">Pay Now</button>
            </form>
        </div>
    </div>
</div>
