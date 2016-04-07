<?php
namespace lepota\components;

use Yii;
use lepota\exceptions\AjaxException;

class AjaxController extends \yii\rest\Controller
{

    /**
     * Render AJAX exception
     * @param AjaxException $e
     */
    public function renderException(AjaxException $e)
    {
        $response = Yii::$app->response;
        $response->statusCode = $e->getHttpResponseCode();
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $e->getAjaxResponse();
    }

}
