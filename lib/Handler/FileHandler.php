<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Handler;

use PhpDoc2Rst\Util\ProgressBar;

class FileHandler
{
    /**
     * @var string
     */
    private $top_prefix;

    /**
     * @var string
     */
    private $target_src;

    /**
     * @var PDRProgressBar
     */
    private $progressBar;

    /**
     * @var StringHandler
     */
    private $strHandler;

    /**
     * @param string $top_prefix
     */
    public function __construct(string $top_prefix = 'Ridibooks')
    {
        $this->top_prefix = $top_prefix;
        $this->target_src = "src/$top_prefix";

        $this->progressBar = new ProgressBar(ROOT_DIR."/$this->target_src");
        $this->strHandler = new StringHandler();

        $this->initRootDir();
    }

    public function convertDocsToRsts(): void
    {
        $this->getDirContents(ROOT_DIR."/$this->target_src", $this->top_prefix);
    }

    /**
     * @param string $path
     */
    public function makeDirIfNotExist(string $path): void
    {
        if (!file_exists($path) && !is_dir($path) && pathinfo($path, PATHINFO_EXTENSION) === "") {
            mkdir($path);
        }
    }

    private function initRootDir(): void
    {
        $this->makeDirIfNotExist(DOC_DIR."/$this->top_prefix");
        exec($this->strHandler->makeRstTitleCmd($this->target_src) . " > " .
            $this->strHandler->makeIdxRstFilePath($this->top_prefix));
    }

    /**
     * @param string $dir
     * @param string $root_prefix
     */
    private function getDirContents(string $dir, string $root_prefix): void
    {
        $file_cmd = [];
        $first_dir = true;

        $files = scandir($dir);

        foreach ($files as $file_name)
        {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$file_name);
            $my_name = pathinfo($path, PATHINFO_FILENAME);

            if (is_dir($path) && $file_name !== "." && $file_name !== "..") {
                $this->makeDirIfNotExist(DOC_DIR."/$root_prefix/$my_name");

                exec($this->strHandler->makeRstTitleCmd("/$my_name") . " > " .
                    $this->strHandler->makeIdxRstFilePath("$root_prefix/$my_name"));
                exec($this->strHandler->addRstListChildCmd($my_name, $first_dir) . " >> " .
                    $this->strHandler->makeIdxRstFilePath($root_prefix));

                $this->getDirContents($path, "$root_prefix/$my_name");
            } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $file_cmd[] = "{ " . implode(";", [
                        $this->strHandler->makeRstTitleCmd($my_name, '-'),
                        $this->strHandler->convertRstCmd($path),
                        $this->strHandler->addNewlineCmd()
                    ]) . ";}>> " . $this->strHandler->makeIdxRstFilePath($root_prefix);

                $this->progressBar->addProgress();
            }
        }

        if (!$first_dir) {
            exec("echo \"\" >> " . $this->strHandler->makeIdxRstFilePath($root_prefix));
        }

        foreach ($file_cmd as $cmd)
        {
            exec($cmd);
        }
    }
}
