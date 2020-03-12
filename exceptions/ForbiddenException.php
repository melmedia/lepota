<?php

namespace lepota\exceptions;

/**
 * The request was a valid request, but the server is refusing to respond to it.
 * 403 error semantically means "unauthorized", i.e. the user does not have the necessary permissions for the resource.
 */
class ForbiddenException extends AjaxException
{
    public function getHttpResponseCode(): int
    {
        return 403;
    }
}
