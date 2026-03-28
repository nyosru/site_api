<?php

namespace Tests\Feature\Web;

use App\Models\TelegramInMsg;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramLogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_telegram_log_page_is_available_and_shows_saved_message(): void
    {
        TelegramInMsg::query()->create([
            'telegram_user_id' => 1001,
            'telegram_message_id' => 55,
            'username' => 'demo_user',
            'first_name' => 'Demo',
            'last_name' => null,
            'language_code' => 'ru',
            'text' => 'hello from test',
            'command' => null,
            'is_start' => false,
            'bot_token_hash' => hash('sha256', 'test-token'),
            'payload' => ['message' => ['text' => 'hello from test']],
            'received_at' => now(),
        ]);

        $response = $this->get('/telegram/log');

        $response->assertOk()
            ->assertSee('Telegram Log')
            ->assertSee('hello from test')
            ->assertSee('demo_user');
    }
}
