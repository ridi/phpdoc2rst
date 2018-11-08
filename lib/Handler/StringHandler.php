<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Handler;

class StringHandler
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
     * @param string $doxphp_bin
     * @param string $target_php
     * @return string
     */
    public static function convertRstCmd(string $doxphp_bin, string $target_php): string
    {
        return "$doxphp_bin/doxphp < $target_php | $doxphp_bin/doxphp2sphinx";
    }

    /**
     * @return string
     */
    public static function addNewlineCmd(): string
    {
        return "echo \"\n\"";
    }
}
