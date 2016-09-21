<?php
namespace lepota\components\validators;

use Yii;

class UrlValidator extends \yii\validators\UrlValidator
{
    public $domain;

    protected function validateValue($value)
    {
        $result = parent::validateValue($value);
        if (!empty($result)) {
            return $result;
        }
        if ($this->defaultScheme !== null && strpos($value, '://') === false) {
            $value = $this->defaultScheme . '://' . $value;
        }
        $urlDomain = parse_url($value, PHP_URL_HOST);
        if (!in_array($urlDomain, (array)$this->domain)) {
            return [$this->message, []];
        }
        return null;
    }

}
