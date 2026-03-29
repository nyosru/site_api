<?php

namespace App\Http\Controllers\Api;

use App\Application\Telegram\Services\VkBotService;
use App\Models\TelegramInMsg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use OpenApi\Attributes as OA;
use Illuminate\Support\Str;
use Throwable;

final class VkController extends Controller
{
//    private const ADMIN_ID = 360209578;
//    private const REGISTER_TRIGGER_PATH = 'telegram.registered.trigger';

//    #[OA\Get(
//        path: '/api/telegram',
//        operationId: 'apiTelegramGet',
//        summary: 'Send Telegram message via signed query params',
//        description: 'Legacy-compatible endpoint. Use msg/domain/s parameters to send outgoing messages.',
//        tags: ['Telegram'],
//        parameters: [
//            new OA\Parameter(name: 'token', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
//            new OA\Parameter(name: 'msg', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
//            new OA\Parameter(name: 'domain', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
//            new OA\Parameter(name: 's', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
//            new OA\Parameter(
//                name: 'id',
//                in: 'query',
//                required: false,
//                description: 'Telegram chat id, can be repeated as array id[]=1&id[]=2',
//                schema: new OA\Schema(type: 'string')
//            ),
//            new OA\Parameter(
//                name: 'answer',
//                in: 'query',
//                required: false,
//                description: 'Set to json to force JSON response',
//                schema: new OA\Schema(type: 'string', enum: ['json'])
//            ),
//        ],
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: 'Command handled',
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: 'res', type: 'boolean', example: true),
//                        new OA\Property(property: 'text', type: 'string', nullable: true),
//                        new OA\Property(property: 'error', type: 'string', nullable: true),
//                    ],
//                    type: 'object'
//                )
//            ),
//            new OA\Response(
//                response: 422,
//                description: 'Validation-like failure, for example missing token',
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: 'res', type: 'boolean', example: false),
//                        new OA\Property(property: 'text', type: 'string', example: 'token is required'),
//                    ],
//                    type: 'object'
//                )
//            ),
//        ]
//    )]
//    #[OA\Post(
//        path: '/api/telegram',
//        operationId: 'apiTelegramPost',
//        summary: 'Telegram webhook handler and sender',
//        description: 'Accepts webhook updates from Telegram and also supports outgoing message mode with legacy payload fields.',
//        tags: ['Telegram'],
//        requestBody: new OA\RequestBody(
//            required: false,
//            content: new OA\JsonContent(
//                properties: [
//                    new OA\Property(property: 'token', type: 'string', nullable: true),
//                    new OA\Property(property: 'msg', type: 'string', nullable: true),
//                    new OA\Property(property: 'domain', type: 'string', nullable: true),
//                    new OA\Property(property: 's', type: 'string', nullable: true),
//                    new OA\Property(
//                        property: 'id',
//                        type: 'array',
//                        items: new OA\Items(type: 'integer'),
//                        nullable: true
//                    ),
//                    new OA\Property(property: 'answer', type: 'string', nullable: true, example: 'json'),
//                    new OA\Property(
//                        property: 'message',
//                        properties: [
//                            new OA\Property(property: 'text', type: 'string', nullable: true, example: '/start'),
//                            new OA\Property(
//                                property: 'from',
//                                properties: [
//                                    new OA\Property(property: 'id', type: 'integer', example: 123456789),
//                                    new OA\Property(property: 'username', type: 'string', nullable: true, example: 'username'),
//                                    new OA\Property(property: 'first_name', type: 'string', nullable: true, example: 'Ivan'),
//                                ],
//                                type: 'object',
//                                nullable: true
//                            ),
//                        ],
//                        type: 'object',
//                        nullable: true
//                    ),
//                ],
//                type: 'object'
//            )
//        ),
//        responses: [
//            new OA\Response(
//                response: 200,
//                description: 'Webhook handled or message processed',
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: 'res', type: 'boolean', example: true),
//                        new OA\Property(property: 'text', type: 'string', nullable: true),
//                        new OA\Property(property: 'error', type: 'string', nullable: true),
//                    ],
//                    type: 'object'
//                )
//            ),
//            new OA\Response(
//                response: 422,
//                description: 'Missing required token',
//                content: new OA\JsonContent(
//                    properties: [
//                        new OA\Property(property: 'res', type: 'boolean', example: false),
//                        new OA\Property(property: 'text', type: 'string', example: 'token is required'),
//                    ],
//                    type: 'object'
//                )
//            ),
//        ]
//    )]
    public function __invoke(Request $request, VkBotService $service ): JsonResponse|string
    {
//        date_default_timezone_set('Asia/Yekaterinburg');
//
//        $payload = $request->json()->all();
//        if (empty($payload)) {
//            $rawPayload = json_decode((string) $request->getContent(), true);
//            if (is_array($rawPayload)) {
//                $payload = $rawPayload;
//            }
//        }
//
//        $isIncomingWebhook = !empty($payload['message']) || isset($payload['update_id']);
//        $token = (string) ($payload['token'] ?? $request->query('token') ?? config('services.telegram.bot_token', ''));
//        $answerJsonRequested = ($payload['answer'] ?? $request->query('answer')) === 'json';
//
//        if ($isIncomingWebhook && !empty($payload['message'])) {
//            $this->storeIncomingMessage($payload, $token !== '' ? $token : null);
//        }
//
//        if ($token === '' && !$isIncomingWebhook) {
//            return $this->response($answerJsonRequested, ['res' => false, 'text' => 'token is required'], 422);
//        }

        try {
//            if ($token !== '' && !$isIncomingWebhook && !$this->isWebhookRegistered()) {
//                $registered = $telegram->setWebhook($token, $request->getSchemeAndHttpHost().'/api/telegram');
//
//                if ($registered) {
//                    File::put(storage_path('app/'.self::REGISTER_TRIGGER_PATH), (string) time());
//                }
//            }
//
//            $messageText = (string) ($payload['msg'] ?? $request->query('msg') ?? '');
//            if ($messageText !== '') {
//                return $this->handleOutgoingMessage(
//                    request: $request,
//                    payload: $payload,
//                    text: $messageText,
//                    telegram: $telegram,
//                    token: $token,
//                    answerJsonRequested: $answerJsonRequested,
//                );
//            }
//
//            return $this->handleIncomingWebhook(
//                payload: $payload,
//                telegram: $telegram,
//                token: $token,
//                answerJsonRequested: $answerJsonRequested,
//            );
        } catch (Throwable $e) {
            $message = $e->getMessage().' #'.$e->getCode();
            if ($answerJsonRequested) {
                return response()->json(['res' => false, 'error' => $message], 500);
            }

            return $message;
        }
    }

