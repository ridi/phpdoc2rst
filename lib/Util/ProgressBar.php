<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use ProgressBar\Manager;

class ProgressBar
{

    /**
     * @var Manager
     */
    private $progressBar;

    /**
     * @var int
     */
    private $file_cnt = 1;

    /**
     * @param string $src_path
     */
    public function __construct(string $src_path)
    {
        $this->initProgressBar($src_path);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addProgress(): void
    {
        $this->progressBar->update($this->file_cnt);
        $this->file_cnt += 1;
    }

    /**
     * @param string $src_path
     */
    private function initProgressBar($src_path): void
    {
        $total_php_cnt = count(
            array_filter(
                iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src_path))),
                function (\SplFileInfo $file): bool {
                    return $file->getExtension() === 'php';
                }
            )
        );

        $this->progressBar = new Manager(0, $total_php_cnt, 50, '█', ' ', '▋');
    }
}
