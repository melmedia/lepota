<?php
namespace lepota\components\viewContext;

use Closure;

class AttributeLoader
{

    /**
     * @var ViewContextContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $attr;

    /**
     * @var Closure|string Источник данных
     */
    protected $loader;

    /**
     * @var bool Вызывать загрузчик при инициализации
     */
    protected $loadOnInit;

    protected $loaded = false;


    /**
     * @param ViewContextContainer $container
     * @param string $attr
     * @param Closure|string $loader
     * @param $init
     */
    public function __construct($container, $attr, $loader, $init)
    {
        $this->container = $container;
        $this->attr = $attr;
        $this->loader = $loader;
        $this->loadOnInit = (ViewContextInit::NOW == $init);
    }

    public function init($ctx)
    {
        if ($this->loadOnInit) {
            $this->load($ctx);
        }
    }

    /**
     * Загружаем значение атрибута
     *
     * @param ViewContext $ctx
     * @param bool $returnValue true: вызывает container->set, false: возвращает значение
     * @return mixed Если $resultValue=true возвращает полученное значение
     */
    public function load($ctx, $returnValue = false)
    {
        $result = null;
        if ($this->loaded) {
            return $result;
        }

        $value = null;
        if (is_string($this->loader)) {
            $loaderClass = $this->loader;
            /** @var data\IViewContextData $loader */
            $loader = new $loaderClass;
            $value = $loader->load($ctx);
        }
        else if (is_callable($this->loader)) {
            $loaderClosure = $this->loader;
            $value = $loaderClosure($ctx);
        }

        if (null !== $value) {
            // вся это хрень с returnValue нужна для того, чтобы загружать атрибуты по требованию,
            // когда загрузка global уже запрещена
            if ($returnValue) {
                $result = $value;
            }
            else {
                $this->container->set($this->attr, $value);
            }
        }
        $this->loaded = true;

        return $result;
    }

    /**
     * Это значение уже загружено?
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

}
