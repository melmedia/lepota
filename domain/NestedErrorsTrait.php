<?php
namespace lepota\domain;

/**
 * Errors for nested objects stored as object (not array):
 * {
 *   "code": "EntityValidation",
 *   "validationErrors": {
 *     "mainPageTiles": {
 *       "activeType": ["Must be one of values of list \"type\" attribute"]
 *     }
 *   }
 * }
 */
trait NestedErrorsTrait
{
    protected $nestedErrors = [];

    /**
     * Adds a new error to the specified nested object
     * @param string $envelope attribute name of nested object
     * @param string $error new error message
     */
    public function setNestedErrors($envelope, $error)
    {
        $this->nestedErrors[$envelope] = $error;
    }

    public function getErrors($attribute = null)
    {
        return parent::getErrors($attribute) + $this->nestedErrors;
    }

}
