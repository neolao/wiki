<?php

include(dirname(__FILE__).'/../inc/common.php');

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
$fileUrl = BASE_URL.$fileRelativePath;
$filePath = BASE_PATH.$fileRelativePath;

// Check invalid file
if (isOutsideWiki($filePath)) {
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
