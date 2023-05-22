<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\OpenApi\Responses\HealthEndpointHealthyResponse;
use App\OpenApi\Responses\ServerErrorResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class HealthController extends Controller
{
    /**
     * Health Endpoint
     *
     * Returns true if everything is healthy.
     *
     * @return Response
     */
    #[OpenApi\Operation]
    #[OpenApi\Response(factory: HealthEndpointHealthyResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: ServerErrorResponse::class, statusCode: 500)]
    public function __invoke(): Response
    {
        return response()->json(['healthy' => true], Response::HTTP_OK);
    }
}
