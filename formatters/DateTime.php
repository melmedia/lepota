<?php
namespace lepota\formatters;

class DateTime
{
    const DATE_TIME_SQL = 'Y-m-d H:i:s';
    const DATE_SQL = 'Y-m-d';
    
    /**
     * Get date+time formatted for PostgreSQL timestamptz
     * @return string
     */
    public static function dateTimeSql()
    {
        return date(self::DATE_TIME_SQL);
    }

}

