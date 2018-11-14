<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use Exception;

class InputInspector
{
    /**
     * @param string[] $argv
     * @return string[]
     * @throws Exception
     */
    public static function getTargetDirPaths(array $argv): array
    {
        if (count($argv) < 3) {
            throw new Exception("Target path is necessary.");
        } elseif (count($argv) > 3) {
            throw new Exception("Too much parameter.");
        }

        foreach (["source" => $argv[1], "docs" => $argv[2]] as $target_title => $target_path) {
            if (!realpath($target_path)) {
                throw new Exception("Unable to get target $target_title directory realpath.");
            } elseif (!is_dir($target_path)) {
                throw new Exception("Target $target_title path is not directory.");
            }
        }

        return [realpath(dirname($argv[0])), realpath($argv[1]), realpath($argv[2])];
    }
}
