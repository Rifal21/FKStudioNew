<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\InvoiceWhatsApp;
use App\Services\Waha\WahaClient;
use App\Services\Waha\WahaException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppSettingsController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::first();

        return view('dashboard.whatsapp', [
            'settings' => $settings,
            'defaultUrl' => WahaClient::DEFAULT_URL,
            'defaultSession' => WahaClient::DEFAULT_SESSION,
            'defaultTemplate' => InvoiceWhatsApp::DEFAULT_TEMPLATE,
            'hasKey' => filled($settings->waha_api_key),
            'activeSession' => $settings->waha_session ?: WahaClient::DEFAULT_SESSION,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'waha_url' => 'required|url|max:255',
            'waha_api_key' => 'nullable|string|max:255',
            'waha_invoice_template' => 'nullable|string|max:2000',
        ]);

        $payload = [
            'waha_url' => rtrim($data['waha_url'], '/'),
            'waha_invoice_template' => $data['waha_invoice_template'] ?? null ?: null,
            'waha_enabled' => $request->boolean('waha_enabled'),
        ];

        // The stored key is never sent back to the browser, so blank means "keep it".
        if (filled($data['waha_api_key'] ?? null)) {
            $payload['waha_api_key'] = $data['waha_api_key'];
        }

        SiteSetting::first()->update($payload);

        return back()->with('success', 'Pengaturan WhatsApp disimpan.');
    }

    // ---- sessions (managed from here, no need to open the WAHA dashboard) ----

    public function sessions(): JsonResponse
    {
        return $this->call(function (WahaClient $client) {
            $sessions = collect($client->listSessions())->map(fn ($s) => [
                'name' => $s['name'],
                'status' => $s['status'] ?? 'UNKNOWN',
                'me' => $s['me'] ?? null,
            ])->sortBy('name')->values();

            return ['sessions' => $sessions, 'active' => $this->activeSession()];
        });
    }

    public function createSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9_-]+$/'],
        ], [
            'name.regex' => 'Nama sesi hanya boleh huruf, angka, strip, dan garis bawah.',
        ]);

        return $this->call(function (WahaClient $client) use ($data) {
            $client->forSession($data['name'])->createSession();

            // The first session you create becomes the sender, so sending works right after scanning.
            $settings = SiteSetting::first();
            if (blank($settings->waha_session)) {
                $settings->update(['waha_session' => $data['name']]);
            }

            return ['ok' => true, 'name' => $data['name']];
        });
    }

    public function qr(string $session): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => ['qr' => $client->forSession($session)->qr()]);
    }

    public function start(string $session): JsonResponse
    {
        return $this->call(function (WahaClient $client) use ($session) {
            $client = $client->forSession($session);

            try {
                $client->upsertAndStart();
            } catch (WahaException $e) {
                // Older WAHA builds have no /api/sessions/start: create or start the session by hand.
                if (! in_array($e->status, [404, 405], true)) {
                    throw $e;
                }

                $client->sessionInfo() === null ? $client->createSession() : $client->start();
            }

            return ['ok' => true];
        });
    }

    /** For a FAILED or stuck session: restart it, or stop then start on servers without /restart. */
    public function restart(string $session): JsonResponse
    {
        return $this->call(function (WahaClient $client) use ($session) {
            $client = $client->forSession($session);

            try {
                $client->restart();
            } catch (WahaException $e) {
                if (! in_array($e->status, [404, 405], true)) {
                    throw $e;
                }

                try {
                    $client->stop();
                } catch (WahaException) {
                    // A crashed session may refuse to stop; starting it is what matters.
                }

                $client->start();
            }

            return ['ok' => true];
        });
    }

    public function stop(string $session): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => $client->forSession($session)->stop() + ['ok' => true]);
    }

    public function logout(string $session): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => $client->forSession($session)->logout() + ['ok' => true]);
    }

    public function destroySession(string $session): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => $client->forSession($session)->deleteSession() + ['ok' => true]);
    }

    /** Chooses which session sends invoices. */
    public function activate(string $session): JsonResponse
    {
        SiteSetting::first()->update(['waha_session' => $session]);

        return response()->json(['ok' => true, 'active' => $session]);
    }

    public function test(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string|max:30']);

        return $this->call(function (WahaClient $client) use ($request) {
            $client->sendText($request->phone, 'Tes dari FKStudio: WhatsApp sudah tersambung. ✅');

            return ['ok' => true];
        });
    }

    private function activeSession(): string
    {
        return SiteSetting::first()->waha_session ?: WahaClient::DEFAULT_SESSION;
    }

    private function call(callable $fn): JsonResponse
    {
        try {
            return response()->json($fn(WahaClient::fromSettings()));
        } catch (WahaException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
