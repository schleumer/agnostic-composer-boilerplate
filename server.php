<?php

$dir = __DIR__;

$file = "index.php";

$matches = [];
preg_match("~/(?<file>.*)~", $_SERVER['REQUEST_URI'], $matches);

if (!empty($matches['file'])) {
    $file = $matches['file'];
}

$phpFile = $dir . "/www/" . $file;

if (!file_exists($phpFile)) {
    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
    exit;
}

$phpDir = dirname($phpFile);

set_include_path(get_include_path() . PATH_SEPARATOR . $phpDir);

chdir($phpDir);

require($phpFile);