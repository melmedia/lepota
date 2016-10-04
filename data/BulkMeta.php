<?php
namespace lepota\data;

use stdClass;
use Functional;

class BulkMeta
{
    /** @var array */
    protected $collection;
    /** @var array */
    protected $meta = [];
    /** @var string|string[] */
    protected $idAttr;
    /** @var array Modified temporary collection after call of filterAttr */
    protected $collectionState;

    public function __construct(array $collection, $idAttr = 'id')
    {
        $this->collection = $collection;
        $this->collectionState = &$this->collection;
        $this->idAttr = $idAttr;
    }

    public function meta($item): stdClass
    {
        return $this->meta[$this->itemId($item)];
    }

    public function filterAttr(array $mapping): self
    {
        $attributeName = array_keys($mapping)[0];
        $filterValues = $mapping[$attributeName];
        if (!is_array($filterValues)) {
            $filterValues = (array) $filterValues;
        }
        $this->collectionState = \Functional\filter($this->collectionState, function ($item) use ($attributeName, $filterValues): bool {
            return in_array($item->$attributeName, $filterValues);
        });
        return $this;
    }

    /**
     * @param array $mapping Map [from attribute => to attribute]
     * @param callable $bulkCallback function (array $fromAttributeValues): array
     * @param string $indexAttribute what attribute of resulted array mapped to fromAttribute
     * @return self
     */
    public function bulkMap(array $mapping, callable $bulkCallback, string $indexAttribute = 'id'): self
    {
        $fromAttribute = array_keys($mapping)[0];
        $toAttribute = $mapping[$fromAttribute];

        $resultData = $bulkCallback(\Functional\pluck($this->collection, $fromAttribute));
        $resultData = array_combine(\Functional\pluck($resultData, $indexAttribute), $resultData);
        foreach ($this->collectionState as $item) {
            $itemId = $this->itemId($item);
            if (!isset($resultData[$item->$fromAttribute])) {
                continue;
            }
            if (!isset($this->meta[$itemId])) {
                $this->meta[$itemId] = new stdClass;
            }
            $this->meta[$itemId]->$toAttribute = $resultData[$item->$fromAttribute];
        }
        return $this;
    }

    protected function itemId($item)
    {
        return join(
            ' ',
            Functional\map(
                (array) $this->idAttr,
                function (string $idAttr) use ($item) { return $item->{$idAttr}; }
            )
        );
    }

}
