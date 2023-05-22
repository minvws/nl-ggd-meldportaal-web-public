<?php

declare(strict_types=1);

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class TestCreatedResponse extends MessageResponse
{
    public function build(
        string $name = 'TestCreated',
        string $description = '',
        string $message = 'Test created',
    ): Response {
        return parent::build($name, $description, $message);
    }
}
