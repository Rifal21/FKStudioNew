<?php

namespace App\Services\Waha;

use App\Models\SiteSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for a WAHA (WhatsApp HTTP API) server.
 * Docs: https://waha.devlike.pro — auth is the X-Api-Key header.
 */
class WahaClient
{
    public const DEFAULT_URL = 'https://apiwa.fkstudio.id';
    public const DEFAULT_SESSION = 'default';

    /** @var array<string, string> chat ids already confirmed with WhatsApp during this request */
    private array $resolved = [];

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
        private readonly string $session = self::DEFAULT_SESSION,
    ) {
    }

    public static function fromSettings(?SiteSetting $settings = null): self
    {
        $settings ??= SiteSetting::first();

        return new self(
            rtrim($settings?->waha_url ?: self::DEFAULT_URL, '/'),
            $settings?->waha_api_key,
            $settings?->waha_session ?: self::DEFAULT_SESSION,
        );
    }

    public function session(): string
    {
        return $this->session;
    }

    /**
     * Indonesian numbers to WhatsApp format: 0812-3456-789, +62 812..., 62812... -> 62812xxxx.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return strlen($digits) >= 9 && strlen($digits) <= 15 ? $digits : null;
    }

    public static function chatId(string $phone): string
    {
        $normalized = self::normalizePhone($phone)
            ?? throw new WahaException('Nomor WhatsApp tidak valid: '.$phone);

        return $normalized.'@c.us';
    }

    // ---- sessions -----------------------------------------------------------

    /** Session info, or null when the session has not been created on the server yet. */
    public function sessionInfo(): ?array
    {
        $response = $this->send(fn (PendingRequest $r) => $r->get("/api/sessions/{$this->session}"));

        return $response->status() === 404 ? null : $this->json($response);
    }

    /** POST /api/sessions/start: creates the session if it is missing, then starts it (one call, no pre-check). */
    public function upsertAndStart(): array
    {
        return $this->json($this->send(fn (PendingRequest $r) => $r->post('/api/sessions/start', ['name' => $this->session])));
    }

    public function createSession(): array
    {
        return $this->json($this->send(fn (PendingRequest $r) => $r->post('/api/sessions', [
            'name' => $this->session,
            'start' => true,
        ])));
    }

    public function start(): array
    {
        return $this->json($this->send(fn (PendingRequest $r) => $r->post("/api/sessions/{$this->session}/start")));
    }

    public function stop(): array
    {
        return $this->json($this->send(fn (PendingRequest $r) => $r->post("/api/sessions/{$this->session}/stop")));
    }

    public function logout(): array
    {
        return $this->json($this->send(fn (PendingRequest $r) => $r->post("/api/sessions/{$this->session}/logout")));
    }

    /** Base64 PNG of the pairing QR, or null when the session is not waiting for a scan. */
    public function qr(): ?array
    {
        $response = $this->send(fn (PendingRequest $r) => $r->get("/api/{$this->session}/auth/qr"));

        if ($response->status() === 404 || $response->status() === 422) {
            return null;
        }

        $data = $this->json($response);

        return isset($data['data']) ? ['mimetype' => $data['mimetype'] ?? 'image/png', 'data' => $data['data']] : null;
    }

    // ---- messages -----------------------------------------------------------

    /**
     * Asks WhatsApp whether the number is registered (GET /api/checkNumberStatus) and returns the
     * chat id to use. Engines that do not implement the check fall back to the plain number.
     */
    public function resolveChatId(string $phone): string
    {
        $fallback = self::chatId($phone);

        if (isset($this->resolved[$fallback])) {
            return $this->resolved[$fallback];
        }

        $response = $this->send(fn (PendingRequest $r) => $r->get('/api/checkNumberStatus', [
            'session' => $this->session,
            'phone' => self::normalizePhone($phone),
        ]));

        if (in_array($response->status(), [404, 405, 501], true)) {
            return $this->resolved[$fallback] = $fallback;
        }

        $data = $this->json($response);

        if (($data['numberExists'] ?? true) === false) {
            throw new WahaException('Nomor '.$phone.' tidak terdaftar di WhatsApp.');
        }

        return $this->resolved[$fallback] = $data['chatId'] ?? $fallback;
    }

    public function sendText(string $phone, string $text): array
    {
        $chatId = $this->resolveChatId($phone);

        return $this->json($this->send(fn (PendingRequest $r) => $r->post('/api/sendText', [
            'session' => $this->session,
            'chatId' => $chatId,
            'text' => $text,
        ])));
    }

    public function sendFile(string $phone, string $binary, string $filename, string $mimetype, ?string $caption = null): array
    {
        $chatId = $this->resolveChatId($phone);

        return $this->json($this->send(fn (PendingRequest $r) => $r->timeout(60)->post('/api/sendFile', [
            'session' => $this->session,
            'chatId' => $chatId,
            'file' => [
                'mimetype' => $mimetype,
                'filename' => $filename,
                'data' => base64_encode($binary),
            ],
            'caption' => $caption,
        ])));
    }

    // ---- internals ----------------------------------------------------------

    /** Runs one HTTP call and turns network failures into a readable WahaException. */
    private function send(callable $call): Response
    {
        if (blank($this->baseUrl)) {
            throw new WahaException('Alamat server WAHA belum diisi.');
        }

        $request = Http::baseUrl($this->baseUrl)
            ->withHeaders(array_filter(['X-Api-Key' => $this->apiKey]))
            ->acceptJson()
            ->asJson()
            ->timeout(20);

        try {
            return $call($request);
        } catch (ConnectionException $e) {
            throw new WahaException('Tidak bisa terhubung ke server WAHA ('.$this->baseUrl.').', null, $e);
        }
    }

    private function json(Response $response): array
    {
        if ($response->status() === 401 || $response->status() === 403) {
            throw new WahaException('API key WAHA ditolak. Periksa kembali API key di pengaturan.', $response->status());
        }

        if ($response->failed()) {
            $message = $response->json('message') ?? $response->json('error') ?? $response->reason();
            throw new WahaException('WAHA menjawab '.$response->status().': '.(is_array($message) ? json_encode($message) : $message), $response->status());
        }

        return $response->json() ?? [];
    }
}
