<?php
namespace lepota\components\viewContext;

use Yii;
use yii\helpers\Url;

/**
 * Yii component Yii::$app->viewContext, use as $this->ctx in controllers
 *
 * @property mixed $global
 */
abstract class ViewContext
{

    /** @var bool $strictMode Строгий режим */
    public $strictMode = false;

    /** @var ViewContextContainer */
    protected $attributeContainer;

    /**
     * Должно вызываться в конструкторе класса-владельца
     * @throws \Exception
     */
    public function __construct()
    {
        $this->attributeContainer = new ViewContextContainer($this);
        $globalContainer = new ViewContextContainer($this);
        $globalContainer->init($this->globalAttributes());
        // не допускаем модификации ctx.global после инициализации компонента
        $globalContainer->setIsWritable(false);
        $this->attributeContainer->global = $globalContainer;
    }

    /**
     * Переменные в ctx->global
     * Данные приходят:
     * - из анонимной функции function (ViewContext $ctx): mixed
     * - из класса, реализующего интерфейс IViewContextData
     *
     * Могут присваиваться:
     * - сразу ViewContextInit::NOW
     * - при первом обращении ViewContextInit::LATER
     *
     * @return array
     */
    abstract protected function globalAttributes();

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function createUrl($route, $params = [])
    {
        return Url::to(array_merge([$route], $params));
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function createAbsoluteUrl($route, $params = [])
    {
        return Url::to(array_merge([$route], $params), true);
    }

    /**
     * Return current page absolute url
     * @return string
     */
    protected function createPageUrl()
    {
        return Url::canonical();
    }

    /**
     * Return main website page link
     * @return string
     */
    protected function createHomeUrl()
    {
        return Url::home();
    }

    /**
     * @return string
     */
    protected function getAppName()
    {
        return Yii::$app->name;
    }

    /**
     * Назначаем данные скопом
     *
     * @param array $data
     */
    public function assign($data = array())
    {
        foreach ($data as $attribute => $value) {
            $this->set($attribute, $value);
        }
    }

    /**
     * Получаем переменную
     *
     * @param string $attribute
     * @return mixed
     * @throws ViewContextUndefinedVariableException
     */
    public function __get($attribute)
    {
        return $this->get($attribute);
    }

    /**
     * Получаем переменную
     *
     * @param string $attribute
     * @return mixed
     * @throws ViewContextUndefinedVariableException
     */
    public function get($attribute)
    {
        return $this->attributeContainer->$attribute;
    }

    /**
     * Назначаем переменную
     *
     * @param string $attribute
     * @param mixed $value
     * @throws ViewContextReadOnlyException
     */
    public function __set($attribute, $value)
    {
        $this->set($attribute, $value);
    }

    /**
     * Назначаем переменную
     *
     * @param string $attribute
     * @param $value
     * @throws ViewContextReadOnlyException
     */
    public function set($attribute, $value)
    {
        $this->attributeContainer->$attribute = $value;
    }

    /**
     * Проверяем существование аттрибута
     *
     * @param string $attribute
     * @return bool
     */
    public function has($attribute)
    {
        return isset($this->attributeContainer->$attribute);
    }

    /**
     * Проверяем существование аттрибута
     *
     * @param string $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        return $this->has($attribute);
    }

}

class ViewContextUndefinedVariableException extends \Exception
{}

class ViewContextReadOnlyException extends \Exception
{}
