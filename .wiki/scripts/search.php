<?php

include(dirname(__FILE__).'/../inc/common.php');

// Get parameters
if (empty($_GET['term'])) {
    die('false');
}
$term = $_GET['term'];
if (get_magic_quotes_gpc()) {
    $term = stripslashes($term);
}

$search = new Wiki_Search();
$filePaths = $search->find($term);

$urls = array();
foreach ($filePaths as $filePath) {
    if (!isOutsideWiki($filePath)) {
        $urls[] = '"'.getFileUrl($filePath).'"';
    }
}

// Print the file urls
echo '[';
echo implode(', ', $urls);
echo ']';
