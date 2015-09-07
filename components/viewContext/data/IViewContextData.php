<?php
namespace lepota\components\viewContext\data;

/**
 * Базовый класс для загрузчиков данных в ViewContext.
 */
interface IViewContextData
{

    /**
     * Вызывается в common.components.Controller::beforeAction или в момент вызова при отложенной загрузке
     * Возвращает данные: строку, массив, всё что будет помещено в ViewContext, null если данные отсутствуют
     * @param ..\ViewContext $ctx
     * @return mixed
     */
    public function load($ctx);

}
