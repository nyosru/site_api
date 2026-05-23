<?php

namespace App\Livewire;

use App\Models\VkGroup;
use Livewire\Component;

class VkGroupComponent extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $groupName = '';
    public string $token = '';
    public bool $payed = false;
    public string $payedDate = '';
    public string $comment = '';

    protected function rules(): array
    {
        return [
            'groupName' => ['required', 'string', 'max:64', 'unique:vk_group,group_name,' . ($this->editingId ?? 'NULL') . ',id'],
            'token' => ['required', 'string', 'max:512'],
            'payed' => ['boolean'],
            'payedDate' => ['nullable', 'date'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $group = VkGroup::query()->findOrFail($id);
        $this->editingId = $group->id;
        $this->groupName = $group->group_name;
        $this->token = $group->token;
        $this->payed = $group->payed;
        $this->payedDate = $group->payed_date?->format('Y-m-d') ?? '';
        $this->comment = $group->comment ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'group_name' => $this->groupName,
            'token' => $this->token,
            'payed' => $this->payed,
            'payed_date' => $this->payedDate !== '' ? $this->payedDate : null,
            'comment' => $this->comment !== '' ? $this->comment : null,
        ];

        if ($this->editingId) {
            VkGroup::query()->findOrFail($this->editingId)->update($data);
        } else {
            VkGroup::query()->create($data);
        }

        $this->resetForm();
    }

    public function togglePayed(int $id): void
    {
        $group = VkGroup::query()->findOrFail($id);
        $group->update(['payed' => !$group->payed]);
    }

    public function delete(int $id): void
    {
        VkGroup::query()->findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->groupName = '';
        $this->token = '';
        $this->payed = false;
        $this->payedDate = '';
        $this->comment = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vk-group-component', [
            'groups' => VkGroup::query()->orderBy('group_name')->get(),
        ])->layout('layouts.app');
    }
}
