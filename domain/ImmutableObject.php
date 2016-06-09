<?php
namespace lepota\domain;

/**
 * Read-only object initialized once using ID attribute
 *
 * @property int $id
 */
abstract class ImmutableObject extends ImmutableValue
{
    protected $id;
    protected $object;

    public static function createEmpty()
    {
        return new static(null);
    }

    public static function createFromArray($attributes)
    {
        return new static($attributes['id']);
    }

    public static function createFromId($id)
    {
        return new static($id);
    }


    abstract protected function initObject();

    protected function __construct($id)
    {
        $this->id = $id;
    }

    public function rules()
    {
        return [
            ['id', 'integer'],
        ];
    }

    public function isEmpty()
    {
        return !$this->id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __get($attribute)
    {
        $getter = 'get' . $attribute;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        return $this->getObject()->$attribute;
    }

    protected function getObject()
    {
        if (null === $this->object) {
            $this->object = $this->initObject();
        }
        return $this->object;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
