<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LaravelLogComponent extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    private const MAX_LINES = 10000;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'pp')]
    public int $perPage = 100;

    public string $statusMessage = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [20, 50, 100, 200], true)) {
            $this->perPage = 100;
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->perPage = 100;
        $this->resetPage();
    }

    public function clearLogs(): void
    {
        $files = File::glob(storage_path('logs/*.log')) ?: [];

        foreach ($files as $file) {
            if (is_file($file) && is_writable($file)) {
                file_put_contents($file, '');
            }
        }

        $this->statusMessage = $files === []
            ? 'Лог-файлы не найдены.'
            : 'Логи очищены.';

        $this->resetPage();
    }

    public function render()
    {
        $path = storage_path('logs/laravel.log');
        $rows = $this->buildPaginator($path);

        return view('livewire.laravel-log-component', [
            'rows' => $rows,
            'logFileExists' => is_file($path),
            'logFilePath' => $path,
        ])->layout('layouts.app');
    }

    private function buildPaginator(string $path): LengthAwarePaginator
    {
        $lines = $this->readLastLines($path, self::MAX_LINES);

        if ($this->search !== '') {
            $query = mb_strtolower(trim($this->search));
            $lines = array_values(array_filter($lines, static function (string $line) use ($query): bool {
                return mb_stripos($line, $query) !== false;
            }));
        }

        $page = max(1, (int) $this->getPage());
        $total = count($lines);
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($lines, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $items,
            $total,
            $this->perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    private function readLastLines(string $path, int $limit): array
    {
        if (! is_file($path) || $limit <= 0) {
            return [];
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return [];
        }

        fseek($handle, 0, SEEK_END);
        $position = ftell($handle);
        $lines = [];
        $buffer = '';

        while ($position > 0 && count($lines) < $limit) {
            $position--;
            fseek($handle, $position);
            $char = fgetc($handle);

            if ($char === "\n") {
                $line = trim(strrev($buffer), "\r");
                if ($line !== '') {
                    $lines[] = $line;
                }
                $buffer = '';
                continue;
            }

            if ($char !== false) {
                $buffer .= $char;
            }
        }

        if ($buffer !== '' && count($lines) < $limit) {
            $line = trim(strrev($buffer), "\r");
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        fclose($handle);

        return $lines;
    }
}
