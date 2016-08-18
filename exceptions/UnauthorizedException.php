<?php
namespace lepota\exceptions;

class UnauthorizedException extends AjaxException
{

    public function getHttpResponseCode(): int
    {
        return 401;
    }

}
