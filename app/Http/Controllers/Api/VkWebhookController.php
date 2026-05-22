<?php

namespace App\Http\Controllers\Api;

use App\Application\Vk\Repositories\VkChannelRepository;
use App\Application\Vk\Services\VkIncomingMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
                    new OA\Property(property: 'object', type: 'object', nullable: true),
                    new OA\Property(property: 'secret', type: 'string', nullable: true),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'ok',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'ok', type: 'boolean', example: true),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request, VkIncomingMessageService $service, VkChannelRepository $channelRepo): JsonResponse|string
    {
        $payload = $request->json()->all();
        if (empty($payload)) {
            $rawPayload = json_decode((string) $request->getContent(), true);
            if (is_array($rawPayload)) {
                $payload = $rawPayload;
            }
        }

        $groupId = isset($payload['group_id']) ? (int) $payload['group_id'] : null;

        if (($payload['type'] ?? null) === 'confirmation' && $groupId !== null) {
            $channel = $channelRepo->findByGroupId($groupId);
            $code = $channel?->confirmation_code ?? config('services.vk.confirmation_code', '');
            return response($code, 200)->header('Content-Type', 'text/plain');
        }

        $channel = $groupId !== null ? $channelRepo->findByGroupId($groupId) : null;
        $tag = $channel?->tag;

        $service->store($payload, $tag);

        return response()->json(['ok' => true]);
    }
}
