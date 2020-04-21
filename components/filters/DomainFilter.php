<?php

namespace lepota\components\filters;

use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class DomainFilter extends Behavior
{
    /** @var string */
    public $deny;

    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    /**
     * @param ActionEvent $event
     * @return boolean
     */
    public function beforeAction($event)
    {
        if (strpos(Yii::$app->request->hostInfo, 'http://' . $this->deny) !== false) {
            $event->isValid = false;
            throw new ForbiddenHttpException();
        }

        return $event->isValid;
    }
}
