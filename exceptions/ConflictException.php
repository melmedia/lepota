<?php
namespace lepota\exceptions;

/**
 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict between multiple simultaneous updates.
 */
class ConflictException extends AjaxException
{

    public function getHttpResponseCode(): int
    {
        return 409;
    }

}
