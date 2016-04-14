<?php
namespace lepota\components;

class Response extends \yii\web\Response
{

    /**
     * Add Link header to response, indicating url relation
     * @param string $url
     * @param string $relation
     */
    public function setLinkHeader($url, $relation)
    {
        $this->headers->add('Link', $url . '; rel="' . $relation . '"');
    }
    
}