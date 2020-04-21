<?php

namespace lepota\domain;

use yii\db\ActiveRecord;

class StorageSpecification
{
    /** @var array Hashmap 'relationName' => ActiveRecord */
    public $saveBefore = [];
    /** @var array Hashmap 'relationName' => ActiveRecord */
    public $saveAfter = [];
    /** @var array Hashmap 'relationName' => ActiveRecord */
    public $deleteAfter = [];
    /** @var ActiveRecord */
    public $rootAR;

    public function saveBefore(array $relations)
    {
        $this->saveBefore = $relations;
    }

    public function saveAfter(array $relations)
    {
        $this->saveAfter = $relations;
    }

    public function deleteAfter(array $relations)
    {
        $this->deleteAfter = $relations;
    }

    public function merge(StorageSpecification $specification)
    {
        $this->saveBefore += $specification->saveBefore;
        $this->saveAfter += $specification->saveAfter;
        $this->deleteAfter += $specification->deleteAfter;
    }
}
