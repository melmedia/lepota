<?php
namespace lepota\exceptions;

/**
 * General model storage error.
 * Error code may be 'EntityStorage' or 'AlreadyExists' if duplicateKey is true
 */
class EntityStorageException extends AjaxException
{
    /** @var bool Can be used for special handing of duplicate key error */
    public $duplicateKey = false;

    /**
     * EntityStorageException constructor.
     * @param \yii\db\Exception|null $dbException
     * @param string|null $message
     */
    public function __construct($dbException = null, $message = null)
    {
        parent::__construct($message);
        if ($dbException instanceof \yii\db\Exception) {
            if ('23505' == $dbException->getCode()) {
                // SQLSTATE[23505]: Unique violation: ERROR: duplicate key value violates unique constraint
                $this->duplicateKey = true;
            }
        }
    }

    public function getHttpResponseCode()
    {
        if ($this->duplicateKey) {
            return 400;
        }
        return 500;
    }

    protected function getAjaxErrorCode()
    {
        if ($this->duplicateKey) {
            return 'AlreadyExists';
        }
        return parent::getAjaxErrorCode();
    }

}
