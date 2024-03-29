<?php

namespace lepota\domain;

use yii\db\ActiveRecord;

interface Service
{

    /**
     * @param int $id
     * @param string|null $excludeStatus
     * @return ActiveRecord|null
     */
    public function get(int $id, string $excludeStatus = null);

    /**
     * @param int $limit
     * @param int|string|null $offset
     * @param string|null $filterStatus
     * @return array
     */
    public function list(int $limit, $offset = null, string $filterStatus = null): array;
}
