<?php

namespace lepota\config;

/**
 * Контейнер свойств конфига (как одного файла, так и всего приложения)
 */
class ParametersContainer
{
    protected $params = [];

    public function __construct($params = null)
    {
        if (is_array($params)) {
            $this->params = $params;
        }
    }

    /**
     * Добавляет только новые параметры, если такой параметр уже есть - выбрасывает исключение
     *
     * @param array $params
     * @throws ConfigError
     */
    public function append($params)
    {
        foreach ($params as $param => $value) {
            if (isset($this->params[$param])) {
                throw new ConfigError("Parameter $param is already defined in base configuration");
            }
            $this->params[$param] = $value;
        }
    }

    /**
     * Добавляет только новые параметры, если такой параметр уже есть - новое значение игнорируется
     *
     * @param array $params
     * @throws ConfigError
     */
    public function appendOrIgnore($params)
    {
        foreach ($params as $param => $value) {
            if (!isset($this->params[$param])) {
                $this->params[$param] = $value;
            }
        }
    }

    /**
     * Объединяет параметры конфигов рекурсивно, в случае совпадения ключей объединяет их
     * в массив (array_merge_recursive)
     *
     * @param array $params
     */
    public function merge($params)
    {
        $this->params = array_merge_recursive($this->params, $params);
    }

    /**
     * Объединяет параметры конфигов рекурсивно, в случае совпадения ключей новые значения
     * перезаписывают старые (CMap::mergeArray)
     *
     * @param array $params
     */
    public function extend($params)
    {
        $this->params = self::mergeArray($this->params, $params);
    }

    /**
     * Следует по вложенным ключам $path и заменяется последнее значение на $value
     *
     * Было:
     * ['features' => [
     *   'timeline' => ['__enabled', 'dashboard' => ['__enabled']]]
     * ]
     *
     * replace(['features', 'timeline'], ['__disabled'])
     *
     * Стало:
     * ['features' => ['timeline' => ['__disabled']]]
     *
     * @param array $path Вложенные ключи
     * @param mixed $value Перезаписываемое значение
     */
    public function replace(array $path, $value)
    {
        $this->params = self::replaceInternal($this->params, $path, $value);
    }

    protected static function replaceInternal($params, $path, $value)
    {
        $param = array_shift($path);
        if (empty($path)) {
            $params[$param] = $value;
        } else {
            $params[$param] = self::replaceInternal($params[$param], $path, $value);
        }
        return $params;
    }

    /**
     * Следует по вложенным ключам $path и удаляет последнее значение
     *
     * Было:
     * ['services' => ['vontakte' => ..., 'facebook' => ...]]
     *
     * remove(['services', 'vkontakte'])
     *
     * Стало:
     * ['services' => ['facebook' => ...]]
     *
     * @param array $path
     */
    public function remove(array $path)
    {
        $this->params = self::removeInternal($this->params, $path);
    }

    protected static function removeInternal($params, $path)
    {
        $key = array_shift($path);
        if (!$path) {
            unset($params[$key]);
        } else {
            $params[$key] = self::removeInternal($params[$key], $path);
        }
        return $params;
    }

    /**
     * Скопировано из CMap
     * @param array $a
     * @param array $b
     * @return array|mixed
     */
    public static function mergeArray($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::mergeArray($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    public function toArray()
    {
        return $this->params;
    }
}
