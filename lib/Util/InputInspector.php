<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use Exception;

class InputInspector
{
    /**
     * @param array $argv
     * @throws Exception
     */
    public static function validate(array $argv): void
    {
        if (count($argv) <= 1) {
            throw new Exception("Target path is necessary for docs.");
        } elseif (!is_dir($argv[1])) {
            throw new Exception("Target path does not exist.");
        }
    }
}