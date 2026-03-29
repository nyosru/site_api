<?php

namespace App\Application\Vk\Services;

use App\Application\Vk\DTO\VkSendMessageRequestDto;
use App\Models\VkGroup;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class VkMessageService
{
    /**
     * @return array{group: string|null, sent_user_ids: array<int>}
     */
    public function send(VkSendMessageRequestDto $dto): array
    {
        $token = $this->resolveToken($dto);
        $version = (string) config('services.vk.api_version', '5.199');

        $sentUserIds = [];

        foreach ($dto->userIds as $userId) {
            $response = Http::asForm()->post('https://api.vk.com/method/messages.send', [
                'access_token' => $token,
                'v' => $version,
                'user_id' => $userId,
                'message' => $dto->message,
                'random_id' => random_int(1, PHP_INT_MAX),
            ])->throw();

            $body = $response->json();
            if (is_array($body) && isset($body['error']) && is_array($body['error'])) {
                $message = (string) ($body['error']['error_msg'] ?? 'VK API error');
                $code = (int) ($body['error']['error_code'] ?? 0);
                throw new RuntimeException($message, $code);
            }

            $sentUserIds[] = $userId;
        }

        return [
            'group' => $dto->groupName,
            'sent_user_ids' => $sentUserIds,
        ];
    }

    private function resolveToken(VkSendMessageRequestDto $dto): string
    {
        if ($dto->groupName !== null && $dto->groupName !== '') {
            $group = VkGroup::query()
                ->where('group_name', $dto->groupName)
                ->where('payed', true)
                ->first();

            if ($group !== null && $group->token !== '') {
                return $group->token;
            }
        }

        if ($dto->token !== null && $dto->token !== '') {
            return $dto->token;
        }

        if ($dto->groupName !== null && $dto->groupName !== '') {
            throw new RuntimeException('Token for group not found: '.$dto->groupName);
        }

        throw new RuntimeException('Either token or group is required');
    }
}
