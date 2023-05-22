<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\BaseTestFormRequest;
use App\Models\User;
use App\Role;
use App\Services\TestMapper;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use MinVWS\Logging\Laravel\LogService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

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
    public function __invoke(Request $request): Response
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_FORBIDDEN);
        }

        $json = $request->json();
        if (!$json instanceof ParameterBag) {
            return response()->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $validationSuccess = true;
        $validationResults = [];
        $mapped = [];

        // Loop through tests
        foreach ($json->all() as $key => $value) {
            if (!is_array($value)) {
                $validationSuccess = false;
                $validationResults[] = [
                    'validated' => false,
                    'errors' => [
                        'message' => 'Invalid JSON',
                    ],
                ];
                continue;
            }

            // Map GgdTest to our Test
            $mappedTest = TestMapper::fromGgdTest($value);

            // Validate to TestRequest
            $request = new BaseTestFormRequest();
            $validator = \Validator::make($mappedTest, $request->rules(), $request->messages());

            // If not valid, set $success to false
            if ($validator->fails()) {
                $validationSuccess = false;
                $validationResults[] = [
                    'validated' => false,
                    'errors' => $validator->errors()->toArray(),
                ];
                continue;
            }

            $validationResults[] = [
                'validated' => true,
                'errors' => [],
            ];
            $mapped[] = $mappedTest;
        }

        if (!$validationSuccess) {
            // Return results with validation errors
            return response()->json($validationResults, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        unset($validationSuccess, $validationResults);

        // Store tests
        $results = [];
        foreach ($mapped as $data) {
            if (! $this->testService->storeTest($data, $user, $user->hasRole(Role::SPECIMEN))) {
                $results[] = [
                    'stored' => false,
                    'error' => 'Test could not be created',
                ];
                continue;
            }

            $results[] = [
                'stored' => true,
            ];
        }

        // Return results
        return response()->json($results, Response::HTTP_ACCEPTED);
    }
}
