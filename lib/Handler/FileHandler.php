<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Handler;

use PhpDoc2Rst\Util\ProgressBar;

class FileHandler
{
    /**
     * @var string
     */
    private $source_dir;

    /**
     * @var string
     */
    private $doc_dir;

    /**
     * @var string
     */
    private $bin_dir;

    /**
     * @var string
     */
    private $last_src_name;

    /**
     * @var \ProgressBar\Manager
     */
    private $progressBarManager;

    /**
     * @param array $target_paths
     */
    public function __construct(array $target_paths)
    {
        [$this->bin_dir, $this->source_dir, $this->doc_dir] = $target_paths;
        $this->last_src_name = pathinfo($this->source_dir, PATHINFO_FILENAME);
        $this->initRootDir();

        $this->progressBarManager = ProgressBar::initProgressBar($this->source_dir);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function convertDocsToRsts(): void
    {
        if (is_dir($this->source_dir)) {
            $this->getDirContents($this->source_dir, $this->last_src_name);
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

            if (!is_dir($top_path)) {
                if (file_exists($top_path)) {
                    throw new Exception("Target path directory includes file name.");
                } elseif (!realpath($top_path)) {
                    mkdir($top_path);
                }
            }
        }
    }

    private function initRootDir(): void
    {
        $this->makeDirIfNotExist("$this->doc_dir/$this->last_src_name");
        CommandHandler::execMakeDirRstCmd($this->last_src_name, "$this->doc_dir/$this->last_src_name");
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
                    $file_commands[] = CommandHandler::makePhpRstCmd($my_name, $path, $docs_parent_dir, $this->bin_dir);
                    $this->progressBarManager->advance();
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
