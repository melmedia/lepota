<?php
namespace lepota\domain;

use lepota\domain\ImmutableValue;

/**
 * Reference to entity stored as ":entityType :entityId", example: "publication 1"
 */
class EntityReference extends ImmutableValue
{
    /** @var string */
    public $type;
    /** @var int */
    public $id;

    public static function createFromReference($reference)
    {
        list($type, $id) = explode(' ', $reference);
        return new static($type, $id);
    }

    public static function createEmpty()
    {
        return new static(null, null);
    }


    public function __construct($type, $id)
    {
        $this->type = $type ? preg_replace('~[^a-zA-Z]~', '', $type) : null;
        $this->id = $id ? (int) $id : null;
    }

    public function __toString()
    {
        return join(' ', [$this->type, $this->id]);
    }

    /**
     * @see \yii\base\Model::rules()
     * @return array validation rules
     */
    public function rules()
    {
        return [];
    }

    /**
     * Check if value object is empty (used by lepota\domain\validators\ValueRequiredValidator)
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->id || !$this->type;
    }

}
