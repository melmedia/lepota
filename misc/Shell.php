<?php
namespace lepota\misc;

use Exception;

class Shell
{

    /**
     * @param string $command
     * @return bool Always true
     */
    public function execute(string $command): bool
    {
        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        if (0 !== $exitCode) {
            throw new Exception("Command '{$command}' failed, exit code $exitCode, output: " . var_export($output, true));
        }
        return true;
    }

}
