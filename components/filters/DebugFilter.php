<?php
namespace lepota\components\filters;

use yii\base\ActionFilter;

/**
 * Restrict actions to run only in dev environment
 */
class DebugFilter extends ActionFilter
{

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param \yii\base\Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        return defined('YII_DEBUG') && YII_DEBUG;
    }

}
