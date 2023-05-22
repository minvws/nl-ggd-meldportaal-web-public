<?php

declare(strict_types=1);

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class MessageResponse extends ResponseFactory implements Reusable
{
    public function build(
        string $name = 'MessageResponse',
        string $description = '',
        string $message = 'Some message',
    ): Response {
        $response = Schema::object()->properties(
            Schema::string('message')->example($message)
        );

        return Response::created($name)
            ->description($description)
            ->content(
                MediaType::json()->schema($response)
            );
    }
}
