<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiscoverMarzPayEndpoints extends Command
{
    protected $signature = 'marzpay:discover';
    protected $description = 'Discover MarzPay API endpoints';

    public function handle()
    {
        $apiKey = config('services.marzpay.api_key');
        $baseUrl = 'https://wallet.wearemarz.com/api/v1';

        $this->info("Using API Key: " . substr($apiKey, 0, 10) . "...");
        $this->info("Testing base URL: " . $baseUrl);

        // Test common endpoints
        $endpoints = [
            '/',
            '/ping',
            '/health',
            '/status',
            '/docs',
            '/documentation',
            '/api-docs',
            '/swagger',
            '/collections',
            '/payments',
            '/transactions',
            '/mobile-money',
        ];

        $results = [];

        foreach ($endpoints as $endpoint) {
            $url = $baseUrl . $endpoint;

            $this->line("Testing: " . $url);

            try {
                // Try GET
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                ])->timeout(5)->get($url);

                $results[$url] = [
                    'method' => 'GET',
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 200),
                ];

                // If GET works, also try POST with minimal data
                if ($response->successful() || $response->status() === 405) {
                    $postResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout(5)->post($url, ['test' => true]);

                    $results[$url . ' (POST)'] = [
                        'method' => 'POST',
                        'status' => $postResponse->status(),
                        'body' => substr($postResponse->body(), 0, 200),
                    ];
                }
            } catch (\Exception $e) {
                $results[$url] = [
                    'error' => $e->getMessage()
                ];
            }
        }

        $this->info("\nDiscovery Results:");
        $this->table(
            ['URL', 'Method', 'Status', 'Response/Error'],
            array_map(function ($url, $data) {
                return [
                    'url' => $url,
                    'method' => $data['method'] ?? 'N/A',
                    'status' => $data['status'] ?? 'N/A',
                    'response' => $data['body'] ?? ($data['error'] ?? 'N/A'),
                ];
            }, array_keys($results), $results)
        );

        return 0;
    }
}
