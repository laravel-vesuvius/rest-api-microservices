<?php

declare(strict_types=1);

namespace App\Domain\Common\Message;

class AbstractListQuery implements SyncMessageInterface
{
    /**
     * @var int
     */
    protected $limit = 15;

    /**
     * @var int
     */
    protected $offset = 0;

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
}
