<?php
namespace lepota\exceptions;

use yii\web\HttpException;

/**
 * Render error for AJAX
 *
 * Replace in subclasses method getDefaultMessage() and return error message.
 *
 * Redefine getDefaultMessage() in subclasses to customize error message.
 * Use Controller::renderException to render JSON response:
 * {errors: [{code: errorCode, message: errorMessage}]}
 * errorCode is formatted from exception class name (ProjectAccessDeniedException => projectAccessDenied)
 *
 * @see Controller::renderException
 */
class AjaxException extends HttpException
{

    /**
     * @param string|null $message Translated message
     */
    public function __construct($message = null)
    {
        parent::__construct($this->getHttpResponseCode(), $this->getAjaxMessage($message));
    }

    /**
     * Error message for user
     * @return string
     */
    public function getDefaultMessage()
    {
        return '';
    }

    /**
     * HTTP response code to return to client
     * @return int
     */
    public function getHttpResponseCode(): int
    {
        return 400;
    }

    /**
     * @param string|null $message
     * @return array
     */
    public function getAjaxResponse($message = null)
    {
        return [
            'code' => $this->getAjaxErrorCode(),
            'message' => null !== $message ? $message : $this->getAjaxMessage($message)
        ];
    }

    /**
     * Get error code from class name
     * Example: ProjectAccessDeniedError => projectAccessDenied
     * @return string
     */
    protected function getAjaxErrorCode()
    {
        $errorCode = (new \ReflectionClass($this))->getShortName();
        return preg_replace('/Exception$/', '', $errorCode);
    }

    /**
     * Get error message
     * @param string|null $message
     * @return mixed
     */
    protected function getAjaxMessage($message = null)
    {
        $variants = array_filter([
            $message,
            $this->getDefaultMessage(),
            $this->getMessage(),
        ]);
        return array_shift($variants);
    }

}
