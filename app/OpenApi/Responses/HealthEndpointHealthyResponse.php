<?php

declare(strict_types=1);

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class HealthEndpointHealthyResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::boolean('healthy')->example(true)
        );

        return Response::created('HealthEndpointHealthyResponse')
            ->description('Health endpoint healthy response')
            ->content(
                MediaType::json()->schema($response)
            );
    }
}
