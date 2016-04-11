<?php
namespace lepota\exceptions;

class ForbiddenException extends AjaxException
{

    public function getHttpResponseCode()
    {
        return 403;
    }

}
