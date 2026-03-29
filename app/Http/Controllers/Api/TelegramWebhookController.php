<?php

namespace App\Http\Controllers\Api;

use App\Models\TelegramInMsg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        if (empty($payload)) {
            $rawPayload = json_decode((string) $request->getContent(), true);
            if (is_array($rawPayload)) {
                $payload = $rawPayload;
            }
        }

        $message = $payload['message'] ?? [];
        $from = $message['from'] ?? [];
        $text = (string) ($message['text'] ?? '');
        $command = Str::startsWith($text, '/') ? strtok($text, ' ') : null;

        TelegramInMsg::query()->create([
            'telegram_user_id' => isset($from['id']) ? (int) $from['id'] : null,
            'telegram_message_id' => isset($message['message_id']) ? (int) $message['message_id'] : null,
            'username' => $from['username'] ?? null,
            'first_name' => $from['first_name'] ?? null,
            'last_name' => $from['last_name'] ?? null,
            'language_code' => $from['language_code'] ?? null,
            'text' => $text !== '' ? $text : null,
            'command' => $command ?: null,
            'is_start' => $command === '/start',
            'bot_token_hash' => null,
            'payload' => $payload,
            'received_at' => now(),
        ]);

        Log::info('telegram webhook received', [
            'update_id' => $payload['update_id'] ?? null,
            'telegram_user_id' => $from['id'] ?? null,
            'username' => $from['username'] ?? null,
            'command' => $command,
            'text' => $text !== '' ? $text : null,
        ]);

        return response()->json([
            'res' => true,
        ]);
    }
}
