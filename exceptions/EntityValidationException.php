<?php
namespace lepota\exceptions;

use Functional;
use Yii;
use yii\base\Model;

/**
 * Model validation errors wrapper
 */
class EntityValidationException extends AjaxException
{
    /** @var Model|null */
    protected $model;
    /** @var array Attributes validation errors */
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

    /**
     * Shortcut to create validation exception with one attribute is required
     * @param string $paramName
     * @return EntityValidationException
     */
    public static function paramRequired($paramName)
    {
        return new self([
            $paramName => [Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $paramName])]
        ]);
    }

    /**
     * Shortcut to create validation exception with one attribute of object is required
     * @param string $envelope
     * @param string $paramName
     * @return EntityValidationException
     */
    public static function nestedParamRequired($envelope, $paramName)
    {
        return new self([
            $envelope => [
                $paramName => [Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $paramName])]
            ]
        ]);
    }

    /**
     * Shortcut to create validation exception with several attributes required
     * @param string[] $params
     * @return EntityValidationException
     */
    public static function paramsAreRequired($params)
    {
        $errors = array_combine(
            $params,
            Functional\map($params, function ($paramName) {
                return [Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $paramName])];
            })
        );
        return new self($errors);
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
