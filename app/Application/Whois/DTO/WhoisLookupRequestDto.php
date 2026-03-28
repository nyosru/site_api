<?php

namespace App\Application\Whois\DTO;

final readonly class WhoisLookupRequestDto
{
    public function __construct(
        public string $domain,
    ) {}
}
