<?php
namespace lepota\components;

class Twig extends \evertwig\TwigComponent
{

    function init()
    {
        parent::init();

        if (\Yii::$app->has('viewContext')) {
            $this->addGlobals(['ctx' => \Yii::$app->viewContext]);
        }
    }

}
