<?php

namespace App\Http\Controllers\Api;

use App\Application\Vk\Repositories\VkChannelRepository;
use App\Application\Vk\Services\VkIncomingMessageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'VK', description: 'VK API integration')]
final class VkWebhookController extends Controller
{
    #[OA\Post(
        path: '/api/vk/webhook',
        operationId: 'apiVkWebhook',
        summary: 'Принять входящий callback от VK API',
        description: 'Сохраняет входящий JSON-запрос от VK с пометкой "не доставлено". Для confirmation type возвращает confirmation code из БД по group_id.',
        tags: ['VK'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'type', type: 'string', example: 'message_new'),
                    new OA\Property(property: 'group_id', type: 'integer', example: 236808681),
                    new OA\Property(property: 'secret', type: 'string', nullable: true, example: 'your_secret_key'),
                    new OA\Property(
                        property: 'object',
                        type: 'object',
                        nullable: true,
                        example: ['message' => ['id' => 1, 'text' => 'test']]
                    ),
                ],
                type: 'object',
                example: [
                    'type' => 'message_new',
                    'group_id' => 236808681,
                    'secret' => 'your_secret_key',
                    'object' => ['message' => ['id' => 1, 'text' => 'test']],
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK. Для type=confirmation — строка с кодом подтверждения. Для остальных — "ok".',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'ok', type: 'boolean', example: true),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[OA\Examples(
        example: 'confirmation',
        summary: 'Confirmation request',
        description: 'VK отправляет этот запрос для подтверждения сервера. Ответом должна быть строка с confirmation code.',
        value: ['type' => 'confirmation', 'group_id' => 236808681],
    )]
    #[OA\Examples(
        example: 'message_new',
        summary: 'New message event',
        description: 'Обычное входящее сообщение от пользователя VK.',
        value: [
            'type' => 'message_new',
            'group_id' => 236808681,
            'secret' => 'your_secret_key',
            'object' => [
                'message' => [
                    'id' => 42,
                    'date' => 1680000000,
                    'text' => 'Привет!',
                    'from_id' => 123456789,
                ],
            ],
        ],
    )]
    public function __invoke(Request $request, VkIncomingMessageService $service, VkChannelRepository $channelRepo): string
    {
        $payload = $request->json()->all();
        if (empty($payload)) {
            $rawPayload = json_decode((string) $request->getContent(), true);
            if (is_array($rawPayload)) {
                $payload = $rawPayload;
            }
        }

        Log::channel('vk')->info('vk webhook received', ['payload' => $payload]);

        $groupId = isset($payload['group_id']) ? (int) $payload['group_id'] : null;
        $channel = $groupId !== null ? $channelRepo->findByGroupId($groupId) : null;

        $incomingSecret = (string) ($payload['secret'] ?? '');
        $validation = [
            'channel_found' => $channel !== null,
            'channel_tag' => $channel?->tag,
            'channel_name' => $channel?->name,
        ];

        if ($channel !== null && $channel->secret !== null) {
            $validation['secret_expected'] = true;
            $validation['secret_valid'] = $incomingSecret === $channel->secret;
        } else {
            $validation['secret_expected'] = false;
            $validation['secret_valid'] = null;
        }

        if (($payload['type'] ?? null) === 'confirmation' && $groupId !== null) {
            return $channel?->confirmation_code ?? config('services.vk.confirmation_code', '');
        }

        $service->store($payload, $channel?->tag, $validation);

        return 'ok';
    }
}
