<?php
namespace lepota\domain;

interface Factory
{

    /**
     * @return DomainModel
     */
    function createEmpty();

}