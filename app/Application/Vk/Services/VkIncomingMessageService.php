<?php

namespace App\Application\Vk\Services;

use App\Application\Vk\DTO\VkIncomingMessageDto;
use App\Application\Vk\Repositories\VkIncomingMessageRepository;

final class VkIncomingMessageService
{
    public function __construct(
        private readonly VkIncomingMessageRepository $repository,
    ) {}

    public function store(array $payload, ?string $channel = null, ?array $validation = null): VkIncomingMessageDto
    {
        $message = $this->repository->create($payload, $channel, $validation);

        return new VkIncomingMessageDto(
            id: $message->id,
            channel: $message->channel,
            payload: $message->payload,
            validation: $message->validation,
            receivedAt: $message->received_at->toIso8601String(),
        );
    }

    /**
     * @return array<int, array{id: int, channel: string|null, payload: array, validation: array|null, received_at: string}>
     */
    public function consumeUndelivered(?string $channel = null): array
    {
        $messages = $this->repository->getUndelivered($channel);

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->id,
                'channel' => $message->channel,
                'payload' => $message->payload,
                'validation' => $message->validation,
                'received_at' => $message->received_at?->toIso8601String(),
            ];
        }

        $ids = array_column($result, 'id');
        foreach ($ids as $id) {
            $this->repository->markAsDelivered($id);
        }

        return $result;
    }
}
