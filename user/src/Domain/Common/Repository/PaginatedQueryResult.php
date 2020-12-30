<?php

declare(strict_types=1);

namespace App\Domain\Common\Repository;

class PaginatedQueryResult
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var int|null
     */
    private $count;

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

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
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
