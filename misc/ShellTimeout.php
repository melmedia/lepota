<?php
namespace lepota\misc;

use Exception;

/**
 * Execute shell command with timeout
 */
class ShellTimeout extends Shell
{
    const TIMEOUT_EXIT_STATUS_CODE = 124;

    /** @var string 10s, 1m, ... */
    protected $timeout;

    public function __construct(string $timeout = '5s')
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $command
     * @return bool false=timeout, else true
     */
    public function execute(string $command): bool
    {
        $output = [];
        $exitCode = 0;
        exec("timeout {$this->timeout} {$command}", $output, $exitCode);
        if (self::TIMEOUT_EXIT_STATUS_CODE === $exitCode) {
            return false;
        }
        if (0 !== $exitCode) {
            throw new Exception("Command '{$command}' failed, exit code $exitCode, output: " . var_export($output, true));
        }
        return true;
    }

}
