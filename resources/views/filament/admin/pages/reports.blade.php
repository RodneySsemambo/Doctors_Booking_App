<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::card>
            <form wire:submit.prevent="generateReport">
                {{ $this->form }}

                <div class="flex gap-3 mt-6">
                    <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                        Generate PDF Report
                    </x-filament::button>

                   
                </div>
            </form>
        </x-filament::card>

        <x-filament::card>
            <div class="prose dark:prose-invert">
                <h3>Available Reports</h3>
                <ul>
                    <li><strong>Appointments Report:</strong> Detailed list of all appointments with status breakdown</li>
                    <li><strong>Revenue Report:</strong> Financial summary with payment methods and totals</li>
                    <li><strong>Doctor Performance:</strong> Doctor statistics including appointments and ratings</li>
                    <li><strong>Patient Statistics:</strong> Patient demographics and registration trends</li>
                    <li><strong>Cancellation Report:</strong> Analysis of cancelled appointments</li>
                </ul>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>