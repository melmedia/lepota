<?php

namespace lepota\domain;

interface Factory
{

    /**
     * @return DomainModel
     */
    public function createEmpty();
}