//    private function handleOutgoingMessage(
//        Request $request,
//        array $payload,
//        string $text,
//        TelegramBotService $telegram,
//        string $token,
//        bool $answerJsonRequested
//    ): JsonResponse|string {
//        $domainSource = (string) ($request->query('domain') ?? $payload['domain'] ?? '');
//        $domain = str_contains($domainSource, 'xn--') && function_exists('idn_to_utf8')
//            ? (idn_to_utf8($domainSource) ?: $domainSource)
//            : $domainSource;
//
//        $formattedText = str_replace(
//            ['<Br/>', '<Br />', '<br/>', '<br />', '<br>', '<br >'],
//            PHP_EOL,
//            $text
//        );
//        $message = $domain.PHP_EOL.$formattedText;
//
//        $signature = $request->query('s') ?? $payload['s'] ?? null;
//
//        if ($signature == md5($domainSource)) {
//            $toIds = $this->extractTargetIds($request, $payload);
//            $toIds[] = self::ADMIN_ID;
//            $toIds = array_values(array_unique($toIds));
//
//            foreach ($toIds as $id) {
//                if ($id > 0) {
//                    $telegram->sendMessage($token, $id, $message);
//                }
//            }
//
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        if ($signature == md5('1') || $signature == 1) {
//            $telegram->sendMessage($token, self::ADMIN_ID, $message);
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        return $this->response($answerJsonRequested, ['res' => false, 'text' => 'no super var']);
//    }
//
//    private function handleIncomingWebhook(
//        array $payload,
//        TelegramBotService $telegram,
//        string $token,
//        bool $answerJsonRequested
//    ): JsonResponse|string {
//        $incomingText = (string) ($payload['message']['text'] ?? '');
//        $from = $payload['message']['from'] ?? [];
//        $fromId = (int) ($from['id'] ?? 0);
//        $username = (string) ($from['username'] ?? '');
//
//        if ($token === '') {
//            return $this->response($answerJsonRequested, ['res' => true, 'text' => 'message logged, bot token missing']);
//        }
//
//        if ($incomingText === '/get_my_id' && $fromId > 0) {
//            $telegram->sendMessage($token, $fromId, 'Ваш id: '.$fromId);
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        if ($incomingText === '/start' && $fromId > 0) {
//            $lines = ['новый старт', ''];
//            foreach ($from as $key => $value) {
//                $lines[] = $key.': '.$value;
//            }
//
//            $telegram->sendMessage($token, self::ADMIN_ID, implode(PHP_EOL, $lines));
//            $telegram->sendMessage(
//                $token,
//                $fromId,
//                'Здравствуйте Ваш id: '.$fromId.PHP_EOL.'Напишите адрес сайта к которому хотите подключиться'
//            );
//
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        if (
//            ($incomingText === '/link-to-alfa' || $incomingText === 'дай ссылку на альфа банк')
//            && $fromId > 0
//        ) {
//            $telegram->sendMessage(
//                $token,
//                self::ADMIN_ID,
//                'дай ссылку на альфа банк: запросил #'.$fromId.' @'.$username
//            );
//
//            $telegram->sendMessage(
//                $token,
//                $fromId,
//                'Привет'.PHP_EOL.
//                'Альфа банк готов платить, условия тут https://php-cat.com/money'.PHP_EOL.PHP_EOL.
//                'Ссылка для регистрации: https://alfabank.ru/everyday/debit-cards/alfacard-short/?platformId=alfapartners_cpa_79135_DC-visaclassic-70field-sale-254227-rega77-0-0-webmaster&utm_source=alfapartners&utm_medium=cpa&utm_campaign=79135&utm_content=alfapartners_cpa_79135_DC-visaclassic-70field-sale-254227-rega77-0-0-webmaster&card=visa_classic'
//            );
//
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        if ($incomingText !== '' && $fromId > 0) {
//            $lines = ['сообщение в бота', $incomingText, ''];
//            foreach ($from as $key => $value) {
//                $lines[] = $key.': '.$value;
//            }
//
//            $telegram->sendMessage($token, self::ADMIN_ID, implode(PHP_EOL, $lines));
//            $telegram->sendMessage($token, $fromId, 'Принято, спасибо');
//            return $this->response($answerJsonRequested, ['res' => true]);
//        }
//
//        return $this->response($answerJsonRequested, ['res' => false, 'text' => 'не сработала ни одна команда']);
//    }
//
//    private function storeIncomingMessage(array $payload, ?string $token): void
//    {
//        $message = $payload['message'] ?? [];
//        $from = $message['from'] ?? [];
//        $text = (string) ($message['text'] ?? '');
//        $command = Str::startsWith($text, '/') ? strtok($text, ' ') : null;
//
//        TelegramInMsg::query()->create([
//            'telegram_user_id' => isset($from['id']) ? (int) $from['id'] : null,
//            'telegram_message_id' => isset($message['message_id']) ? (int) $message['message_id'] : null,
//            'username' => $from['username'] ?? null,
//            'first_name' => $from['first_name'] ?? null,
//            'last_name' => $from['last_name'] ?? null,
//            'language_code' => $from['language_code'] ?? null,
//            'text' => $text !== '' ? $text : null,
//            'command' => $command ?: null,
//            'is_start' => $command === '/start',
//            'bot_token_hash' => $token !== null && $token !== '' ? hash('sha256', $token) : null,
//            'payload' => $payload,
//            'received_at' => now(),
//        ]);
//    }
//
//    /**
//     * @return array<int>
//     */
//    private function extractTargetIds(Request $request, array $payload): array
//    {
//        $ids = $request->query('id', $payload['id'] ?? []);
//
//        if (!is_array($ids)) {
//            $ids = [$ids];
//        }
//
//        return array_values(array_filter(array_map(static fn ($id): int => (int) $id, $ids)));
//    }
//
//    private function isWebhookRegistered(): bool
//    {
//        return File::exists(storage_path('app/'.self::REGISTER_TRIGGER_PATH));
//    }
//
//    private function response(bool $answerJsonRequested, array $body, int $status = 200): JsonResponse|string
//    {
//        if ($answerJsonRequested) {
//            return response()->json($body, $status);
//        }
//
//        return (string) ($body['text'] ?? ($body['res'] ?? 'ok'));
//    }
}
