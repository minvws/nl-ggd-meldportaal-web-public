<?php

declare(strict_types=1);

namespace App\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MonsterException extends AbstractException
{
    protected static $endpoint = 'monster';

    protected ?RequestInterface $request;
    protected ?ResponseInterface $response;

    protected array $errors = [];

    public function __construct(
        string $message,
        int $code = 0,
        \Throwable $previous = null,
        RequestInterface $request = null,
        ResponseInterface $response = null,
        array $errors = []
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorMessage(): string
    {
        if (!empty($this->errors)) {
            return implode(', ', $this->errors) ;
        }
        return $this->getMessage();
    }
}
