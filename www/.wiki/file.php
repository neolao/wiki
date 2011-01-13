<?php

// Global variables
$scriptPath = dirname(__FILE__);

// Get stylesheet
$style = file_get_contents($scriptPath.'/style.css');

// Get the raw content
$path = $_GET['path'];
$documentRoot = $scriptPath.'/../';
$filePath = $documentRoot.$path;

if (!is_file($filePath)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$filePath = realpath($filePath);
$content = file_get_contents($filePath);

// Convert the raw content to wiki content
include_once($scriptPath.'/Textile.php');
$textile = new Textile();
$html = $textile->TextileThis($content);


// Find the main title
$title = 'Undefined';
$count = preg_match('|<h1>([^<]+)</h1>|', $html, $matches);
if ($count > 0) {
    $title = $matches[1];
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title; ?></title>
        <style type="text/css">
            <?php echo $style; ?>
        </style>
    </head>
    <body>
        <?php echo $html; ?>
    </body>
</html>
