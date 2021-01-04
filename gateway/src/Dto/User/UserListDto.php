<?php

declare(strict_types=1);

namespace App\Dto\User;

use App\Dto\PaginatedResponse;

class UserListDto extends PaginatedResponse
{
    /**
     * @var UserSimpleDto[]
     */
    protected $data;
}
