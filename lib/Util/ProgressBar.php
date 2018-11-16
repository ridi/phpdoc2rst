<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use ProgressBar\Manager;

class ProgressBar
{
    /**
     * @param string $src_path
     * @return Manager
     */
    public static function create($src_path): Manager
    {
        $total_php_cnt = count(
            array_filter(
                iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src_path))),
                function (\SplFileInfo $file): bool {
                    return $file->getExtension() === 'php';
                }
            )
        );

        return new Manager(0, $total_php_cnt, 50, '█', ' ', '▋');
    }
}
