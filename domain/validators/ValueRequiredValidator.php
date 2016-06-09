<?php
namespace lepota\domain\validators;

use Yii;
use yii\base\NotSupportedException;
use yii\validators\Validator;
use lepota\domain\ImmutableValue;

/**
 * Applicable to attributes of type lepota\domain\Value 
 */
class ValueRequiredValidator extends Validator
{

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} cannot be blank.');
        }
    }

    /**
     * Validates a value.
     * A validator class can implement this method to support data validation out of the context of a data model.
     * @param ImmutableValue $value the data value to be validated
     * @return array|null the error message and the parameters to be inserted into the error message.
     * Null should be returned if the data is valid.
     * @throws NotSupportedException if type of $value is not Value
     */
    protected function validateValue($value)
    {
        if (!$value instanceof ImmutableValue) {
            return ['{attribute} is not of type ' . ImmutableValue::class, []];
        }
        return $value->isEmpty() ? [$this->message, []] : null; 
    }

}
