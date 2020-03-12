<?php

namespace lepota\config;

/**
 * Обёртка вокруг единичного конфиг-файла, возвращающего массив с параметрами
 */
class ConfigFile
{
    use ParametersContainerTrait;

    protected $params;

    public function __construct($filePath)
    {
        $this->params = $this->load($filePath);
    }

    /**
     * @param string $configPath
     * @return null|ParametersContainer
     */
    protected function load($configPath)
    {
        $config = include $configPath;
        return $config instanceof self ? $config->params :
            new ParametersContainer(is_array($config) ? $config : null);
    }
}
