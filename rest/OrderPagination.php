<?php
namespace lepota\rest;

use Functional;
use Yii;
use yii\helpers\Url;

/**
 * List splits into pages by value of ordering attribute.
 *
 * To response http headers added (orderingAttributeName='publicationTime'):
 * 'Link: currentUrl?afterPublicationTime=ordering attribute value of last data; rel="next"'
 */
class OrderPagination extends Pagination
{
    /** @var string */
    protected $orderingAttributeName;
    protected $orderingValue;

    protected function __construct(int $limit, array $ordering, callable $dataCallback)
    {
        parent::__construct($limit, $dataCallback);
        $this->orderingAttributeName = array_keys($ordering)[0];
        $this->orderingValue = $ordering[$this->orderingAttributeName];
    }

    public function data()
    {
        $data = call_user_func($this->dataCallback, $this->limit + 1, $this->orderingValue);

        $isHaveNextPage = ($data && count($data) > $this->limit);
        $data = array_slice($data, 0, $this->limit);

        if ($isHaveNextPage) {
            $nextOrderingValue = Functional\last($data)->{$this->orderingAttributeName};
            Yii::$app->response->setLinkHeader(
                Url::current(['after' . ucfirst($this->orderingAttributeName) => $nextOrderingValue]),
                'next'
            );
        }

        return $data;
    }

}
