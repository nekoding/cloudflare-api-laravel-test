<?php

namespace App\Services\Cloudflare;

use Illuminate\Support\Facades\Http;

final class CloudflareService
{

    private $httpAdapter;

    public function __construct()
    {
        $this->httpAdapter = Http::acceptJson()->withHeaders([
            'X-Auth-Email'  => config('services.cloudflare.email'),
            'X-Auth-Key'    => config('services.cloudflare.api_key')
        ])
            ->baseUrl(config('services.cloudflare.base_url'));
    }

    public function ipAccessRule(string $ipaddress, string $mode, string $notes = ""): array
    {
        $res = $this->httpAdapter->post("user/firewall/access_rules/rules", [
            'mode'  => $mode,
            'configuration' => [
                'target'    => 'ip',
                'value'     => $ipaddress,
            ],
            'notes' => $notes
        ]);

        return $res->json();
    }
}
