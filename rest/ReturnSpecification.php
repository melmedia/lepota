<?php
namespace lepota\rest;

/**
 * Allow to specify desired format of REST endpoint:
 * return=only:count - limit response to only count of objects instead of objects list.
 * return=addition:field1,field2,... - comma-separated list of additional fields to return in list.
 */
class ReturnSpecification
{
    const MODE_ONLY = 'only';
    const MODE_ADDITION = 'addition';

    /** @var string */
    public $mode;
    /** @var string */
    public $only;
    /** @var string[] */
    public $additions = [];

    public function __construct(string $return = null)
    {
        if ($return) {
            list($this->mode, $parts) = explode(':', $return);
            $parts = explode(',', $parts);

            switch ($this->mode) {
                case self::MODE_ONLY:
                    $this->only = $parts[0];
                    break;

                case self::MODE_ADDITION:
                    $this->additions = $parts;
                    break;
            }
        }
    }

    /**
     * Check if only one field requested
     * @param string $only
     * @return bool
     */
    public function only(string $only): bool
    {
        return $only == $this->only;
    }

    /**
     * Check if addition requested
     * @param string $addition
     * @return bool
     */
    public function addition(string $addition): bool
    {
        return in_array($addition, $this->additions);
    }

}
