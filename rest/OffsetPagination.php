<?php

namespace lepota\rest;

/**
 * List splits into pages by numeric offset.
 *
 * To response http headers added:
 * 'Link: currentUrl?offset=current offset + limit; rel="next"'
 */
class OffsetPagination extends Pagination
{
    /** @var int|null */
    protected $offset;

    public function __construct(int $limit, ?int $offset, callable $dataCallback)
    {
        parent::__construct($limit, $dataCallback);
        $this->offset = $offset;
    }

    public function getNextPageParams(array $data): array
    {
        return ['offset' => $this->offset + $this->limit];
    }

    protected function getData(int $limit): array
    {
        return call_user_func($this->dataCallback, $limit, $this->offset);
    }
}
