<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Handler;

use PhpDoc2Rst\Util\ProgressBar;

class FileHandler
{
    /**
     * @var string
     */
    private $root_dir;

    /**
     * @var string
     */
    private $doc_dir;

    /**
     * @var string
     */
    private $target_src = "src/Ridibooks/Api";

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @param string $root_dir
     */
    public function __construct(string $root_dir)
    {
        $this->root_dir = $root_dir;
        $this->doc_dir = "$root_dir/docs";
        $this->progressBar = new ProgressBar("$root_dir/$this->target_src");

        $this->initRootDir();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function convertDocsToRsts(): void
    {
        if (is_dir("$this->root_dir/$this->target_src")) {
            $this->getDirContents("$this->root_dir/$this->target_src", $this->target_src);
        }
    }

    /**
     * @param string $path
     */
    public function makeDirIfNotExist(string $path): void
    {
        $top_path = "";

        foreach (explode("/", $path) as $each_dir) {
            $top_path .= "$each_dir/";

            if (!realpath($top_path) && !file_exists($top_path) && !is_dir($top_path) && pathinfo($top_path, PATHINFO_EXTENSION) === "") {
                mkdir($top_path);
            }
        }
    }

    private function initRootDir(): void
    {
        $this->makeDirIfNotExist("$this->doc_dir/$this->target_src");
        CommandHandler::execMakeDirRstCmd($this->target_src, "$this->doc_dir/$this->target_src");
    }

    /**
     * @param string $dir
     * @param string $root_prefix
     * @throws \InvalidArgumentException
     */
    private function getDirContents(string $dir, string $root_prefix): void
    {
        $file_commands = [];
        $first_dir = true;
        $docs_parent_dir = "$this->doc_dir/$root_prefix";

        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $file_name) {
                $path = realpath("$dir/$file_name");
                $my_name = pathinfo($path, PATHINFO_FILENAME);

                if (is_dir($path) && $file_name !== "." && $file_name !== "..") {
                    $this->makeDirIfNotExist("$docs_parent_dir/$my_name");

                    CommandHandler::execMakeDirRstCmd("$root_prefix/$my_name", "$docs_parent_dir/$my_name");
                    CommandHandler::execAddToParentDirRstCmd($my_name, $first_dir, $docs_parent_dir);

                    $first_dir = false;
                    $this->getDirContents($path, "$root_prefix/$my_name");
                } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                    $file_commands[] = CommandHandler::makePhpRstCmd($my_name, $this->root_dir, $path, $docs_parent_dir);
                    $this->progressBar->addProgress();
                }
            }

            if (!$first_dir) {
                exec(CommandHandler::addNewlineCmd() . " >> " . CommandHandler::makeIdxRstFilePath($docs_parent_dir));
            }

            foreach ($file_commands as $cmd) {
                exec($cmd);
            }
        }
    }
}
