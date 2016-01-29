<?php
namespace lepota\components\redis;

class Cache extends \yii\redis\Cache
{
    /** @var array Конфигурация подключения к redis, из конфига компонента */
    public $config;

    public function init()
    {
        $this->redis = $this->config;
        parent::init();
    }

    /**
     * Реализация yii2-redis версии 2.0.0 не обрабатывает connection timeout, так что при таймауте всё заканчивается
     * на fwrite Broken pipe, что даже не ловится внутри yii\redis\Connection, так что получается что _socket есть,
     * но пользоваться им нельзя.
     * Поэтому мы дёргаем команду PING и в случае ошибки просто переинициализируем cache->redis
     * Рекомендуется к применению в местах типа Worker::perform, в которых может произойти таймаут
     */
    public function checkConnection()
    {
        try {
            $this->redis->executeCommand('PING');
        }
        catch (\Exception $e) {
            $this->redis = null;
            $this->init();
        }
    }

}