<?php

namespace App\Application\Vk\DTO;

final readonly class VkSendMessageRequestDto
{
    /**
     * @param array<int> $userIds
     */
    public function __construct(
        public ?string $groupName,
        public ?string $token,
        public array $userIds,
        public string $message,
    ) {
    }
}
