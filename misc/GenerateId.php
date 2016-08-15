<?php
namespace lepota\misc;

/**
 * Generate random text identifier
 */
class GenerateId
{
    /** Letters to generate ID from */
    const LETTERS = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    /** Letters to generate ID from */
    const DIGITS = "1234567890";

    public static function letters($length)
    {
        return self::randomSymbols(self::LETTERS, $length);
    }

    public static function digits($length)
    {
        return self::randomSymbols(self::DIGITS, $length);
    }

    public static function lettersAndNumbers($length)
    {
        return self::randomSymbols(self::LETTERS . self::DIGITS, $length);
    }

    protected static function randomSymbols($symbols, $length)
    {
        if ($length > strlen($symbols)) {
            $symbols = str_repeat($symbols, (int) ceil($length / strlen($symbols)));
        }
        return substr(str_shuffle($symbols), 0, $length);
    }

}
