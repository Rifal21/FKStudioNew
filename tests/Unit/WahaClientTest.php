<?php

namespace Tests\Unit;

use App\Services\Waha\WahaClient;
use App\Services\Waha\WahaException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WahaClientTest extends TestCase
{
    #[DataProvider('phones')]
    public function test_it_normalizes_indonesian_numbers(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, WahaClient::normalizePhone($input));
    }

    public static function phones(): array
    {
        return [
            ['0812-3456-7890', '6281234567890'],
            ['+62 812 3456 7890', '6281234567890'],
            ['6281234567890', '6281234567890'],
            ['81234567890', '6281234567890'],
            ['', null],
            [null, null],
            ['12', null],
        ];
    }

    public function test_it_reports_a_rejected_api_key_in_plain_language(): void
    {
        Http::fake(['*' => Http::response(['message' => 'Unauthorized'], 401)]);

        $this->expectException(WahaException::class);
        $this->expectExceptionMessage('API key');

        (new WahaClient('https://waha.test', 'bad'))->sessionInfo();
    }

    public function test_session_info_is_null_when_the_session_does_not_exist_yet(): void
    {
        Http::fake(['*' => Http::response(['message' => 'not found'], 404)]);

        $this->assertNull((new WahaClient('https://waha.test', 'k', 'fk'))->sessionInfo());
    }

    public function test_it_creates_a_session_and_reads_the_qr(): void
    {
        Http::fake([
            '*/api/sessions' => Http::response(['name' => 'fk', 'status' => 'STARTING']),
            '*/api/fk/auth/qr' => Http::response(['mimetype' => 'image/png', 'data' => 'AAAA']),
        ]);

        $client = new WahaClient('https://waha.test', 'k', 'fk');

        $this->assertSame('STARTING', $client->createSession()['status']);
        $this->assertSame('AAAA', $client->qr()['data']);
        Http::assertSent(fn ($r) => $r->method() === 'POST' && $r['name'] === 'fk' && $r['start'] === true);
    }
}
