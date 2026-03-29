<?php

namespace Tests\Feature\Web;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LaravelLogPageTest extends TestCase
{
    public function test_laravel_log_page_is_available_and_shows_latest_lines(): void
    {
        $logPath = storage_path('logs/laravel.log');
        File::ensureDirectoryExists(dirname($logPath));
        File::put($logPath, implode("\n", [
            '[2026-03-29 10:00:00] local.INFO: First line',
            '[2026-03-29 10:00:01] local.ERROR: Second line',
            '[2026-03-29 10:00:02] local.WARNING: Third line',
        ])."\n");

        $response = $this->get('/laravel/log');

        $response->assertOk()
            ->assertSee('Laravel Log')
            ->assertSee('Third line')
            ->assertSee('Second line')
            ->assertSee('First line');
    }

    public function test_laravel_log_page_filters_lines_by_query(): void
    {
        $logPath = storage_path('logs/laravel.log');
        File::ensureDirectoryExists(dirname($logPath));
        File::put($logPath, implode("\n", [
            '[2026-03-29 10:00:00] local.INFO: Alpha event',
            '[2026-03-29 10:00:01] local.ERROR: Bravo failure',
            '[2026-03-29 10:00:02] local.INFO: Charlie event',
        ])."\n");

        $response = $this->get('/laravel/log?q=error');

        $response->assertOk()
            ->assertSee('Bravo failure')
            ->assertDontSee('Alpha event')
            ->assertDontSee('Charlie event');
    }
}
