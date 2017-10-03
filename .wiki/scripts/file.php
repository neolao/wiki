<?php

include(dirname(__FILE__).'/../inc/common.php');


// Get the raw content
$path = $_GET['path'];
$filePath = BASE_PATH.'/'.$path;


// Check invalid file
if (isOutsideWiki($filePath)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}



$config         = getConfig();
$filePath       = realpath($filePath);
$directoryPath  = dirname($filePath);
$content        = file_get_contents($filePath);
$search         = new Wiki_Search();


// Auto index the page
$search->index($filePath);


// Convert the raw content to wiki content
switch ($config['syntax']) {
    case 'markdown':
        include_once(INC_PATH.'/markdown/markdown.php');
        $html = Markdown($content);
        break;
    default:
        include_once(INC_PATH.'/textile/Textile.php');
        $textile = new Textile();
        $html = $textile->TextileThis($content);
}


// Find the main title
$title = getTitleSuffix().'Undefined';
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

        <?php
        echo getCommonHtmlHeader();


        // Code highlighter
        echo '<script type="text/javascript">';
        if ($hasCode) {
            $highlighter = array(JS_URL.'/syntaxHighlighter/sh_main.js');
            foreach ($languages as $language) {
                array_push($highlighter, JS_URL.'/syntaxHighlighter/sh_'.$language.'.js');
            }

            echo 'head.js(';
            foreach ($highlighter as $js) {
                echo '"', $js, '",';
            }
            echo 'function(){sh_highlightDocumentCustom()});';
        }
        echo '</script>';


        echo getToolbarHeader();


        echo '<script type="text/javascript">';
        echo 'config.isFile = true;';
        if ($path === 'index.wiki') {
            echo 'config.isHome = true;';
        }

        // Breadcrumb
        $separator = '';
        $breadcrumb = explode('/', $path);
        array_pop($breadcrumb);
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

        <?php echo $html; ?>

    </body>
</html>
