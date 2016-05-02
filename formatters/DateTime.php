<?php
namespace lepota\formatters;

class DateTime
{
    const DATE_TIME_SQL = 'Y-m-d H:i:sO';
    const DATE_SQL = 'Y-m-d';
    const DATE_TIME_ISO_8601 = 'Y-m-d\TH:i:sP';

    /**
     * Get date+time formatted for PostgreSQL timestamptz
     * @return string
     */
    public static function dateTimeSql()
    {
        return date(self::DATE_TIME_SQL);
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
     * Convert SQL datetime format to AJAX format: "2016-01-30T14:55+03:00" (ISO-8601)
     * @return string
     */
    public static function sqlToAjax($dateTime)
    {
        return str_replace(' ', 'T', $dateTime) . ':00';
    }

}
