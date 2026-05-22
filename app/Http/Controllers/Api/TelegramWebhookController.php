<?php

namespace App\Http\Controllers\Api;

use App\Models\TelegramInMsg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

final class TelegramWebhookController extends Controller
{
    #[OA\Post(
        path: '/api/telegram/webhook',
        operationId: 'apiTelegramWebhookPost',
        summary: 'Receive Telegram webhook update',
        description: 'Dedicated endpoint for Telegram webhook updates. Reads raw JSON, extracts message data, stores in telegram_in_msg.',
        tags: ['Telegram'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'update_id', type: 'integer', example: 123456789),
                    new OA\Property(
                        property: 'message',
                        type: 'object',
                        nullable: true,
                        properties: [
                            new OA\Property(property: 'message_id', type: 'integer', example: 1),
                            new OA\Property(property: 'text', type: 'string', nullable: true, example: '/start'),
                            new OA\Property(
                                property: 'from',
                                type: 'object',
                                nullable: true,
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 123456789),
                                    new OA\Property(property: 'username', type: 'string', nullable: true, example: 'username'),
                                    new OA\Property(property: 'first_name', type: 'string', nullable: true, example: 'Ivan'),
                                    new OA\Property(property: 'last_name', type: 'string', nullable: true, example: 'Ivanov'),
                                    new OA\Property(property: 'language_code', type: 'string', nullable: true, example: 'ru'),
                                ],
                            ),
                        ],
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Webhook processed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'res', type: 'boolean', example: true),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
//        $payload = $request->json()->all();
//        if (empty($payload)) {
//            $rawPayload = json_decode((string) $request->getContent(), true);
//            if (is_array($rawPayload)) {
//                $payload = $rawPayload;
//            }
//        }

        $v1 = file_get_contents("php://input");
        $payload =
        $array =
        $v = json_decode($v1, true);

        $message = $payload['message'] ?? [];
        $from = $message['from'] ?? [];
        $text = (string) ($message['text'] ?? '');
//        $text = (string) serialize($payload);
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
