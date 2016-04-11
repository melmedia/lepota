<?php
namespace lepota\exceptions;

class UnauthorizedException extends AjaxException
{

    public function getHttpResponseCode()
    {
        return 401;
    }

}
