<?php
declare(strict_types=1);

namespace PhpDoc2Rst;

use Exception;
use PhpDoc2Rst\Handler\Command;
use PhpDoc2Rst\Util\ProgressBar;

class Converter
{
    /**
     * @var string
     */
    private $source_dir_path;

    /**
     * @var string
     */
    private $doc_dir_path;

    /**
     * @var string
     */
    private $bin_dir_path;

    /**
     * @var string
     */
    private $last_src_name;

    /**
     * @var int
     */
    private $total_php_file_cnt;

    /**
     * @var int
     */
    private $converted_php_file_cnt;

    /**
     * @var \ProgressBar\Manager
     */
    private $progressBarManager;

    /**
     * @var int
     */
    private $progressPercent = 0;

    /**
     * @param string $bin_dir_path
     * @param string $source_dir_path
     * @param string $doc_dir_path
     */
    public function __construct(string $bin_dir_path, string $source_dir_path, string $doc_dir_path)
    {
        $this->bin_dir_path = $bin_dir_path;
        $this->source_dir_path = $source_dir_path;
        $this->doc_dir_path = $doc_dir_path;
        $this->last_src_name = pathinfo($source_dir_path, PATHINFO_FILENAME);
        $this->total_php_file_cnt = $this->countPhpFiles($source_dir_path);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function execute(): void
    {
        $this->initRootDir();
        $this->progressBarManager = ProgressBar::create($this->total_php_file_cnt);

        if (is_dir($this->source_dir_path)) {
            $this->getDirContents($this->source_dir_path, $this->last_src_name);
        }
    }

    /**
     * @param string $path
     * @throws Exception
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

    /**
     * @throws Exception
     */
    private function initRootDir(): void
    {
        $this->makeDirIfNotExist("$this->doc_dir_path/$this->last_src_name");
        Command::execMakeDirRstCmd($this->last_src_name, "$this->doc_dir_path/$this->last_src_name");
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
        $docs_parent_dir = "$this->doc_dir_path/$root_prefix";

        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $file_name) {
                $path = realpath("$dir/$file_name");
                $my_name = pathinfo($path, PATHINFO_FILENAME);

                if (is_dir($path) && $file_name !== "." && $file_name !== "..") {
                    $this->makeDirIfNotExist("$docs_parent_dir/$my_name");

                    Command::execMakeDirRstCmd("$root_prefix/$my_name", "$docs_parent_dir/$my_name");
                    Command::execAddToParentDirRstCmd($my_name, $first_dir, $docs_parent_dir);

                    $first_dir = false;
                    $this->getDirContents($path, "$root_prefix/$my_name");
                } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                    $file_commands[] = Command::makePhpRstCmd($my_name, $path, $docs_parent_dir, $this->bin_dir_path);
                    $this->converted_php_file_cnt += 1;
                    $this->progressPercent = ProgressBar::updateProgressBar(
                        $this->total_php_file_cnt,
                        $this->converted_php_file_cnt,
                        $this->progressBarManager,
                        $this->progressPercent);
                }
            }

            if (!$first_dir) {
                exec(Command::addNewlineCmd() . " >> " . Command::makeIdxRstFilePath($docs_parent_dir));
            }

            foreach ($file_commands as $cmd) {
                exec($cmd);
            }
        }
    }

    /**
     * @param string $src_path
     * @return int
     */
    private function countPhpFiles(string $src_path) {
        return count(
            array_filter(
                iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src_path))),
                function (\SplFileInfo $file): bool {
                    return $file->getExtension() === 'php';
                }
            )
        );
    }
}
