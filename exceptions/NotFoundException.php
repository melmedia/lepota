<?php
namespace lepota\exceptions;

class NotFoundException extends AjaxException
{

    public function getHttpResponseCode()
    {
        return 404;
    }

}
