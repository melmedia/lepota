<?php

namespace lepota\domain;

use Yii;
use lepota\domain\ImmutableObject;

class Tag extends ImmutableObject
{
    protected function initObject()
    {
        return Yii::$app->navigationClient->get("tag", ['id' => $this->id])->tags[0];
    }
}
