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
     * @var ProgressBar
     */
    private $progressBar;

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
    }

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function execute(): void
    {
        $this->initRootDir();
        $this->progressBar = new ProgressBar($this->countPhpFiles());

        if (is_dir($this->source_dir_path)) {
            $this->makeRst($this->source_dir_path, $this->last_src_name);
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
     * @param string $source_dir
     * @param string $root_prefix
     * @throws \InvalidArgumentException
     */
    private function makeRst(string $source_dir, string $root_prefix): void
    {
        $file_commands = [];
        $first_dir = true;
        $docs_parent_dir = "$this->doc_dir_path/$root_prefix";

        $files = scandir($source_dir);

        if ($files !== false) {
            foreach ($files as $file_name) {
                $target_file_path = realpath("$source_dir/$file_name");
                $file_name = pathinfo($target_file_path, PATHINFO_FILENAME);

                if (is_dir($target_file_path) && $file_name !== "." && $file_name !== "..") {
                    $this->makeDirIfNotExist("$docs_parent_dir/$file_name");

                    Command::execMakeDirRstCmd("$root_prefix/$file_name", "$docs_parent_dir/$file_name");
                    Command::execAddToParentDirRstCmd($file_name, $first_dir, $docs_parent_dir);

                    $first_dir = false;
                    $this->makeRst($target_file_path, "$root_prefix/$file_name");
                } elseif (pathinfo($target_file_path, PATHINFO_EXTENSION) === 'php') {
                    $file_commands[] = Command::makePhpRstCmd($file_name, $target_file_path, $docs_parent_dir, $this->bin_dir_path);
                    $this->progressBar->advance();
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
     * @return int
     */
    private function countPhpFiles(): int
    {
        return count(
            array_filter(
                iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->source_dir_path))),
                function (\SplFileInfo $file): bool {
                    return $file->getExtension() === 'php';
                }
            )
        );
    }
}
