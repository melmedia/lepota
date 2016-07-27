<?php
namespace lepota\formatters;

class Url
{

    /**
     * Convert cyrillic text to valid url
     * @param string $text
     * @param string $charset
     * @return string
     */
    public static function fromText($text, $charset)
    {
        return
            preg_replace('~[^a-z0-9_-]~', '',
                mb_strtolower(
                str_replace(' ', '_',
                    Transliteration::ru2Lat(
                        trim($text)
                    )
                ),
                $charset
            )
        );
    }

}
