<?php

namespace App\Livewire;

use App\Models\VkIncomingMessage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VkIncomingLogComponent extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'channel')]
    public string $channel = '';

    #[Url(as: 'status')]
    public string $status = 'all';

    #[Url(as: 'pp')]
    public int $perPage = 20;

    public bool $autoRefresh = false;

    public function updatedChannel(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (!in_array($this->perPage, [20, 50, 100], true)) {
            $this->perPage = 20;
        }

        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function toggleDelivered(int $id): void
    {
        $message = VkIncomingMessage::query()->findOrFail($id);
        $message->update([
            'is_delivered' => !$message->is_delivered,
            'delivered_at' => $message->is_delivered ? null : now(),
        ]);
    }

    public function resetFilters(): void
    {
        $this->channel = '';
        $this->status = 'all';
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $rows = VkIncomingMessage::query()
            ->when($this->channel !== '', fn($query) => $query->where('channel', $this->channel))
            ->when($this->status === 'delivered', fn($query) => $query->where('is_delivered', true))
            ->when($this->status === 'undelivered', fn($query) => $query->where('is_delivered', false))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.vk-incoming-log-component', [
            'rows' => $rows,
        ])->layout('layouts.app');
    }
}
