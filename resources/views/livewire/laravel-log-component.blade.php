<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Laravel Log</h2>
        <div class="d-flex" style="gap: 8px;">
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="clearLogs"
                    wire:confirm="Очистить все логи?">
                Очистить логи
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                Сбросить фильтры
            </button>
        </div>
    </div>

    @if($statusMessage !== '')
        <div class="alert alert-info py-2">
            {{ $statusMessage }}
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="search">Быстрый фильтр (должно быть в строке)</label>
                    <input id="search"
                           type="text"
                           class="form-control"
                           placeholder="например: ERROR или SQLSTATE"
                           wire:model.live.debounce.300ms="search">
                </div>
                <div class="form-group col-md-2">
                    <label for="perPage">На странице</label>
                    <select id="perPage" class="form-control" wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Лог-файл</label>
                    @if($logFileExists)
                        <div class="form-control-plaintext text-success">Найден</div>
                    @else
                        <div class="form-control-plaintext text-danger">Не найден</div>
                    @endif
                </div>
            </div>

            <small class="text-muted d-block">
                Путь: {{ $logFilePath }}
            </small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Строка</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $index => $line)
                <tr>
                    <td>{{ ($rows->currentPage() - 1) * $rows->perPage() + $index + 1 }}</td>
                    <td style="white-space: pre-wrap; font-family: Consolas, Monaco, monospace;">{{ $line }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        @if($logFileExists)
                            Нет строк по текущему фильтру
                        @else
                            Файл лога пока отсутствует
                        @endif
                    </td>
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
