<?php

include(dirname(__FILE__).'/../inc/common.php');

if (version_compare(PHP_VERSION, '5') < 0) {
    die('"PHP 5 is required for the search engine"');
}

// Find all wiki files
$files = rglob('*.wiki', BASE_PATH);

// Print the list in JSON
$separator = '';
$cutLength = strlen(BASE_PATH);
echo '[';
foreach ($files as $file) {
    echo $separator, '"', substr($file, $cutLength), '"';
    $separator = ',';
}
echo ']';

