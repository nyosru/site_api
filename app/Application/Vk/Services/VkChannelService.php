<?php

namespace App\Application\Vk\Services;

use App\Application\Vk\Repositories\VkChannelRepository;
use App\Models\VkChannel;
use Illuminate\Support\Collection;

final class VkChannelService
{
    public function __construct(
        private readonly VkChannelRepository $repository,
    ) {}

    public function findByGroupId(int $groupId): ?VkChannel
    {
        return $this->repository->findByGroupId($groupId);
    }

    /**
     * @return Collection<int, VkChannel>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): VkChannel
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): VkChannel
    {
        $channel = VkChannel::query()->findOrFail($id);
        return $this->repository->update($channel, $data);
    }

    public function toggleActive(int $id): VkChannel
    {
        $channel = VkChannel::query()->findOrFail($id);
        return $this->repository->toggleActive($channel);
    }

    public function delete(int $id): void
    {
        $channel = VkChannel::query()->findOrFail($id);
        $this->repository->delete($channel);
    }
}
