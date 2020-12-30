<?php

namespace App\Manager;

use App\Dto\UserDataDto;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtTokenManager implements TokenManagerInterface
{
    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    public function __construct(JWTEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encode(UserDataDto $dto): string
    {
        return $this->encoder->encode([
            'id' => $dto->id,
            'username' => $dto->username,
            'roles' => $dto->roles,
        ]);
    }

    public function decode(string $token): UserDataDto
    {
        $payload = $this->encoder->decode($token);

        return UserDataDto::create($payload['id'], $payload['username'], $payload['roles']);
    }
}
