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
        return $this->meta[$this->itemId($item)] ?? new stdClass;
    }

    public function filterAttr(array $mapping): self
    {
        $attributeName = array_keys($mapping)[0];
        $filterValues = $mapping[$attributeName];
        if (!is_array($filterValues)) {
            $filterValues = (array) $filterValues;
        }
        $this->collectionState = Functional\filter($this->collectionState, function ($item) use ($attributeName, $filterValues): bool {
            return in_array($item->$attributeName, $filterValues);
        });
        return $this;
    }

    /**
     * @param array $mapping Map [from attribute => to attribute] or [function ($item): string, to attribute]
     * @param callable $bulkCallback function (array $fromAttributeValues): array
     * @param string|string[] $indexAttribute what attribute of resulted array mapped to fromAttribute
     * @return self
     */
    public function bulkMap(array $mapping, callable $bulkCallback, $indexAttribute = 'id'): self
    {
        if (isset($mapping[0])) {
            $fromAttribute = $mapping[0];
            $toAttribute = $mapping[1];
        } else {
            $fromAttribute = array_keys($mapping)[0];
            $toAttribute = $mapping[$fromAttribute];
        }

        $keys = is_string($fromAttribute) ?
            Functional\pluck($this->collection, $fromAttribute) :
            Functional\map($this->collection, $fromAttribute);
        $resultData = $bulkCallback($keys);
        $resultData = is_string($indexAttribute) ?
            array_combine(Functional\pluck($resultData, $indexAttribute), $resultData) :
            array_combine(
                Functional\map($resultData, function ($item) use ($indexAttribute) { return self::objectId($item, $indexAttribute); }),
                $resultData
            );

        foreach ($this->collectionState as $item) {
            $itemId = $this->itemId($item);
            $fromAttributeValue = is_string($fromAttribute) ? $item->$fromAttribute : $fromAttribute($item);
            if (!isset($resultData[$fromAttributeValue])) {
                continue;
            }
            if (!isset($this->meta[$itemId])) {
                $this->meta[$itemId] = new stdClass;
            }
            $this->meta[$itemId]->$toAttribute = $resultData[$fromAttributeValue];
        }
        return $this;
    }

    protected function itemId($item): string
    {
        return self::objectId($item, $this->idAttr);
    }

    /**
     * @param mixed $object
     * @param string|string[] $idAttr
     * @return string
     */
    protected static function objectId($object, $idAttr): string
    {
        return join(
            ' ',
            Functional\map(
                (array) $idAttr,
                function (string $idAttr) use ($object) { return $object->{$idAttr}; }
            )
        );
    }

}
