<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">VK Groups (группы для отправки)</h2>
        <button type="button" class="btn btn-sm btn-primary" wire:click="create">
            + Добавить группу
        </button>
    </div>

    @if($showForm)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $editingId ? 'Редактировать группу' : 'Новая группа' }}</h5>
                <form wire:submit="save">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="groupName">Название группы</label>
                            <input id="groupName" type="text" class="form-control @error('groupName') is-invalid @enderror"
                                   wire:model.blur="groupName" placeholder="my_group">
                            @error('groupName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="token">Токен</label>
                            <input id="token" type="text" class="form-control @error('token') is-invalid @enderror"
                                   wire:model.blur="token">
                            @error('token') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-4">
                            <label for="payedDate">Дата оплаты</label>
                            <input id="payedDate" type="date" class="form-control @error('payedDate') is-invalid @enderror"
                                   wire:model.blur="payedDate">
                            @error('payedDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-8">
                            <label for="comment">Комментарий</label>
                            <input id="comment" type="text" class="form-control @error('comment') is-invalid @enderror"
                                   wire:model.blur="comment">
                            @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input id="payed" type="checkbox" class="form-check-input" wire:model="payed">
                                <label for="payed" class="form-check-label">Оплачено</label>
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
                <th>Название группы</th>
                <th>Токен</th>
                <th>Оплачено</th>
                <th>Дата оплаты</th>
                <th>Комментарий</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td><code>{{ $group->group_name }}</code></td>
                    <td>
                        <code>{{ substr($group->token, 0, 20) }}{{ strlen($group->token) > 20 ? '...' : '' }}</code>
                    </td>
                    <td>
                        @if($group->payed)
                            <span class="badge badge-success">да</span>
                        @else
                            <span class="badge badge-secondary">нет</span>
                        @endif
                    </td>
                    <td>{{ $group->payed_date?->format('d.m.Y') ?? '-' }}</td>
                    <td>{{ $group->comment ?? '-' }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $group->id }})">
                            Ред.
                        </button>
                        <button type="button" class="btn btn-sm {{ $group->payed ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                wire:click="togglePayed({{ $group->id }})">
                            {{ $group->payed ? 'Не оплачено' : 'Оплачено' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="delete({{ $group->id }})"
                                onclick="return confirm('Удалить группу «{{ $group->group_name }}»?')">
                            Удалить
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Нет групп. Добавьте первую.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
