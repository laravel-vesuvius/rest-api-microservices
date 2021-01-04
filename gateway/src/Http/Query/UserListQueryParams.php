<?php

declare(strict_types=1);

namespace App\Http\Query;

class UserListQueryParams
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;
    
    public function __construct(int $limit, int $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $property => $value) {
            $result[$property] = $value;
        }

        return $result;
    }
}
