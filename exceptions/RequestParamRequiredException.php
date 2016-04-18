<?php
namespace lepota\exceptions;

class RequestParamRequiredException extends AjaxException
{
    protected $paramName;

    public function __construct($paramName)
    {
        parent::__construct();
        $this->paramName = $paramName;
    }

    public function getDefaultMessage()
    {
        return \Yii::t('app', 'Request parameter {param} is required', ['param' => $this->paramName]);
    }

}
