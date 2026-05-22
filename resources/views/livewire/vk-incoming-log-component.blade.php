<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">VK Incoming Messages</h2>
        <div>
            <button type="button" class="btn btn-sm {{ $autoRefresh ? 'btn-success' : 'btn-outline-success' }} me-1"
                    wire:click="toggleAutoRefresh">
                {{ $autoRefresh ? 'Автообновление ON (15с)' : 'Автообновление' }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                Сбросить фильтры
            </button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="channel">Канал</label>
                    <input id="channel" type="text" class="form-control"
                           placeholder="идентификатор канала"
                           wire:model.live.debounce.300ms="channel">
                </div>
                <div class="form-group col-md-2">
                    <label for="perPage">На странице</label>
                    <select id="perPage" class="form-control" wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Статус доставки</label>
                    <div class="d-flex flex-wrap" style="gap: 6px;">
                        <button type="button"
                                class="btn btn-sm {{ $status === 'all' ? 'btn-dark' : 'btn-outline-dark' }}"
                                wire:click="setStatus('all')">
                            Все
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $status === 'undelivered' ? 'btn-warning' : 'btn-outline-warning' }}"
                                wire:click="setStatus('undelivered')">
                            Не доставлено
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $status === 'delivered' ? 'btn-success' : 'btn-outline-success' }}"
                                wire:click="setStatus('delivered')">
                            Доставлено
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive" @if($autoRefresh) wire:poll.15s @endif>
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Время</th>
                <th>Канал</th>
                <th>Статус</th>
                <th>Доставлен</th>
                <th>Payload (type)</th>
                <th>Payload (group_id)</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ optional($row->received_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                    <td>{{ $row->channel ?? '-' }}</td>
                    <td>
                        @if($row->is_delivered)
                            <span class="badge badge-success">доставлено</span>
                        @else
                            <span class="badge badge-warning">не доставлено</span>
                        @endif
                    </td>
                    <td>{{ optional($row->delivered_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                    <td>{{ $row->payload['type'] ?? '-' }}</td>
                    <td>{{ $row->payload['group_id'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Нет записей по текущему фильтру</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Показано {{ $rows->count() }} из {{ $rows->total() }}
        </div>
        <div>
            {{ $rows->onEachSide(1)->links() }}
        </div>
    </div>
</div>
