<?php

namespace Handler;

class StringHandler
{
    private $dir_doc_name = 'idx';

    public function setDirDocName(string $dir_doc_name)
    {
        $this->dir_doc_name = $dir_doc_name;
    }

    public function makeIdxRstFilePath($prefix) {
        return DOC_DIR."$prefix/".$this->dir_doc_name.".rst";
    }

    public function makeRstTitleCmd($name, $underline_type = '=') {
        return "echo \"$name\n".str_repeat($underline_type, strlen($name)) . "\n\"";
    }

    public function addRstListChildCmd($name, &$first_dir) {
        $list_child = "   $name/".$this->dir_doc_name;

        if ($first_dir) {
          $first_dir = false;
          $list_child = ".. toctree::\n   :maxdepth: 2\n\n$list_child";
        }

        return "echo \"$list_child\"";
    }

    public function convertRstCmd($target_php) {
        return DOXPHP_BIN."/doxphp < $target_php | ".DOXPHP_BIN."/doxphp2sphinx";
    }

    public function addNewlineCmd() {
        return "echo \"\n\"";
    }
}
