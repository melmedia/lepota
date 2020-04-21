<?php

namespace lepota\data;

use Functional;
use GuzzleHttp\Promise\Promise;

/**
 * Request entities one by one => Load few entities at time => Run callbacks to set data.
 */
abstract class BulkRequest
{
    protected $requests = [];

    /**
     * Realization must load entities by array of IDs and return array indexed by these IDs
     * @param int[] $ids
     * @return array Index is ID of entity
     */
    abstract protected function loadEntities(array $ids): array;

    /**
     * Request user by ID
     * @param int $id
     * @return Promise
     */
    public function get(int $id): Promise
    {
        $promise = new Promise();
        $this->requests[] = (object)[
            'id' => $id,
            'promise' => $promise,
        ];
        return $promise;
    }

    /**
     * Collect and run all requests
     */
    public function run()
    {
        $ids = Functional\unique(array_filter(Functional\pluck($this->requests, 'id')));
        $entities = [];
        if ($ids) {
            $entities = $this->loadEntities($ids);
        }

        foreach ($this->requests as $request) {
            $request->promise->resolve($entities[$request->id]);
        }
        $this->requests = [];
        $queue = \GuzzleHttp\Promise\queue();
        $queue->run();
    }
}
