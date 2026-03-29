<?php

namespace App\Application\Telegram\Services;

use Illuminate\Support\Facades\Http;

final class VkBotService
{
    public function sendMessage(string $token, int $chatId, string $text): void
    {
        Http::asForm()->post($this->baseUrl($token).'/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ])->throw();
    }

    public function setWebhook(string $token, string $url): bool
    {
        $response = Http::asForm()->post($this->baseUrl($token).'/setWebhook', [
            'url' => $url,
        ])->throw();

        return (bool) ($response->json('ok') ?? false);
    }

    private function baseUrl(string $token): string
    {
        return 'https://api.telegram.org/bot'.$token;
    }
}
