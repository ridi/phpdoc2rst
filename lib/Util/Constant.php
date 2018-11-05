<?php

require_once 'Argv.php';

define('LIB_DIR', __DIR__.'/..');
define('ROOT_DIR', $argv[1]);

define('DOC_DIR', ROOT_DIR.'/docs/');
define('DOXPHP_BIN', ROOT_DIR.'/vendor/doxphp/doxphp/bin');