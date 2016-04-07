<?php
namespace lepota\exceptions;

use CDbException;

/**
 * General model storage error
 */
class EntityStorageException extends AjaxException
{
    /** @var bool Can be used for special handing of duplicate key error */
    public $duplicateKey = false;

    public function __construct($dbException = null)
    {
        if ($dbException instanceof CDbException) {
            if ('23505' == $dbException->getCode()) {
                // SQLSTATE[23505]: Unique violation: ERROR: duplicate key value violates unique constraint
                $this->duplicateKey = true;
            }
        }
    }

}
