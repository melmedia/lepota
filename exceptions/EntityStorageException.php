<?php
namespace lepota\exceptions;

/**
 * General model storage error.
 * Error code may be 'EntityStorage' or 'AlreadyExists' if duplicateKey is true
 */
class EntityStorageException extends AjaxException
{
    /** @var bool Can be used for special handing of duplicate key error */
    public $isDuplicatedKey = false;

    /** @var string Name of violated constraint */
    public $constraint;

    /**
     * EntityStorageException constructor.
     * @param \yii\db\Exception|null $dbException
     * @param string|null $message
     */
    public function __construct($dbException = null, $message = null)
    {
        if ($dbException instanceof \yii\db\Exception) {
            if ('23505' == $dbException->errorInfo[0]) {
                // SQLSTATE[23505]: Unique violation: ERROR: duplicate key value violates unique constraint
                $this->isDuplicatedKey = true;
                if (isset($dbException->errorInfo[2])) {
                    preg_match('~constraint "([^"]+)"~', $dbException->errorInfo[2], $matches);
                    if (isset($matches[1])) {
                        $this->constraint = $matches[1];
                    }
                }
            }
        }
        parent::__construct($message);
    }

    public function getHttpResponseCode(): int
    {
        if ($this->isDuplicatedKey) {
            return 400;
        }
        return 500;
    }

    protected function getAjaxErrorCode()
    {
        if ($this->isDuplicatedKey) {
            return 'AlreadyExists';
        }
        return parent::getAjaxErrorCode();
    }

}
