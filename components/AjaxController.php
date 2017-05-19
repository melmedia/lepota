<?php
namespace lepota\components;

use Functional;
use Yii;
use lepota\exceptions\AjaxException;

class AjaxController extends \yii\rest\Controller
{

    public function behaviors()
    {
        return Functional\map(
            Functional\reject(parent::behaviors(), function($filter, $index) {
                return 'authenticator' == $index || 'rateLimiter' == $index;
            }),
            function ($filter, $f) {
                if ('contentNegotiator' == $f) {
                    $filter['formats'] = [
                        'application/json' => Response::FORMAT_JSON,
                    ];
                }
                return $filter;
            });
    }

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
