<?php
namespace lepota\components\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * copied from composer.json
 * "kfreiman/yii2-json-attribute-behavior": "dev-master#b1043e4ed8b8d92a740bbdcaf94b5b0b939e5d9c",
 */
class JsonAttributeBehavior extends Behavior
{

    /**
     * @var string[] Attributes you want to be encoded
     */
    public $attributes = [];

    /**
     * @var bool How to decode JSON
     */
    public $asArray = false;

    /**
     * @var array store old attributes
     */
    protected $_oldAttributes = [];


    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encodeAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encodeAttributes',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'decodeAttributes',
        ];
    }


    public function encodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_oldAttributes[$attribute])) {
                $this->owner->setOldAttribute($attribute, $this->_oldAttributes[$attribute]);
            }

            $this->owner->$attribute = Json::encode($this->owner->$attribute);
        }
    }


    public function decodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->_oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);

            $value = Json::decode($this->owner->$attribute, $this->asArray);
            $this->owner->setAttribute($attribute, $value);
            $this->owner->setOldAttribute($attribute, $value);
        }
    }


    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, array_keys($this->attributes));
    }


    public function __get($name)
    {
        foreach ($this->attributes as $rawAttr => $attr) {
            if ($name == $rawAttr) {
                return $this->_oldAttributes[$attr];
            }
        }
    }
}
