<?php

// Global variables
$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '.wiki')-1);


if (empty($_GET['term'])) {
    die('<p>Please type something</p>');
}
$term = $_GET['term'];
if (get_magic_quotes_gpc()) {
    $term = stripslashes($term);
}
$termHtml = htmlentities($term, ENT_QUOTES, 'UTF-8');
echo '<h1>Search for "', $termHtml, '"</h1>';


$baseDirectory = dirname(__FILE__).'/../';
$files = array();
function search($directoryPath)
{
    global $baseUrl, $baseDirectory, $term, $files;

    if ($handle = opendir($directoryPath)) {
        while (false !== ($name = readdir($handle))) {
            if (!in_array($name, array('.', '..', '.htaccess', '.wiki', '.git'))) {
                $filePath = $directoryPath.'/'.$name;

                if (is_dir($filePath)) {
                    search($filePath);
                    continue;
                }
                if (!is_readable($filePath)) {
                    continue;
                }
                $content = file_get_contents($filePath);
                $result = strpos($content, $term);
                if ($result !== false) {
                    $link = $baseUrl.substr($filePath, strlen($baseDirectory));
                    $files[] = '<li><a class="file" href="'.$link.'">'.$name.'</a></li>';
                }
            }
        }
        closedir($handle);
    }
}
search($baseDirectory);

if (count($files) > 0) {
    ksort($files);

    $browse = '<ul>';
    foreach ($files as $file) {
        $browse .= $file;
    }
    $browse .= '</ul>';

    echo $browse;
    exit;
}
echo '<p>Not found</p>';

