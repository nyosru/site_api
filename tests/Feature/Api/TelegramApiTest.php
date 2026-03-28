<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        @mkdir(storage_path('app'), 0777, true);
        file_put_contents(storage_path('app/telegram.registered.trigger'), (string) time());
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('app/telegram.registered.trigger'));
        parent::tearDown();
    }

    public function test_telegram_api_returns_422_when_token_missing_and_json_answer_requested(): void
    {
        $response = $this->postJson('/api/telegram', [
            'answer' => 'json',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'res' => false,
                'text' => 'token is required',
            ]);
    }

    public function test_telegram_api_sends_signed_outgoing_message_to_targets_and_admin(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $domain = 'example.com';

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'domain' => $domain,
            'msg' => 'line1<br>line2',
            'id' => [111, 222],
            's' => md5($domain),
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        Http::assertSentCount(3);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 111
                && str_contains((string) $request['text'], 'example.com')
                && str_contains((string) $request['text'], 'line1')
                && str_contains((string) $request['text'], 'line2');
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 222;
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 360209578;
        });
    }

    public function test_telegram_api_handles_webhook_get_my_id_command(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'message' => [
                'text' => '/get_my_id',
                'from' => [
                    'id' => 987654,
                    'username' => 'tester',
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 987654
                && str_contains((string) $request['text'], '987654');
        });

        $this->assertDatabaseHas('telegram_in_msg', [
            'telegram_user_id' => 987654,
            'text' => '/get_my_id',
            'command' => '/get_my_id',
            'is_start' => 0,
        ]);
    }

    public function test_telegram_api_handles_webhook_start_command_and_sends_two_messages(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'message' => [
                'text' => '/start',
                'from' => [
                    'id' => 123321,
                    'username' => 'start_user',
                    'first_name' => 'Ivan',
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        Http::assertSentCount(2);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 360209578
                && str_contains((string) $request['text'], 'новый старт')
                && str_contains((string) $request['text'], 'username: start_user');
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 123321
                && str_contains((string) $request['text'], 'Здравствуйте Ваш id: 123321');
        });

        $this->assertDatabaseHas('telegram_in_msg', [
            'telegram_user_id' => 123321,
            'text' => '/start',
            'command' => '/start',
            'is_start' => 1,
        ]);
    }

    public function test_telegram_api_handles_link_to_alfa_command_and_sends_two_messages(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'message' => [
                'text' => '/link-to-alfa',
                'from' => [
                    'id' => 222333,
                    'username' => 'alfa_user',
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        Http::assertSentCount(2);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 360209578
                && str_contains((string) $request['text'], 'дай ссылку на альфа банк: запросил #222333 @alfa_user');
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 222333
                && str_contains((string) $request['text'], 'https://php-cat.com/money')
                && str_contains((string) $request['text'], 'alfabank.ru/everyday/debit-cards/alfacard-short');
        });

        $this->assertDatabaseHas('telegram_in_msg', [
            'telegram_user_id' => 222333,
            'text' => '/link-to-alfa',
            'command' => '/link-to-alfa',
            'is_start' => 0,
        ]);
    }

    public function test_telegram_api_sends_message_to_admin_when_signature_is_md5_of_one(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'domain' => 'example.com',
            'msg' => 'admin only',
            's' => md5('1'),
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        Http::assertSentCount(1);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 360209578
                && str_contains((string) $request['text'], 'admin only');
        });
    }

    public function test_telegram_api_stores_regular_incoming_message_text(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson('/api/telegram', [
            'token' => 'test-token',
            'answer' => 'json',
            'message' => [
                'text' => 'hello bot',
                'from' => [
                    'id' => 777888,
                    'username' => 'regular_user',
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        $this->assertDatabaseHas('telegram_in_msg', [
            'telegram_user_id' => 777888,
            'text' => 'hello bot',
            'command' => null,
            'is_start' => 0,
        ]);
    }
}
