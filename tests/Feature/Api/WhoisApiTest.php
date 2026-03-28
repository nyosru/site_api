<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class WhoisApiTest extends TestCase
{
    /**
     * проверка цр whois сервиса, вхуисим домен php-cat.ru
     * @return void
     */
    public function test_whois_service_returns_valid_payload_for_php_cat_ru(): void
    {
        $response = $this->getJson('/api/whois?domain=php-cat.ru');

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'domain',
                'available',
            ]);

        $payload = $response->json();

        $this->assertIsInt($payload['status']);
        $this->assertSame('php-cat.ru', $payload['domain']);
        $this->assertIsBool($payload['available']);

        if ($payload['available'] === false) {
            $this->assertArrayHasKey('info', $payload);
            $this->assertIsArray($payload['info']);
        }
    }
}

