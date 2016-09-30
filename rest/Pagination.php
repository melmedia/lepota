<?php
namespace lepota\rest;

abstract class Pagination
{
    /** @var callable */
    protected $dataCallback;
    /** @var int */
    protected $limit;

    protected function __construct(int $limit, callable $dataCallback)
    {
        $this->limit = $limit;
        $this->dataCallback = $dataCallback;
    }

    /**
     * @param int $limit
     * @param int|null $offset
     * @param callable $dataCallback function (int $limit, int $offset = null): array
     * @return array
     */
    public static function offset(int $limit, int $offset = null, callable $dataCallback)
    {
        return (new OffsetPagination($limit, $offset, $dataCallback))->data();
    }

    /**
     * @param int $limit
     * @param array $ordering [name of attribute used for ordering => value of ordering attribute]
     * @param callable $dataCallback function (int $limit, mixed $orderingValue = null): array
     * @return array
     */
    public static function order(int $limit, array $ordering, callable $dataCallback)
    {
        return (new OrderPagination($limit, $ordering, $dataCallback))->data();
    }

}
