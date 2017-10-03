<?php
include(dirname(__FILE__).'/../inc/common.php');

// Get the raw content
$path = $_GET['path'];
$directoryPath = BASE_PATH.'/'.$path;

// Check invalid directory
if (isOutsideWiki($directoryPath)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}


// Page title
$title = getTitleSuffix();
$directoryPath = realpath($directoryPath);
$pathInfo = explode('/', $directoryPath);
$title .= $pathInfo[count($pathInfo) - 1];

// Browse
$folders = array();
$files = array();
if ($handle = opendir($directoryPath)) {
    while (false !== ($name = readdir($handle))) {
        if (!in_array($name, array('.', '..', '.htaccess', '.wiki'))) {
            if (is_dir($directoryPath.'/'.$name)) {
                $folders[$name] = '<li><a class="folder" href="'.$name.'/">'.$name.'</a></li>';
            } else {
                $files[$name] = '<li><a class="file" href="'.$name.'">'.$name.'</a></li>';
            }
        }
    }
    closedir($handle);
}
ksort($folders);
ksort($files);
$browse = '<ul class="browse" style="-moz-column-count:2;-webkit-column-count:2;column-count:2;">';
foreach ($folders as $folder) {
    $browse .= $folder;
}
foreach ($files as $file) {
    $browse .= $file;
}
$browse .= '</ul>';


?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title; ?></title>
        <?php
        echo getCommonHtmlHeader();
        echo getToolbarHeader();

        echo '<script type="text/javascript">';
        echo 'config.isDirectory = true;';

        // Breadcrumb
        $separator = '';
        $breadcrumb = explode('/', $path);
        $currentFolder = array_pop($breadcrumb);
        if (empty($currentFolder)) {
            array_pop($breadcrumb);
        }
        echo 'config.breadcrumb = [';
        foreach ($breadcrumb as $folderName) {
            echo $separator, '"', $folderName, '"';
            $separator = ', ';
        }
        echo '];';

        echo '</script>';
        ?>
    </head>
    <body>
        <h1><?php echo $title; ?></h1>
        <?php echo $browse; ?>
    </body>
</html>
