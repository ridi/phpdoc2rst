<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use Exception;

class InputInspector
{
    /**
     * @param array $argv
     * @return string
     * @throws Exception
     */
    public static function getRootDir(array $argv): string
    {
        if (count($argv) <= 1) {
            throw new Exception("Target directory path is necessary for docs.");
        } elseif (!file_exists($argv[1])) {
            throw new Exception("Target directory path does not exist.");
        } elseif (!is_dir($argv[1])) {
            throw new Exception("Target path is not directory.");
        }

        return $argv[1];
    }
}
