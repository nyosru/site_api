<?php

namespace App\Application\Vk\Repositories;

use App\Models\VkIncomingMessage;
use Illuminate\Support\Collection;

final class VkIncomingMessageRepository
{
    public function create(array $payload, ?string $channel = null): VkIncomingMessage
    {
        return VkIncomingMessage::query()->create([
            'channel' => $channel,
            'payload' => $payload,
            'is_delivered' => false,
            'received_at' => now(),
        ]);
    }

    /**
     * @return Collection<int, VkIncomingMessage>
     */
    public function getUndelivered(?string $channel = null): Collection
    {
        $query = VkIncomingMessage::query()
            ->where('is_delivered', false);

        if ($channel !== null) {
            $query->where('channel', $channel);
        }

        return $query->orderBy('id')->get();
    }

    public function markAsDelivered(int $id): void
    {
        VkIncomingMessage::query()
            ->where('id', $id)
            ->update([
                'is_delivered' => true,
                'delivered_at' => now(),
            ]);
    }
}
