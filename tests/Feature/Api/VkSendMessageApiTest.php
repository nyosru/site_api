<?php

namespace Tests\Feature\Api;

use App\Models\VkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VkSendMessageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vk_send_message_by_token_to_single_user(): void
    {
        Http::fake([
            'https://api.vk.com/method/messages.send' => Http::response(['response' => 1], 200),
        ]);

        $response = $this->postJson('/api/vk/send', [
            'token' => 'vk-token-1',
            'user_id' => 12345,
            'message' => 'Привет',
        ]);

        $response->assertOk()->assertJson([
            'ok' => true,
            'group' => null,
            'sent_user_ids' => [12345],
        ]);

        Http::assertSentCount(1);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'vk-token-1'
                && $request['user_id'] === 12345
                && $request['message'] === 'Привет'
                && $request['v'] === '5.199';
        });
    }

    public function test_vk_send_message_by_group_name_to_multiple_users_from_comma_string(): void
    {
        VkGroup::query()->create([
            'group_name' => 'main_group',
            'token' => 'group-token-1',
            'payed' => true,
        ]);

        Http::fake([
            'https://api.vk.com/method/messages.send' => Http::response(['response' => 1], 200),
        ]);

        $response = $this->postJson('/api/vk/send', [
            'group' => 'main_group',
            'user_id' => '10, 20,30',
            'message' => 'Тест',
        ]);

        $response->assertOk()->assertJson([
            'ok' => true,
            'group' => 'main_group',
            'sent_user_ids' => [10, 20, 30],
        ]);

        Http::assertSentCount(3);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'group-token-1'
                && $request['user_id'] === 10;
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'group-token-1'
                && $request['user_id'] === 20;
        });
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'group-token-1'
                && $request['user_id'] === 30;
        });
    }

    public function test_vk_send_message_returns_422_when_group_and_token_are_missing(): void
    {
        Http::fake();

        $response = $this->postJson('/api/vk/send', [
            'user_id' => 1,
            'message' => 'x',
        ]);

        $response->assertStatus(422)->assertJson([
            'ok' => false,
        ]);
    }

    public function test_vk_send_message_returns_422_when_group_token_not_found(): void
    {
        Http::fake();

        $response = $this->postJson('/api/vk/send', [
            'group' => 'unknown_group',
            'user_id' => 1,
            'message' => 'x',
        ]);

        $response->assertStatus(422)->assertJson([
            'ok' => false,
            'error' => 'Token for group not found: unknown_group',
        ]);
    }

    public function test_vk_send_message_uses_token_from_db_group_and_ignores_sent_token(): void
    {
        VkGroup::query()->create([
            'group_name' => 'priority_group',
            'token' => 'db-priority-token',
            'payed' => true,
        ]);

        Http::fake([
            'https://api.vk.com/method/messages.send' => Http::response(['response' => 1], 200),
        ]);

        $response = $this->postJson('/api/vk/send', [
            'group' => 'priority_group',
            'token' => 'request-token-should-be-ignored',
            'user_id' => 9001,
            'message' => 'Token priority test',
        ]);

        $response->assertOk()->assertJson([
            'ok' => true,
            'group' => 'priority_group',
            'sent_user_ids' => [9001],
        ]);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'db-priority-token'
                && $request['user_id'] === 9001;
        });
    }

    public function test_vk_send_message_ignores_unpaid_group_and_uses_request_token(): void
    {
        VkGroup::query()->create([
            'group_name' => 'unpaid_group',
            'token' => 'db-unpaid-token',
            'payed' => false,
        ]);

        Http::fake([
            'https://api.vk.com/method/messages.send' => Http::response(['response' => 1], 200),
        ]);

        $response = $this->postJson('/api/vk/send', [
            'group' => 'unpaid_group',
            'token' => 'request-fallback-token',
            'user_id' => 7007,
            'message' => 'Fallback token test',
        ]);

        $response->assertOk()->assertJson([
            'ok' => true,
            'group' => 'unpaid_group',
            'sent_user_ids' => [7007],
        ]);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.vk.com/method/messages.send'
                && $request['access_token'] === 'request-fallback-token'
                && $request['user_id'] === 7007;
        });
    }
}
