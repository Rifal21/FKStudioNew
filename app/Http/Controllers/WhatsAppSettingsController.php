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
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'waha_url' => 'required|url|max:255',
            'waha_session' => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9_-]+$/'],
            'waha_api_key' => 'nullable|string|max:255',
            'waha_invoice_template' => 'nullable|string|max:2000',
        ], [
            'waha_session.regex' => 'Nama sesi hanya boleh huruf, angka, strip, dan garis bawah.',
        ]);

        $settings = SiteSetting::first();

        $payload = [
            'waha_url' => rtrim($data['waha_url'], '/'),
            'waha_session' => $data['waha_session'],
            'waha_invoice_template' => $data['waha_invoice_template'] ?? null ?: null,
            'waha_enabled' => $request->boolean('waha_enabled'),
        ];

        // The stored key is never sent back to the browser, so blank means "keep it".
        if (filled($data['waha_api_key'] ?? null)) {
            $payload['waha_api_key'] = $data['waha_api_key'];
        }

        $settings->update($payload);

        return back()->with('success', 'Pengaturan WhatsApp disimpan.');
    }

    public function status(): JsonResponse
    {
        return $this->call(function (WahaClient $client) {
            $info = $client->sessionInfo();

            return [
                'exists' => $info !== null,
                'status' => $info['status'] ?? 'NOT_CREATED',
                'me' => $info['me'] ?? null,
            ];
        });
    }

    public function qr(): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => ['qr' => $client->qr()]);
    }

    /** Creates the session on the WAHA server if needed and starts it. */
    public function start(): JsonResponse
    {
        return $this->call(function (WahaClient $client) {
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

    public function stop(): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => $client->stop() + ['ok' => true]);
    }

    public function logout(): JsonResponse
    {
        return $this->call(fn (WahaClient $client) => $client->logout() + ['ok' => true]);
    }

    public function test(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string|max:30']);

        return $this->call(function (WahaClient $client) use ($request) {
            $client->sendText($request->phone, "Tes dari FKStudio: WhatsApp sudah tersambung. ✅");

            return ['ok' => true];
        });
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
