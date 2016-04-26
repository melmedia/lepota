<?php
namespace lepota\formatters;

class DateTime
{
    const DATE_TIME_SQL = 'Y-m-d H:i:sO';
    const DATE_SQL = 'Y-m-d';
    
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

}
