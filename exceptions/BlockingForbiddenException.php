<?php
namespace lepota\exceptions;

/**
 * Exception returning extended information about blocking in AJAX response
 */
class BlockingForbiddenException extends ForbiddenException
{
    /** @var array ['user' => ['id' => ..., 'firstName' => ..., 'lastName' => ...]] */
    protected $blocking;
    /** @var string */
    protected $envelopeName;

    public function __construct($blocking, string $envelopeName = 'blocking')
    {
        parent::__construct();
        $this->blocking = $blocking;
        $this->envelopeName = $envelopeName;
    }

    public function getAjaxResponse($message = null)
    {
        $response = parent::getAjaxResponse($message);
        $response[0][$this->envelopeName] = $this->blocking;
        return $response;
    }

}
