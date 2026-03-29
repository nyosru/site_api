<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TelegramWebhookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_route_stores_incoming_message_and_returns_ok(): void
    {
        Log::spy();

        $response = $this->postJson('/api/telegram/webhook', [
            'update_id' => 100500,
            'message' => [
                'message_id' => 77,
                'text' => '/start',
                'from' => [
                    'id' => 424242,
                    'username' => 'demo_user',
                    'first_name' => 'Demo',
                    'language_code' => 'ru',
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'res' => true,
        ]);

        $this->assertDatabaseHas('telegram_in_msg', [
            'telegram_user_id' => 424242,
            'telegram_message_id' => 77,
            'username' => 'demo_user',
            'text' => '/start',
            'command' => '/start',
            'is_start' => 1,
        ]);

        Log::shouldHaveReceived('info')->once();
    }
}
