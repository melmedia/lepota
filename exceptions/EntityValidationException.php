<?php
namespace lepota\exceptions;

use yii\base\Model;

/**
 * Model validation errors wrapper
 */
class EntityValidationException extends AjaxException
{
    /** @var Model|null */
    protected $model;
    /** @var array Ошибки валидации модели */
    protected $modelErrors;

    /**
     * @param Model|array $modelErrors
     */
    public function __construct($modelErrors)
    {
        parent::__construct();
        if ($modelErrors instanceof Model) {
            $this->model = $modelErrors;
            $this->modelErrors = $this->model->getErrors();
        }
        else {
            $this->modelErrors = $modelErrors;
        }
    }

    public function getAjaxResponse($message = null)
    {
        return [
            'code' => $this->getAjaxErrorCode(),
            'validationErrors' => $this->modelErrors,
        ];
    }

    /**
     * @return Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getModelErrors()
    {
        return $this->modelErrors;
    }

}
