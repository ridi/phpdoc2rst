<?php

if (count($argv) <= 1) {
    echo "Target path is necessary for docs.\n";
    exit(1);
} elseif (!is_dir($argv[1])) {
    echo "Target path does not exist.\n";
    exit(1);
}