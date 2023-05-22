<?php

declare(strict_types=1);

namespace App\OpenApi\RequestBodies;

use App\OpenApi\Schemas\TestSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class TestStoreRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        return RequestBody::create('TestCreate')
            ->description('Create new test result')
            ->content(
                MediaType::json()->schema(TestSchema::ref())
            );
    }
}
