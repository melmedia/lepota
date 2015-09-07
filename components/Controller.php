<?php
namespace lepota\components;

class Controller extends \yii\web\Controller
{
    /**
     * @var \lepota\components\viewContext\ViewContext
     * @see Yii::$app->viewContext
     * @see main/config/common/components/viewContext.php
     */
    public $ctx;


    public function init()
    {
        $this->ctx = \Yii::$app->viewContext;
    }

}
