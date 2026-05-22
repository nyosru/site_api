<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Site API',
    description: 'API-сервис для интеграции с VK, Telegram и WHOIS. Принимает вебхуки, отправляет сообщения, сохраняет входящие данные и отдаёт их потребителям.'
)]
#[OA\Server(
    url: 'https://api.local',
    description: 'Local development'
)]
#[OA\Server(
    url: 'https://api.uralweb.info',
    description: 'Production'
)]
class OpenApiSpec
{
}
