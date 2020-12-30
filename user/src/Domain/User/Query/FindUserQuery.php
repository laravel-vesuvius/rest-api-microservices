<?php

declare(strict_types=1);

namespace App\Domain\User\Query;

use App\Domain\Common\Message\SyncMessageInterface;

class FindUserQuery implements SyncMessageInterface
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
