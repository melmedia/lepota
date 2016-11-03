<?php
namespace lepota\data;

use Exception;
use stdClass;
use Functional;

class BulkMeta
{
    /** @var array */
    protected $collection;
    /** @var array */
    protected $meta = [];
    /** @var string|string[]|null */
    protected $idAttr;

    public function __construct(array $collection, $idAttr = 'id')
    {
        $this->collection = $collection;
        $this->idAttr = $idAttr;
    }

    protected function copy($collection)
    {
        $copy = new BulkMeta($collection, $this->idAttr);
        $copy->meta = &$this->meta;
        return $copy;
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
        return $this->copy(
            Functional\filter($this->collection, function ($item) use ($attributeName, $filterValues): bool {
                return in_array($item->$attributeName, $filterValues);
            })
        );
    }

    /**
     * @param array $mapping Map [from attribute => to attribute]
     * or [[from attributes], to attribute] (array used as from attribute)
     * or [to attribute] (entire value used as from attribute)
     * @param callable $bulkCallback function (array $fromAttributeValues): array
     * @param string|string[] $indexAttribute what attribute of resulted array mapped to fromAttribute
     * @return self
     */
    public function bulkMap(array $mapping, callable $bulkCallback, $indexAttribute = 'id'): self
    {
        if (!$this->collection) {
            // nothing to map
            return $this;
        }

        if (isset($mapping[0])) {
            if (isset($mapping[1])) {
                $fromAttribute = $mapping[0];
                $toAttribute = $mapping[1];
            } else {
                $fromAttribute = null;
                $toAttribute = $mapping[0];
            }
        } else {
            $fromAttribute = array_keys($mapping)[0];
            $toAttribute = $mapping[$fromAttribute];
        }

        $resultData = $bulkCallback(
            Functional\unique(
                array_filter(
                    Functional\map($this->collection, function ($item) use ($fromAttribute) { return self::objectKey($item, $fromAttribute, false); })
                )
            )
        );
        $resultData = array_combine(
            Functional\map($resultData, function ($item) use ($indexAttribute) { return self::objectKey($item, $indexAttribute, true); }),
            $resultData
        );

        foreach ($this->collection as $item) {
            $itemId = $this->itemId($item);
            $fromAttributeValue = self::objectKey($item, $fromAttribute, true);
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
        return self::objectKey($item, $this->idAttr, true);
    }

    /**
     * @param mixed $object
     * @param string|string[]|callable|null $keyAttr
     * @param bool $isReturnString if true, join array with spaces
     * @return string|array
     * @throws Exception
     */
    protected static function objectKey($object, $keyAttr, bool $isReturnString)
    {
        switch (true) {
            case is_null($keyAttr):
                $key = $object;
                break;

            case is_string($keyAttr):
                $key = $object->$keyAttr;
                break;

            case is_array($keyAttr):
                $key = Functional\map(
                    $keyAttr,
                    function (string $keyAttrItem) use ($object) { return $object->{$keyAttrItem}; }
                );
                if ($isReturnString) {
                    $key = join(' ', $key);
                }
                break;

            case is_callable($keyAttr):
                $key = $keyAttr($object);
                break;

            default:
                throw new Exception('Wrong keyAttr type');
        }

        return $key;
    }

}
