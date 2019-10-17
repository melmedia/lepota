<?php
namespace lepota\components;

use Yii;
use yii\base\Component;

class MessageTemplateRenderer extends Component
{
    /** @var string|FrontendClient */
    public $frontendClient;

    public function init()
    {
        $this->frontendClient = Yii::$app->get($this->frontendClient);
    }

    public function render(string $templateName, array $params = []): string
    {
        return $this->frontendClient->get("backend/email/{$templateName}", $params);
    }

}
