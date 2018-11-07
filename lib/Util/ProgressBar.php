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
        $this->progressBar = $this->initProgressBar($src_path);
    }

    public function addProgress(): void
    {
        $this->progressBar->update($this->file_cnt);
        $this->file_cnt += 1;
    }

    /**
     * @param string $src_path
     * @return Manager
     */
    private function initProgressBar($src_path): Manager
    {
        $total_php_cnt = 0;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src_path)) as $filename)
        {
            if ($filename->getExtension() !== "php") {
                continue;
            }

            $total_php_cnt += 1;
        }

        return new Manager(0, $total_php_cnt, 50, '█', ' ', '▋');
    }
}
