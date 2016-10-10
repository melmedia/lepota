<?php
namespace lepota\rest;

use Functional;

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

    public function getData(int $limit): array
    {
        return call_user_func($this->dataCallback, $limit, $this->orderingValue);
    }

    protected function getNextPageParams(array $data): array
    {
        $nextOrderingValue = Functional\last($data)->{$this->orderingAttributeName};
        return ['after' . ucfirst($this->orderingAttributeName) => $nextOrderingValue];
    }

}
