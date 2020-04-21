<?php

namespace lepota\domain;

/**
 * @property int $id
 */
interface DomainModel
{
    /**
     * @param array $values
     * @param bool $safeOnly
     */
    public function setAttributes($values, $safeOnly = true);

    /**
     * @param array|null $names
     * @param array $except
     * @return array
     */
    public function getAttributes($names = null, $except = []);
}
