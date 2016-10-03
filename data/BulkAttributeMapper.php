<?php
namespace lepota\data;

use stdClass;
use Functional;

class BulkAttributeMapper
{
    /** @var array */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function filterAttribute(string $attributeName, $filterValues): self
    {
        if (!is_array($filterValues)) {
            $filterValues = (array) $filterValues;
        }
        return new BulkAttributeMapper(
            Functional\filter($this->data, function (stdClass $item) use ($attributeName, $filterValues): bool {
                return in_array($item->$attributeName, $filterValues);
            })
        );
    }

    public function bulkMapAttribute($mapping, callable $bulkCallback): self
    {

    }

}
