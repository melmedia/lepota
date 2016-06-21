<?php
namespace lepota\redisEvents;

use Redis;
use League\Event\{
    CallbackListener, Emitter, EmitterInterface, EventInterface, ListenerInterface
};
use yii\base\Configurable;

/**
 * Events handled by Redis (yii2 component). Channel name is event name
 */
class RedisEmitter extends Emitter implements Configurable
{
    /** @var Redis Redis connection instance */
    protected $redis;

    public function __construct($config = ['host' => '127.0.0.1', 'port' => 6379, 'database' => 0])
    {
        $this->redis = new Redis;
        if (!$this->redis->connect($config['host'], $config['port'])) {
            throw new \Exception("Can't connect to redis with parameters " . json_encode($config));
        }
        if (!$this->redis->select($config['database'])) {
            throw new \Exception("Can't select redis database " . $config['database']);
        }
    }

    /**
     * Redis 'subscribe' command callback
     * @param Redis $redis
     * @param string $channel
     * @param string $message
     */
    public function receive($redis, $channel, $message)
    {
        $message = json_decode($message);
        var_dump($message);
        $this->invokeListeners($channel, $message['event'], $message['arguments']);
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($event, $listener, $priority = self::P_NORMAL)
    {
        var_dump($this->redis->subscribe([$event], [$this, 'receive']));
        return parent::addListener($event, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($event, $listener)
    {
        parent::removeListener($event, $listener);
        if (!$this->hasListeners($event)) {
            $this->redis->unsubscribe($event);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllListeners($event)
    {
        $this->redis->unsubscribe($event);
        return parent::removeAllListeners($event);
    }

    /**
     * {@inheritdoc}
     */
    public function emit($event)
    {
        list($name, $event) = $this->prepareEvent($event);
        $arguments = [$event] + func_get_args();
        var_dump($this->redis->publish($name, json_encode(['message' => $event, 'arguments' => $arguments])));
        return $event;
    }


}
