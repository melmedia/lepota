<?php

namespace lepota\domain;

use Yii;
use lepota\domain\ImmutableObject;

/**
 * @property \stdClass $identity
 * @property \stdClass $profile
 */
class User extends ImmutableObject
{
    protected function initObject()
    {
        return Yii::$app->userClient->get("user/{$this->id}")->user;
    }
}
