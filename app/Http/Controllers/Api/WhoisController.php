<?php

namespace App\Http\Controllers\Api;

use App\Application\Whois\DTO\WhoisLookupRequestDto;
use App\Application\Whois\Services\WhoisLookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

final class WhoisController extends Controller
{
    #[OA\Get(
        path: '/api/whois',
        operationId: 'apiWhoisLookup',
        summary: 'Check domain availability and fetch WHOIS data',
        tags: ['Whois'],
        parameters: [
            new OA\Parameter(
                name: 'domain',
                description: 'Domain name to check, for example: example.com',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'google.com')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Whois lookup result',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 1),
                        new OA\Property(property: 'domain', type: 'string', example: 'google.com'),
                        new OA\Property(property: 'available', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'info',
                            type: 'object',
                            nullable: true,
                            additionalProperties: new OA\AdditionalProperties(type: 'string')
                        ),
                        new OA\Property(property: 'message', type: 'string', nullable: true),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The domain field is required.'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request, WhoisLookupService $service): JsonResponse
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $response = $service->lookup(
            new WhoisLookupRequestDto(domain: $validated['domain'])
        );

        return response()->json($response->toArray());
    }
}
