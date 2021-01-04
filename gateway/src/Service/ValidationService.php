<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ValidationHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $data): object
    {
        $violations = $this->validator->validate($data);
        if ($violations->count()) {
            throw new ValidationHttpException($violations);
        }

        return $data;
    }
}
