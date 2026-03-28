<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Telegram Log</h2>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
            Сбросить фильтры
        </button>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="search">Быстрый поиск</label>
                    <input id="search" type="text" class="form-control"
                           placeholder="текст, команда, username"
                           wire:model.live.debounce.300ms="search">
                </div>
                <div class="form-group col-md-3">
                    <label for="telegramUserId">Telegram User ID</label>
                    <input id="telegramUserId" type="number" class="form-control"
                           placeholder="например 360209578"
                           wire:model.live.debounce.300ms="telegramUserId">
                </div>
                <div class="form-group col-md-2">
                    <label for="perPage">На странице</label>
                    <select id="perPage" class="form-control" wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Период</label>
                    <div class="d-flex flex-wrap" style="gap: 6px;">
                        <button type="button"
                                class="btn btn-sm {{ $period === 'today' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="setPeriod('today')">
                            Сегодня
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $period === '7d' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="setPeriod('7d')">
                            7 дней
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $period === '30d' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="setPeriod('30d')">
                            30 дней
                        </button>
                        <button type="button"
                                class="btn btn-sm {{ $period === 'all' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="setPeriod('all')">
                            Всё
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap" style="gap: 6px;">
                <button type="button"
                        class="btn btn-sm {{ $type === 'all' ? 'btn-dark' : 'btn-outline-dark' }}"
                        wire:click="setType('all')">
                    Все
                </button>
                <button type="button"
                        class="btn btn-sm {{ $type === 'start' ? 'btn-success' : 'btn-outline-success' }}"
                        wire:click="setType('start')">
                    Только /start
                </button>
                <button type="button"
                        class="btn btn-sm {{ $type === 'commands' ? 'btn-info' : 'btn-outline-info' }}"
                        wire:click="setType('commands')">
                    Только команды
                </button>
                <button type="button"
                        class="btn btn-sm {{ $type === 'text' ? 'btn-warning' : 'btn-outline-warning' }}"
                        wire:click="setType('text')">
                    Только текст
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Время</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Тип</th>
                <th>Текст</th>
                <th>Команда</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ optional($row->received_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                    <td>{{ $row->telegram_user_id ?? '-' }}</td>
                    <td>{{ $row->username ?? '-' }}</td>
                    <td>
                        @if($row->is_start)
                            <span class="badge badge-success">start</span>
                        @elseif(!empty($row->command))
                            <span class="badge badge-info">command</span>
                        @else
                            <span class="badge badge-secondary">text</span>
                        @endif
                    </td>
                    <td style="max-width: 520px; white-space: pre-wrap;">{{ $row->text ?? '-' }}</td>
                    <td>{{ $row->command ?? '-' }}</td>
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
