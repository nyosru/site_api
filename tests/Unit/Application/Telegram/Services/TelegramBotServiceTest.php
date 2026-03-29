<?php

namespace Tests\Unit\Application\Telegram\Services;

use App\Application\Telegram\Services\VkBotService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramBotServiceTest extends TestCase
{
    public function test_send_message_calls_telegram_api_with_expected_payload(): void
    {
        Http::fake([
            'https://api.telegram.org/bottest-token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $service = new VkBotService();
        $service->sendMessage('test-token', 123456, 'hello');

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === 123456
                && $request['text'] === 'hello';
        });
    }

    public function test_set_webhook_returns_true_when_telegram_confirms(): void
    {
        Http::fake([
            'https://api.telegram.org/bottest-token/setWebhook' => Http::response(['ok' => true], 200),
        ]);

        $service = new VkBotService();
        $result = $service->setWebhook('test-token', 'https://example.test/api/telegram');

        $this->assertTrue($result);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/setWebhook'
                && $request['url'] === 'https://example.test/api/telegram';
        });
    }
}
