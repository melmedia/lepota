<?php

namespace lepota\config;

/**
 * Требования к классу-владельцу:
 * Содержит ParametersContainer $params
 *
 * @property ParametersContainer $params
 */
trait ParametersContainerTrait
{
    /**
     * Добавляет только новые параметры, если такой параметр уже есть - выбрасывает исключение
     *
     * @param array $params key => value
     * @return $this
     * @throws ConfigError
     */
    public function append($params)
    {
        $this->params->append(self::wrapArgs(func_get_args()));
        return $this;
    }

    /**
     * Добавляет только новые параметры, если такой параметр уже есть - новое значение игнорируется
     *
     * @param array $params key => value
     * @return $this
     * @throws ConfigError
     */
    public function appendOrIgnore($params)
    {
        $this->params->appendOrIgnore(self::wrapArgs(func_get_args()));
        return $this;
    }

    /**
     * Объединяет параметры конфигов рекурсивно, в случае совпадения ключей объединяет их
     * в массив (array_merge_recursive)
     *
     * @param array $params key => value
     * @return $this
     */
    public function merge($params)
    {
        $this->params->merge(self::wrapArgs(func_get_args()));
        return $this;
    }

    /**
     * Объединяет параметры конфигов рекурсивно, в случае совпадения ключей новые значения перезаписывают
     * старые (CMap::mergeArray)
     *
     * @param array $params key => value
     * @return $this
     */
    public function extend($params)
    {
        $this->params->extend(self::wrapArgs(func_get_args()));
        return $this;
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
     * @return $this
     */
    public function replace($path, $value)
    {
        $this->params->replace($path, $value);
        return $this;
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
     * @return $this
     */
    public function remove($path)
    {
        $this->params->remove($path);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->params->toArray();
    }

    protected static function wrapArgs($args)
    {
        if (2 == count($args)) {
            list($param, $value) = $args;
            if ($value instanceof ConfigFile) {
                $value = $value->toArray();
            }
            return [$param => $value];
        } else {
            return $args[0];
        }
    }
}
