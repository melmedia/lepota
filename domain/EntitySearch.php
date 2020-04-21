<?php

namespace lepota\domain;

use Functional;

class EntitySearch
{
    protected function getCkeditorContentIndex($content)
    {
        if (!$content) {
            return '';
        }

        return Functional\reduce_left(
            Functional\filter($content, function (array $contentBlock): bool {
                return 'ckeditor' == $contentBlock['type'];
            }),
            function (array $contentBlock, $index, $collection, string $reduction = null) {
                return $reduction . ' ' . Functional\reduce_left(
                    $contentBlock['content'],
                    function (array $contentBlockItem, $index, $collection, string $reduction = null) {
                        switch ($contentBlockItem['type']) {
                            case 'text':
                                $reduction .= ' ' . self::html2text($contentBlockItem['content']);
                                break;

                            case 'image':
                                $reduction .= ' ' . self::html2text($contentBlockItem['content']['caption']);
                                break;
                        }

                        return $reduction;
                    }
                );
            }
        );
    }

    protected static function html2text(string $html): string
    {
        return html_entity_decode(strip_tags($html), ENT_QUOTES);
    }
}
