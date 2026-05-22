<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">VK Channels</h2>
        <button type="button" class="btn btn-sm btn-primary" wire:click="create">
            + Добавить канал
        </button>
    </div>

    @if($showForm)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $editingId ? 'Редактировать канал' : 'Новый канал' }}</h5>
                <form wire:submit="save">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name">Название канала</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   wire:model.blur="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tag">Тег (идентификатор)</label>
                            <input id="tag" type="text" class="form-control @error('tag') is-invalid @enderror"
                                   wire:model.blur="tag" placeholder="my_channel">
                            @error('tag') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-4">
                            <label for="groupId">Group ID</label>
                            <input id="groupId" type="number" class="form-control @error('groupId') is-invalid @enderror"
                                   wire:model.blur="groupId">
                            @error('groupId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="confirmationCode">Код подтверждения</label>
                            <input id="confirmationCode" type="text" class="form-control @error('confirmationCode') is-invalid @enderror"
                                   wire:model.blur="confirmationCode">
                            @error('confirmationCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="secret">Secret</label>
                            <input id="secret" type="text" class="form-control @error('secret') is-invalid @enderror"
                                   wire:model.blur="secret" placeholder="секретный ключ от VK">
                            @error('secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input id="isActive" type="checkbox" class="form-check-input" wire:model="isActive">
                                <label for="isActive" class="form-check-label">Активен</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancel">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Тег</th>
                <th>Group ID</th>
                <th>Статус</th>
                <th>Код подтверждения</th>
                <th>Secret</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($channels as $channel)
                <tr>
                    <td>{{ $channel->id }}</td>
                    <td>{{ $channel->name }}</td>
                    <td><code>{{ $channel->tag }}</code></td>
                    <td>{{ $channel->group_id }}</td>
                    <td>
                        @if($channel->is_active)
                            <span class="badge badge-success">активен</span>
                        @else
                            <span class="badge badge-secondary">отключён</span>
                        @endif
                    </td>
                    <td><code>{{ $channel->confirmation_code }}</code></td>
                    <td><code>{{ $channel->secret ?? '-' }}</code></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $channel->id }})">
                            Ред.
                        </button>
                        <button type="button" class="btn btn-sm {{ $channel->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                wire:click="toggleActive({{ $channel->id }})">
                            {{ $channel->is_active ? 'Выкл' : 'Вкл' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="delete({{ $channel->id }})"
                                onclick="return confirm('Удалить канал «{{ $channel->name }}»?')">
                            Удалить
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Нет каналов. Добавьте первый.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
