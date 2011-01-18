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
$directoryPath = dirname($filePath);
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


// Find code
$availableLanguages = array('bash', 'bison', 'c', 'changelog', 'code', 'cpp', 'css', 'diff', 'haxe', 'html', 'java',
    'javascript', 'latex', 'log', 'makefile', 'pascal', 'perl', 'php', 'prolog', 'properties', 'python', 
    'ruby', 'scala', 'sh', 'sql', 'xml', 'xorg');
$languages = array();
foreach ($availableLanguages as $language) {
    if (preg_match('|class="'.$language.'"|', $html)) {
        array_push($languages, $language);
    }
}
$hasCode = (count($languages) > 0);


// Browse feature
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
$browse = '<ul class="browse" style="-moz-column-count:$1;-webkit-column-count:$1;column-count:$1;">';
foreach ($folders as $folder) {
    $browse .= $folder;
}
foreach ($files as $file) {
    $browse .= $file;
}
$browse .= '</ul>';
$html = preg_replace('|<p>\[browse ?(\d*)?\]</p>|', $browse, $html);


?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl.'/style.css'; ?>"/>
        <script type="text/javascript" src="<?php echo $baseUrl.'/head.min.js'; ?>"></script>

        <?php
        echo '<script type="text/javascript">';

        // Code highlighter
        if ($hasCode) {
            $highlighter = array($baseUrl.'/syntaxHighlighter/sh_main.js');
            foreach ($languages as $language) {
                array_push($highlighter, $baseUrl.'/syntaxHighlighter/sh_'.$language.'.js');
            }

            echo 'head.js(';
            foreach ($highlighter as $js) {
                echo '"', $js, '",';
            }
            echo 'function(){sh_highlightDocumentCustom()});';
        }

        // Toolbar
        echo 'head.js(';
        echo '"', $baseUrl, '/toolbar/jquery-1.4.4.min.js",';
        echo '"', $baseUrl, '/toolbar/toolbar.js"';
        echo ');';

        echo '</script>';
        ?>
    </head>
    <body>

        <?php echo $html; ?>

    </body>
</html>
