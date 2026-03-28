<?php

namespace App\Application\Whois\DTO;

final readonly class WhoisLookupResponseDto
{
    /**
     * @param  array<string, mixed>|null  $info
     */
    public function __construct(
        public int $status,
        public string $domain,
        public ?bool $available = null,
        public ?array $info = null,
        public ?string $message = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'status' => $this->status,
            'domain' => $this->domain,
        ];

        if ($this->available !== null) {
            $payload['available'] = $this->available;
        }

        if ($this->info !== null) {
            $payload['info'] = $this->info;
        }

        if ($this->message !== null) {
            $payload['message'] = $this->message;
        }

        return $payload;
    }
}
