<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Http\Requests\BaseTestFormRequest;
use App\Models\User;
use App\OpenApi\RequestBodies\TestStoreRequestBody;
use App\OpenApi\Responses\ErrorValidationResponse;
use App\OpenApi\Responses\ServerErrorResponse;
use App\OpenApi\Responses\TestCreatedResponse;
use App\Role;
use App\Services\TestService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use MinVWS\Logging\Laravel\LogService;
use Symfony\Component\HttpFoundation\Response;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class TestController extends Controller
{
    protected LogService $logService;
    protected TestService $testService;

    public function __construct(TestService $testService, LogService $logService)
    {
        $this->logService = $logService;
        $this->testService = $testService;
    }

    /**
     * Create new test result.
     */
    #[OpenApi\Operation]
    #[OpenApi\RequestBody(factory: TestStoreRequestBody::class)]
    #[OpenApi\Response(factory: TestCreatedResponse::class, statusCode: 201)]
    #[OpenApi\Response(factory: ErrorValidationResponse::class, statusCode: 422)]
    #[OpenApi\Response(factory: ServerErrorResponse::class, statusCode: 500)]
    public function __invoke(BaseTestFormRequest $request): Response
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_FORBIDDEN);
        }

        $data = $request->validated();
        if (! $this->testService->storeTest($data, $user, $user->hasRole(Role::SPECIMEN))) {
            return response()->json(['message' => 'Test could not be created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Test created'], Response::HTTP_CREATED);
    }
}
