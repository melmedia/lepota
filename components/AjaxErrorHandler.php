<?php

namespace lepota\components;

use lepota\exceptions\AjaxException;

class AjaxErrorHandler extends \yii\web\ErrorHandler
{
    protected function convertExceptionToArray($exception)
    {
        if (!$exception instanceof AjaxException) {
            return parent::convertExceptionToArray($exception);
        }
        
        return $exception->getAjaxResponse();
    }
}
