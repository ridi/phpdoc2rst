<?php

namespace Util;

require_once ROOT_DIR.'/vendor/autoload.php';
use ProgressBar\Manager;

class PDRProgressBar
{

    /**
     * @var Manager
     */
    private $progressBar;

    /**
     * @var int
     */
    private $file_cnt;

    /**
     * PDRProgressBar constructor.
     * @param string $src_path
     */
    public function __construct($src_path)
    {
        $this->file_cnt = 1;
        $this->progressBar = $this->initProgressBar($src_path);
    }

    /**
     * @param string $src_path
     * @return Manager
     */
    function initProgressBar($src_path) {
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

    public function addProgress() {
        $this->progressBar->update($this->file_cnt);
        $this->file_cnt += 1;
    }
}
