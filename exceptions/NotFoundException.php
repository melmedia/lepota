<?php

namespace lepota\exceptions;

class NotFoundException extends AjaxException
{
    public function getHttpResponseCode(): int
    {
        return 404;
    }
}
