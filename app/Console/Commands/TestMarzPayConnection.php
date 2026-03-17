<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService;

class TestMarzPayConnection extends Command
{
    protected $signature = 'marzpay:test';
    protected $description = 'Test MarzPay API connection';

    public function handle()
    {
        $paymentService = new PaymentService();

        $this->info('Testing MarzPay API connection...');
        $this->line('API Key: ' . config('services.marzpay.api_key'));
        $this->line('Base URL: ' . config('services.marzpay.base_url'));

        $result = $paymentService->testApiConnection();

        if (isset($result['error'])) {
            $this->error('Connection failed: ' . $result['error']);
            $this->line('Suggestions:');
            foreach ($result['suggestions'] as $suggestion) {
                $this->line('  • ' . $suggestion);
            }
            return 1;
        }

        $this->info('✓ Connection successful!');
        $this->line('URL: ' . $result['url']);
        $this->line('Status: ' . $result['status']);
        $this->line('Response: ' . substr($result['body'], 0, 200));

        return 0;
    }
}
