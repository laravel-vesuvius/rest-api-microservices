<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceErrorException extends HttpException
{
    /**
     * @var array|null
     */
    private $errors;

    public function __construct(int $statusCode, ?array $errors)
    {
        parent::__construct($statusCode);

        $this->errors = $errors;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
