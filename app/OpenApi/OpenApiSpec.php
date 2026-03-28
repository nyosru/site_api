<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Site API'
)]
#[OA\Server(
    url: 'https://api.local',
    description: 'Local HTTPS server'
)]
class OpenApiSpec
{
}
