<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IDCloudHostService
{
    protected $apiUrl;
    protected $apiKey;
    protected $apiUser;

    public function __construct()
    {
        $this->apiUrl = config('services.idcloudhost.api_url', 'https://my.idcloudhost.com/api.php');
        $this->apiKey = config('services.idcloudhost.api_key');
        $this->apiUser = config('services.idcloudhost.api_username');
    }

    /**
     * Cek Ketersediaan Domain (WHOIS lookup)
     *
     * @param string $domain
     * @return array
     */
    public function checkAvailability(string $domain): array
    {
        $domain = strtolower(trim($domain));
        
        // Validasi format domain sederhana
        if (!preg_match('/^[a-z0-9\-]+\.[a-z]{2,6}(\.[a-z]{2,6})?$/', $domain)) {
            return [
                'status' => 'invalid',
                'message' => 'Format domain tidak valid.'
            ];
        }

        // Jika API Key/User tidak dikonfigurasi, gunakan pengecekan WHOIS lokal via socket
        if (empty($this->apiKey) || empty($this->apiUser)) {
            return $this->fallbackWhoisCheck($domain);
        }

        try {
            // Hit WHMCS / IDCloudHost Domain Availability API
            $response = Http::asForm()->post($this->apiUrl, [
                'username' => $this->apiUser,
                'password' => $this->apiKey,
                'action'   => 'checkavailability',
                'domain'   => $domain,
                'responsetype' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Jika domain tersedia
                if (isset($data['result']) && $data['result'] === 'success') {
                    $isAvailable = $data['status'] === 'available';
                    return [
                        'status' => $isAvailable ? 'available' : 'registered',
                        'price' => $this->getDomainPrice($domain),
                        'message' => $isAvailable ? 'Domain tersedia untuk didaftarkan.' : 'Domain sudah terdaftar.'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('IDCloudHost API checkAvailability failed: ' . $e->getMessage());
        }

        // Jika API gagal atau bermasalah, gunakan WHOIS socket fallback
        return $this->fallbackWhoisCheck($domain);
    }

    /**
     * Daftarkan Domain via IDCloudHost API
     *
     * @param string $domain
     * @param int $years
     * @param array $contact
     * @return array
     */
    public function registerDomain(string $domain, int $years = 1, array $contact = []): array
    {
        if (empty($this->apiKey) || empty($this->apiUser)) {
            Log::info("IDCloudHost mock registration triggered for {$domain}");
            return [
                'success' => true,
                'message' => 'Pendaftaran domain sukses (Simulated Mode - API Credentials Kosong).',
                'domain' => $domain,
                'status' => 'registered'
            ];
        }

        try {
            // Hit WHMCS / IDCloudHost Domain Registration API
            $response = Http::asForm()->post($this->apiUrl, [
                'username' => $this->apiUser,
                'password' => $this->apiKey,
                'action'   => 'registerdomain',
                'domain'   => $domain,
                'regperiod'=> $years,
                'domainfields' => base64_encode(serialize([])),
                'responsetype' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']) && $data['result'] === 'success') {
                    return [
                        'success' => true,
                        'message' => 'Registrasi domain berhasil.',
                        'domain' => $domain,
                        'status' => 'registered'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Gagal mendaftarkan domain melalui API.',
                    'status' => 'failed'
                ];
            }
        } catch (\Exception $e) {
            Log::error('IDCloudHost API registerDomain failed: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => 'Terjadi kesalahan koneksi API IDCloudHost.',
            'status' => 'failed'
        ];
    }

    /**
     * Pengecekan WHOIS Socket Fallback
     */
    protected function fallbackWhoisCheck(string $domain): array
    {
        $parts = explode('.', $domain);
        $tld = end($parts);

        // Server WHOIS berdasarkan TLD populer
        $whoisServers = [
            'com' => 'whois.verisign-grs.com',
            'net' => 'whois.verisign-grs.com',
            'org' => 'whois.pir.org',
            'id'  => 'whois.pandi.or.id',
            'my.id' => 'whois.pandi.or.id',
            'web.id' => 'whois.pandi.or.id',
        ];

        $server = $whoisServers[$tld] ?? 'whois.iana.org';
        
        try {
            $fp = @fsockopen($server, 43, $errno, $errstr, 5);
            if ($fp) {
                fputs($fp, $domain . "\r\n");
                $out = "";
                while (!feof($fp)) {
                    $out .= fgets($fp, 128);
                }
                fclose($fp);

                // Cek kata kunci umum untuk status "tersedia"
                $availableKeywords = [
                    'No match for', 'NOT FOUND', 'No Match', 'domain is not registered',
                    'is available', 'Status: AVAILABLE', 'Domain tidak ditemukan', 'Not Registered'
                ];

                $isAvailable = false;
                foreach ($availableKeywords as $keyword) {
                    if (stripos($out, $keyword) !== false) {
                        $isAvailable = true;
                        break;
                    }
                }

                return [
                    'status' => $isAvailable ? 'available' : 'registered',
                    'price' => $this->getDomainPrice($domain),
                    'message' => $isAvailable ? 'Domain tersedia (Local WHOIS).' : 'Domain sudah terdaftar.'
                ];
            }
        } catch (\Exception $e) {
            Log::error("Whois socket fallback failed for {$domain}: " . $e->getMessage());
        }

        // Default jika socket gagal
        return [
            'status' => 'available',
            'price' => $this->getDomainPrice($domain),
            'message' => 'Koneksi WHOIS terhambat, diasumsikan tersedia.'
        ];
    }

    /**
     * Ambil harga domain beserta margin keuntungan FKStudio
     */
    public function getDomainPrice(string $domain): float
    {
        $parts = explode('.', $domain);
        $tld = end($parts);

        // check double TLD (e.g. my.id, web.id)
        if (count($parts) >= 3) {
            $secondLast = $parts[count($parts) - 2];
            $doubleTld = strtolower("{$secondLast}.{$tld}");
            if (in_array($doubleTld, ['my.id', 'web.id', 'co.id', 'or.id'])) {
                $tld = $doubleTld;
            }
        }

        $prices = [
            'com' => 165000,
            'id' => 245000,
            'my.id' => 35000,
            'web.id' => 75000,
            'net' => 185000,
            'org' => 195000,
        ];

        return (float) ($prices[$tld] ?? 165000);
    }
}
