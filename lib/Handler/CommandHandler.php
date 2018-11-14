<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Handler;

class CommandHandler
{
    /**
     * @var string
     */
    private const DIR_DOC_NAME = 'idx';

    /**
     * @param string $prefix
     * @return string
     */
    public static function makeIdxRstFilePath(string $prefix): string
    {
        return "$prefix/".self::DIR_DOC_NAME.".rst";
    }

    /**
     * @param string $name
     * @param string $underline_type
     * @return string
     */
    public static function makeRstTitleCmd(string $name, string $underline_type = '='): string
    {
        return "echo \"$name\n" . str_repeat($underline_type, strlen($name)) . "\n\"";
    }

    /**
     * @param string $name
     * @param bool $first_dir
     * @return string
     */
    public static function addRstListChildCmd(string $name, bool $first_dir): string
    {
        $list_child = "   $name/".self::DIR_DOC_NAME;

        if ($first_dir) {
            $list_child = ".. toctree::\n   :maxdepth: 2\n\n$list_child";
        }

        return "echo \"$list_child\"";
    }

    /**
     * @param string $bin_path
     * @param string $target_php_src
     * @return string
     */
    public static function convertRstCmd(string $bin_path, string $target_php_src): string
    {
        return "$bin_path/doxphp < $target_php_src | $bin_path/doxphp2sphinx";
    }

    /**
     * @param string $option_words
     * @return string
     */
    public static function addNewlineCmd(string $option_words = ""): string
    {
        return "echo \"$option_words\"";
    }

    /**
     * @param string $rst_title
     * @param string $target_rst_path
     */
    public static function execMakeDirRstCmd(string $rst_title, string $target_rst_path): void
    {
        exec(self::makeRstTitleCmd($rst_title) . " > " .
            self::makeIdxRstFilePath($target_rst_path));
    }

    /**
     * @param string $name
     * @param bool $first_dir
     * @param string $parent_path
     */
    public static function execAddToParentDirRstCmd(string $name, bool $first_dir, string $parent_path): void
    {
        exec(CommandHandler::addRstListChildCmd($name, $first_dir) . " >> " .
            CommandHandler::makeIdxRstFilePath($parent_path));
    }

    /**
     * @param $rst_title
     * @param $target_php_src
     * @param $parent_path
     * @param $bin_path
     * @return string
     */
    public static function makePhpRstCmd($rst_title, $target_php_src, $parent_path, $bin_path): string
    {
        return "{ " . implode(";", [
                self::makeRstTitleCmd($rst_title, '-'),
                self::convertRstCmd($bin_path, $target_php_src),
                self::addNewlineCmd("\n")
            ]) . ";}>> " . CommandHandler::makeIdxRstFilePath($parent_path);
    }
}
