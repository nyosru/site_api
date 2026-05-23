<?php

namespace App\Http\Controllers\Api;

use App\Application\Vk\Services\VkIncomingMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

final class VkIncomingController extends Controller
{
    #[OA\Get(
        path: '/api/vk/incoming',
        operationId: 'apiVkIncomingGet',
        summary: 'Получить неотданные запросы от VK',
        description: 'Возвращает список входящих запросов от VK, которые ещё не были переданы другому сервису. После возврата помечает их как доставленные.',
        tags: ['VK'],
        parameters: [
            new OA\Parameter(name: 'channel', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'my_channel'),
            new OA\Parameter(name: 'preview', in: 'query', required: false, schema: new OA\Schema(type: 'boolean'), description: 'Если true — вернуть сообщения без отметки о доставке'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список сообщений',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'channel', type: 'string', nullable: true, example: 'my_channel'),
                            new OA\Property(property: 'payload', type: 'object'),
                            new OA\Property(property: 'received_at', type: 'string', format: 'date-time'),
                        ],
                        type: 'object'
                    )
                )
            ),
        ]
    )]
    public function __invoke(Request $request, VkIncomingMessageService $service): JsonResponse
    {
        $channel = $request->query('channel');
        $preview = $request->boolean('preview');

        $messages = $service->consumeUndelivered($channel, $preview);

        return response()->json($messages);
    }
}
