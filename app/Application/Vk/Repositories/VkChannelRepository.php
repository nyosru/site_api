<?php

namespace App\Application\Vk\Repositories;

use App\Models\VkChannel;
use Illuminate\Support\Collection;

final class VkChannelRepository
{
    public function findByGroupId(int $groupId): ?VkChannel
    {
        return VkChannel::query()
            ->where('group_id', $groupId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * @return Collection<int, VkChannel>
     */
    public function all(): Collection
    {
        return VkChannel::query()
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): VkChannel
    {
        return VkChannel::query()->create($data);
    }

    public function update(VkChannel $channel, array $data): VkChannel
    {
        $channel->update($data);
        return $channel->fresh();
    }

    public function toggleActive(VkChannel $channel): VkChannel
    {
        $channel->update(['is_active' => !$channel->is_active]);
        return $channel->fresh();
    }

    public function delete(VkChannel $channel): void
    {
        $channel->delete();
    }
}
