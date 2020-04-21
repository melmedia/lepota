<?php

namespace lepota\rest;

use Yii;
use yii\helpers\Url;

abstract class Pagination
{
    /** @var callable */
    protected $dataCallback;
    /** @var int */
    protected $limit;
    /** @var boolean */
    protected $isHaveNextPage;

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
    public static function offset(int $limit, ?int $offset, callable $dataCallback)
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

    abstract protected function getData(int $limit): array;
    abstract public function getNextPageParams(array $data): array;

    public function data()
    {
        $isUnlimited = 0 === $this->limit;
        $data = $this->getData(!$isUnlimited ? $this->limit + 1 : 0);

        $this->isHaveNextPage = !$isUnlimited ? ($data && count($data) > $this->limit) : false;
        if (!$isUnlimited) {
            $data = array_slice($data, 0, $this->limit);
        }

        if ($this->isHaveNextPage) {
            if ('GET' == Yii::$app->request->method) {
                Yii::$app->response->setLinkHeader(
                    Url::current($this->getNextPageParams($data)),
                    'next'
                );
            }
        }

        return $data;
    }

    public function isHaveNextPage()
    {
        return $this->isHaveNextPage;
    }
}
