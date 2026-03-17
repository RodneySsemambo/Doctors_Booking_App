<?php

namespace App\Livewire;

use Livewire\Component;

class CounterTest extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <div class="text-center">
                <p class="text-4xl font-bold text-gray-900 mb-4">{{ $count }}</p>
                <button 
                    wire:click="increment" 
                    type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    Increment Counter
                </button>
                <p class="text-sm text-gray-500 mt-4">
                    @if($count > 0)
                        ✅ Livewire is working! Counter: {{ $count }}
                    @else
                        Click the button to test Livewire
                    @endif
                </p>
            </div>
        </div>
        HTML;
    }
}
