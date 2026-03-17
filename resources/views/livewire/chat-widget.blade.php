{{--
    REMINDER: Make sure your layout has:
    1. @livewireScripts at the bottom of <body> (NOT in <head>)
    2. No separate Alpine import in app.js — Livewire 3 bundles Alpine automatically
    3. [x-cloak] { display: none !important; } in your CSS or a <style> tag in <head>
--}}

<div class="fixed bottom-6 right-6 z-50" x-data="{ open: false, confirmReset: false }">

    {{-- Floating Chat Button (visible when closed) --}}
    <button
        x-show="!open"
        x-cloak
        @click="open = true"
        type="button"
        title="Open chat"
        class="relative bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-105">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">AI</span>
    </button>

    {{-- Chat Window (visible when open) --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="bg-white rounded-2xl shadow-2xl w-[360px] h-[560px] flex flex-col overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="bg-blue-600 text-white px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-400 border border-white rounded-full"></span>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-none">AI Health Assistant</p>
                    <p class="text-xs text-blue-100 mt-0.5">Online</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                {{-- Reset / New conversation --}}
                <button
                    @click="confirmReset = !confirmReset"
                    type="button"
                    title="New conversation"
                    class="p-1.5 hover:bg-white/20 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                {{-- Close (X) button --}}
                <button
                    @click="open = false; confirmReset = false"
                    type="button"
                    title="Close chat"
                    class="p-1.5 hover:bg-white/20 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Confirm Reset Banner --}}
        <div
            x-show="confirmReset"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-amber-50 border-b border-amber-200 px-4 py-3 flex-shrink-0">
            <p class="text-xs font-medium text-amber-800 mb-2">Clear all messages and start over?</p>
            <div class="flex gap-2">
                <button
                    @click="confirmReset = false"
                    type="button"
                    class="flex-1 text-xs py-1.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button
                    wire:click="resetChat"
                    @click="confirmReset = false"
                    wire:loading.attr="disabled"
                    wire:target="resetChat"
                    type="button"
                    class="flex-1 text-xs py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-medium disabled:opacity-60">
                    <span wire:loading.remove wire:target="resetChat">Yes, clear chat</span>
                    <span wire:loading wire:target="resetChat">Clearing…</span>
                </button>
            </div>
        </div>

        {{-- Messages --}}
        <div
            id="chat-messages-container"
            class="flex-1 overflow-y-auto p-3 space-y-3 bg-gray-50">

            @forelse($chatMessages as $msg)
                @if($msg['is_mine'])
                    {{-- User bubble --}}
                    <div class="flex justify-end">
                        <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-3 py-2 max-w-[78%] text-sm shadow-sm">
                            <p class="break-words">{{ $msg['message'] }}</p>
                            <p class="text-[10px] text-blue-200 mt-1 text-right">{{ $msg['created_at'] }}</p>
                        </div>
                    </div>
                @else
                    {{-- Bot bubble --}}
                    <div class="flex justify-start gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="max-w-[78%]">
                            <div class="bg-white rounded-2xl rounded-tl-sm px-3 py-2 shadow-sm border border-gray-100 text-sm text-gray-800">
                                <p class="break-words whitespace-pre-line">{{ $msg['message'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $msg['created_at'] }}</p>
                            </div>

                            {{-- Quick replies — only on the last bot message --}}
                            @if($loop->last && !empty($msg['quick_replies']))
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach($msg['quick_replies'] as $reply)
                                        <button
                                            wire:click="selectQuickReply('{{ addslashes($reply) }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="selectQuickReply"
                                            type="button"
                                            class="text-xs bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-full px-3 py-1 transition disabled:opacity-50">
                                            {{ $reply }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex flex-col items-center justify-center h-full text-center py-8">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Hi! How can I help?</p>
                    <p class="text-xs text-gray-400 mt-1">Ask me about booking appointments</p>
                </div>
            @endforelse

            {{-- Typing indicator --}}
            @if($isTyping)
                <div class="flex justify-start gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tl-sm px-3 py-2.5 shadow-sm border border-gray-100">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="p-3 bg-white border-t border-gray-100 flex-shrink-0">
            @error('message')
                <p class="text-xs text-red-500 mb-1.5 px-1">{{ $message }}</p>
            @enderror

            {{-- No <form> tag — prevents native browser POST / page refresh --}}
            <div class="flex gap-2 items-center">
                <input
                    wire:model="message"
                    wire:keydown.enter.prevent="sendMessage"
                    type="text"
                    placeholder="Type a message…"
                    autocomplete="off"
                    maxlength="1000"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    class="flex-1 border border-gray-200 rounded-full px-4 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                           disabled:opacity-50 disabled:cursor-not-allowed transition">
                <button
                    wire:click="sendMessage"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-9 h-9 flex items-center
                           justify-center transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                    <svg wire:loading.remove wire:target="sendMessage" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <svg wire:loading wire:target="sendMessage" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Auto-scroll — plain JS, no Alpine dependency --}}
<script>
    (function () {
        function scrollChat() {
            var el = document.getElementById('chat-messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        }

        document.addEventListener('DOMContentLoaded', scrollChat);

        document.addEventListener('livewire:init', function () {
            Livewire.hook('commit', function (args) {
                if (args && typeof args.succeed === 'function') {
                    args.succeed(function () {
                        setTimeout(scrollChat, 50);
                    });
                }
            });
        });
    })();
</script>