<?php

// Global variables
$scriptPath = dirname(__FILE__);
$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '/'));

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
        <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl.'/style.css'; ?>"/>
    </head>
    <body>
        <?php echo $html; ?>
    </body>
</html>
