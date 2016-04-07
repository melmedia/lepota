<?php
namespace lepota\exceptions;

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
 * @see MainController::renderException
 */
class AjaxException extends \Exception
{

    /**
     * Текст для пользователя, описывающий ошибку (пропущенный через Yii::t)
     * @return string
     */
    public function getDefaultMessage()
    {
        return '';
    }

    /**
     * HTTP-код ответа, отправляемый клиенту при выбрасывании этой ошибки.
     * Есть заготовленные подклассы Forbidden, NotFound и Unauthorized с соответствующими HTTP-кодами
     *
     * @return int
     */
    public function getHttpResponseCode()
    {
        return 400;
    }

    /**
     * @param null $message
     * @return array
     */
    public function getAjaxResponse($message = null)
    {
        return [
            [
                'code' => $this->getAjaxErrorCode(),
                'message' => null !== $message ? $message : $this->getAjaxMessage($message)
            ]
        ];
    }

    /**
     * Получаем из имени класса код ошибки
     * Например: ProjectAccessDeniedError => projectAccessDenied
     * @return string
     */
    protected function getAjaxErrorCode()
    {
        $errorCode = (new \ReflectionClass($this))->getShortName();
        return lcfirst(preg_replace('/Exception$/', '', $errorCode));
    }

    /**
     * Получаем сообщение
     * @param null $message
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
