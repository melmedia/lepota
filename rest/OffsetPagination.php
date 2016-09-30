<?php
namespace lepota\rest;

use Yii;
use yii\helpers\Url;

/**
 * List splits into pages by numeric offset.
 *
 * To response http headers added:
 * 'Link: currentUrl?offet=current offset + limit; rel="next"'
 */
class OffsetPagination extends Pagination
{
    /** @var int|null */
    protected $offset;

    protected function __construct(int $limit, int $offset = null, callable $dataCallback)
    {
        parent::__construct($limit, $dataCallback);
        $this->offset = $offset;
    }

    public function data()
    {
        $data = call_user_func($this->dataCallback, $this->limit + 1, $this->offset);

        $isHaveNextPage = ($data && count($data) > $this->limit);
        $data = array_slice($data, 0, $this->limit);

        if ($isHaveNextPage) {
            Yii::$app->response->setLinkHeader(
                Url::current(['offset' => $this->offset + $this->limit]),
                'next'
            );
        }

        return $data;
    }

}
