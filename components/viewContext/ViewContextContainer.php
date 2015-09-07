<?php
namespace lepota\components\viewContext;

class ViewContextContainer
{
    /** @var array */
    protected $attributes = array();

    /**
     * @var AttributeLoader Соответствие атрибута контекста загрузчику
     */
    protected $attributeSources;

    /** @var ViewContextTrait */
    protected $ctx;

    /** @var bool Разрешена ли модификация */
    protected $isWritable = true;


    public function __construct($ctx)
    {
        $this->ctx = $ctx;
    }

    public function init($attributes)
    {
        foreach ($attributes as $attr => $source) {
            if (!is_array($source)) {
                throw new \CException("Формат описания переменной должен быть такой: $attr => [Closure или string, ViewContextInit::NOW или ViewContextInit::ON_FIRST_GET]");
            }

            $attrLoader = $source[0];
            if (!(is_string($attrLoader) || is_callable($attrLoader))) {
                throw new \CException("Источником переменной должно быть Closure (анонимная функция) или string (имя класса)");
            }
            $attrInit = $source[1];
            if (!in_array($attrInit, [ViewContextInit::NOW, ViewContextInit::ON_FIRST_GET])) {
                throw new \CException("Опцией загрузки может быть ViewContextInit::NOW, ViewContextInit::ON_FIRST_GET");
            }

            $attribute = new AttributeLoader($this, $attr, $attrLoader, $attrInit);
            $attribute->init($this->ctx);
            $this->attributeSources[$attr] = $attribute;
        }
    }

    public function __isset($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            if (isset($this->attributeSources[$attribute]) && !$this->attributeSources[$attribute]->isLoaded()) {
                $this->attributes[$attribute] = $this->attributeSources[$attribute]->load($this->ctx, true);
            }
        }
        return isset($this->attributes[$attribute]);
    }

    public function set($attribute, $value)
    {
        $this->__set($attribute, $value);
    }

    public function __set($attribute, $value)
    {
        if ($this->isWritable) {
            $this->attributes[$attribute] = $value;
        } else {
            throw new ViewContextReadOnlyException();
        }
    }

    public function get($attribute)
    {
        return $this->__get($attribute);
    }

    public function __get($attribute)
    {
        if (isset($this->$attribute)) {
            return $this->attributes[$attribute];
        } elseif ($this->ctx->strictMode === false) {
            return null;
        }

        throw new ViewContextUndefinedVariableException();
    }

    /**
     * @param bool $isWritable Разрешена ли модификация
     */
    public function setIsWritable($isWritable)
    {
        $this->isWritable = $isWritable;
    }

    /**
     * Разрешена ли модификация?
     * @return bool
     */
    public function getIsWritable()
    {
        return $this->isWritable;
    }

}
