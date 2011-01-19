<?php

// Global variables
$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '.wiki')-1);
$basePath = realpath(dirname(__FILE__).'/../');

// Get parameters
if (empty($_GET['file'])) {
    die('false');
}
if (empty($_GET['term'])) {
    die('false');
}
$fileRelativePath = $_GET['file'];
$term = $_GET['term'];
if (get_magic_quotes_gpc()) {
    $fileRelativePath = stripslashes($fileRelativePath);
    $term = stripslashes($term);
}
$fileUrl = $baseUrl.$fileRelativePath;
$filePath = $basePath.$fileRelativePath;

// Check if the file is readable
if (!is_readable($filePath)) {
    die('false');
}

// Cannot search outside wiki
$filePath = realpath($filePath);
if (strpos($filePath, $basePath) === false) {
    die('false');
}

// Search the term in the file
$content = file_get_contents($filePath);
$result = strpos($content, $term);
if ($result === false) {
    die('false');
}

// Print the file url
die('"'.$fileUrl.'"');
