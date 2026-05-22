<?php

namespace App\Livewire;

use App\Models\VkChannel;
use Livewire\Component;

class VkChannelComponent extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $tag = '';
    public string $groupId = '';
    public string $confirmationCode = '';
    public bool $isActive = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tag' => ['required', 'string', 'max:255', 'unique:vk_channels,tag,' . ($this->editingId ?? 'NULL') . ',id'],
            'groupId' => ['required', 'integer', 'unique:vk_channels,group_id,' . ($this->editingId ?? 'NULL') . ',id'],
            'confirmationCode' => ['required', 'string', 'max:255'],
            'isActive' => ['boolean'],
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
        $channel = VkChannel::query()->findOrFail($id);
        $this->editingId = $channel->id;
        $this->name = $channel->name;
        $this->tag = $channel->tag;
        $this->groupId = (string) $channel->group_id;
        $this->confirmationCode = $channel->confirmation_code;
        $this->isActive = $channel->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'tag' => $this->tag,
            'group_id' => (int) $this->groupId,
            'confirmation_code' => $this->confirmationCode,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            VkChannel::query()->findOrFail($this->editingId)->update($data);
        } else {
            VkChannel::query()->create($data);
        }

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $channel = VkChannel::query()->findOrFail($id);
        $channel->update(['is_active' => !$channel->is_active]);
    }

    public function delete(int $id): void
    {
        VkChannel::query()->findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->name = '';
        $this->tag = '';
        $this->groupId = '';
        $this->confirmationCode = '';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vk-channel-component', [
            'channels' => VkChannel::query()->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
