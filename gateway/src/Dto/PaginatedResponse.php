<?php

declare(strict_types=1);

namespace App\Dto;

class PaginatedResponse
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $count;

    public function __construct(array $data, ?int $limit, ?int $offset, ?int $count)
    {
        $this->data = $data;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->count = $count;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }
}
