<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpInfoService
{
    protected string $token;

    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('ipinfo.token');
        $this->baseUrl = config('ipinfo.base_url', 'https://ipinfo.io');
    }

    public function getCountryCode(string $ip): ?string
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$ip}/country?token={$this->token}");
            if ($response->successful()) {
                return trim($response->body());
            }

            Log::error('IpInfo API error: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('IpInfo Service error: '.$e->getMessage());

            return null;
        }
    }
}
