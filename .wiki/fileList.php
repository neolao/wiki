<?php

if (version_compare(PHP_VERSION, '5') < 0) {
    die('"PHP 5 is required for the search engine"');
}
$baseDirectory = realpath(dirname(__FILE__).'/../');

// Find all wiki files
function rglob($pattern, $path)
{
    $paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path.$pattern);
    foreach ($paths as $path) {
        $files = array_merge($files, rglob($pattern, $path));
    }
    return $files;
}
$files = rglob('*.wiki', $baseDirectory);

// Print the list in JSON
$separator = '';
$cutLength = strlen($baseDirectory);
echo '[';
foreach ($files as $file) {
    echo $separator, '"', substr($file, $cutLength), '"';
    $separator = ',';
}
echo ']';

