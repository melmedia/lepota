<?php

namespace lepota\rest;

/**
 * Allow to specify desired format of REST endpoint:
 * return=only:count - limit response to only count of objects instead of objects list.
 * return=addition:field1,field2,... - comma-separated list of additional fields to return in list.
 */
class ReturnSpecification
{
    protected const MODE_ONLY = 'only';
    protected const MODE_ADDITION = 'addition';

    /** @var string */
    public $mode;
    /** @var string */
    public $only;
    /** @var string[] */
    public $additions = [];

    /**
     * Filter input parameters by prefix, remove prefix from parameter names
     *
     * @param array $queryParams param => value hashmap
     * @param string $prefix
     * @return array
     */
    public static function getQueryParamsByPrefix(array $queryParams, string $prefix): array
    {
        $params = [];
        $prefixLength = strlen($prefix);
        foreach ($queryParams as $param => $value) {
            if (0 !== strncmp($param, $prefix, $prefixLength)) {
                continue;
            }
            $params[substr($param, $prefixLength)] = $value;
        }
        return $params;
    }

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
