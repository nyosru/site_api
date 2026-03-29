<?php

namespace App\Http\Controllers\Api;

use App\Application\Vk\DTO\VkSendMessageRequestDto;
use App\Application\Vk\Services\VkMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use RuntimeException;


final class VkSendMessageController extends Controller
{


#[OA\Get(
    path: '/api/vk/send',
    operationId: 'apiVkSendMessageGet',
    summary: 'Отправить сообщение пользователю VK (через GET)',
    description: 'То же, что и POST, но параметры передаются в query-строке.',
    tags: ['VK'],
    parameters: [
        new OA\Parameter(name: 'group_name', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'main_group'),
        new OA\Parameter(name: 'token', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'vk1.a.xxxxx'),
        new OA\Parameter(name: 'user_id', in: 'query', required: true, schema: new OA\Schema(type: 'string'), example: '12345,67890'),
        new OA\Parameter(name: 'message', in: 'query', required: true, schema: new OA\Schema(type: 'string'), example: 'Привет из API'),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Сообщение отправлено',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: true),
                    new OA\Property(property: 'group', type: 'string', nullable: true, example: 'main_group'),
                    new OA\Property(property: 'sent_user_ids', type: 'array', items: new OA\Items(type: 'integer')),
                ],
                type: 'object'
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Ошибка валидации',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: false),
                    new OA\Property(property: 'error', type: 'string', example: 'Validation failed'),
                ],
                type: 'object'
            )
        ),
    ]
)]
#[OA\Post(
    path: '/api/vk/send',
    operationId: 'apiVkSendMessagePost',
    summary: 'Отправить сообщение пользователю VK от имени группы (через POST)',
    description: 'Для авторизации передайте либо название группы, либо token. Подробности в описании.',
    tags: ['VK'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['message'],
            properties: [
                new OA\Property(property: 'group_name', type: 'string', nullable: true, example: 'main_group'),
                new OA\Property(property: 'token', type: 'string', nullable: true, example: 'vk1.a.xxxxx'),
                new OA\Property(
                    property: 'user_id',
                    description: 'ID пользователя: число, массив ID или строка "1,2,3"',
                    nullable: true,
                    oneOf: [
                        new OA\Schema(type: 'integer', example: 12345),
                        new OA\Schema(type: 'string', example: '12345,67890'),
                        new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
                    ]
                ),
                new OA\Property(property: 'message', type: 'string', example: 'Привет из API'),
            ],
            type: 'object'
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Сообщение отправлено',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: true),
                    new OA\Property(property: 'group', type: 'string', nullable: true, example: 'main_group'),
                    new OA\Property(property: 'sent_user_ids', type: 'array', items: new OA\Items(type: 'integer')),
                ],
                type: 'object'
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Ошибка валидации или доменная ошибка',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: false),
                    new OA\Property(property: 'error', type: 'string', example: 'Token for group not found: main_group'),
                ],
                type: 'object'
            )
        ),
        new OA\Response(
            response: 500,
            description: 'Внутренняя ошибка сервера',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ok', type: 'boolean', example: false),
                    new OA\Property(property: 'error', type: 'string', example: 'Internal server error'),
                ],
                type: 'object'
            )
        ),
    ]
)]

    public function __invoke(Request $request, VkMessageService $service): JsonResponse
    {
        $rawUserId = $request->input('user_id');
        $groupName = $request->input('group_name');

        $validator = Validator::make($request->all(), [
            'group_name' => ['nullable', 'string'],
            'token' => ['nullable', 'string'],
            'user_id' => ['required'],
            'message' => ['required', 'string'],
        ]);

        $validator->after(function ($validator) use ($groupName, $request, $rawUserId): void {

            $group = trim((string) $groupName);
            $token = trim((string) $request->input('token', ''));

            if ($group === '' && $token === '') {
                $validator->errors()->add('group', 'Either group or token is required.');
            }

            $userIds = $this->parseUserIds($rawUserId);
            if ($userIds === []) {
                $validator->errors()->add('user_id', 'User id is required and must contain numeric ids.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $dto = new VkSendMessageRequestDto(
            groupName: $this->nullIfEmpty($groupName),
            token: $this->nullIfEmpty($request->input('token')),
            userIds: $this->parseUserIds($rawUserId),
            message: (string) $request->input('message'),
        );

        try {
            $result = $service->send($dto);
        } catch (RuntimeException $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'group' => $result['group'],
            'sent_user_ids' => $result['sent_user_ids'],
        ]);
    }

    /**
     * @return array<int>
     */
    private function parseUserIds(mixed $raw): array
    {
        if (is_int($raw) || is_float($raw)) {
            return [(int) $raw];
        }

        if (is_string($raw)) {
            $parts = array_filter(array_map('trim', explode(',', $raw)), static fn (string $v): bool => $v !== '');

            return $this->normalizeNumericIds($parts);
        }

        if (is_array($raw)) {
            return $this->normalizeNumericIds($raw);
        }

        return [];
    }

    /**
     * @param array<mixed> $rawIds
     * @return array<int>
     */
    private function normalizeNumericIds(array $rawIds): array
    {
        $result = [];

        foreach ($rawIds as $rawId) {
            if (is_int($rawId)) {
                if ($rawId > 0) {
                    $result[] = $rawId;
                }

                continue;
            }

            if (is_string($rawId)) {
                $trimmed = trim($rawId);
                if ($trimmed !== '' && ctype_digit($trimmed)) {
                    $numeric = (int) $trimmed;
                    if ($numeric > 0) {
                        $result[] = $numeric;
                    }
                }
            }
        }

        return array_values(array_unique($result));
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
