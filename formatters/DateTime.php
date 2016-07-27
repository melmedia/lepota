<?php
namespace lepota\formatters;

class DateTime
{
    const DATE_TIME_SQL = 'Y-m-d H:i:sO';
    const DATE_SQL = 'Y-m-d';
    const DATE_TIME_ISO_8601 = 'Y-m-d\TH:i:sP';

    /**
     * Get date+time formatted for PostgreSQL timestamptz
     * @param int|null $timestamp
     * @return string
     */
    public static function dateTimeSql(int $timestamp = null): string
    {
        return date(self::DATE_TIME_SQL, $timestamp);
    }

    /**
     * Cut time from datetime string, return only date part
     * @param string $dateTime
     * @return string
     */
    public static function onlyDate($dateTime)
    {
        $parts = explode(' ', $dateTime);
        if (!$parts) {
            return $dateTime;
        }
        return $parts[0];
    }

    /**
     * Convert SQL datetime format to AJAX format: "2016-01-30T14:55:00+03:00" (ISO-8601)
     * @return string
     */
    public static function sqlToAjax($dateTime)
    {
        return str_replace(' ', 'T', $dateTime) . ':00';
    }

    /**
     * Convert AJAX datetime format to SQL format: "2016-01-30 14:55:00+03"
     * @return string
     */
    public static function ajaxToSql($dateTime)
    {
        return str_replace('T', ' ', $dateTime);
    }

}
