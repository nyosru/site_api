<?php

namespace App\Application\Vk\DTO;

final readonly class VkIncomingMessageDto
{
    public function __construct(
        public int $id,
        public ?string $channel,
        public array $payload,
        public string $receivedAt,
    ) {}

    /**
     * @return array{id: int, channel: string|null, payload: array, received_at: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'payload' => $this->payload,
            'received_at' => $this->receivedAt,
        ];
    }
}
