<?php

declare(strict_types=1);

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ServerErrorResponse extends MessageResponse
{
    public function build(
        string $name = 'ServerError',
        string $description = '',
        string $message = 'Server Error',
    ): Response {
        return parent::build($name, $description, $message);
    }
}
