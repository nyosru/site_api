<?php

namespace App\Livewire;

use App\Models\TelegramInMsg;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TelegramLogComponent extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'uid')]
    public string $telegramUserId = '';

    #[Url(as: 'type')]
    public string $type = 'all';

    #[Url(as: 'period')]
    public string $period = 'all';

    #[Url(as: 'pp')]
    public int $perPage = 20;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTelegramUserId(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedPeriod(): void
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

    public function setType(string $type): void
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->telegramUserId = '';
        $this->type = 'all';
        $this->period = 'all';
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $rows = TelegramInMsg::query()
            ->when($this->telegramUserId !== '', function (Builder $query): void {
                $query->where('telegram_user_id', (int) $this->telegramUserId);
            })
            ->when($this->search !== '', function (Builder $query): void {
                $q = trim($this->search);
                $query->where(function (Builder $inner) use ($q): void {
                    $inner->where('text', 'like', '%'.$q.'%')
                        ->orWhere('username', 'like', '%'.$q.'%')
                        ->orWhere('command', 'like', '%'.$q.'%');
                });
            })
            ->when($this->type === 'start', fn (Builder $query) => $query->where('is_start', true))
            ->when($this->type === 'commands', fn (Builder $query) => $query->whereNotNull('command'))
            ->when($this->type === 'text', fn (Builder $query) => $query->whereNull('command'))
            ->when($this->period === 'today', fn (Builder $query) => $query->whereDate('received_at', today()))
            ->when($this->period === '7d', fn (Builder $query) => $query->where('received_at', '>=', now()->subDays(7)))
            ->when($this->period === '30d', fn (Builder $query) => $query->where('received_at', '>=', now()->subDays(30)))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.telegram-log-component', [
            'rows' => $rows,
        ])->layout('layouts.app');
    }
}
