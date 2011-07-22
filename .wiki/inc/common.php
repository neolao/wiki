<?php

define('BASE_PATH',     realpath(dirname(__FILE__).'/../../'));
define('PROJECT_PATH',  BASE_PATH.'/.wiki');
define('INC_PATH',      PROJECT_PATH.'/inc');
define('JS_PATH',       PROJECT_PATH.'/js');
define('THEMES_PATH',   PROJECT_PATH.'/themes');
define('SCRIPTS_PATH',  PROJECT_PATH.'/scripts');
define('DATA_PATH',     PROJECT_PATH.'/data');

$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '/'));
$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
$hostUrl = $_SERVER['HTTP_HOST'];
if ($_SERVER['SERVER_PORT'] == 443) {
    $hostUrl = 'https://'.$hostUrl;
} else {
    $hostUrl = 'http://'.$hostUrl;
}
define('HOST_URL',      $hostUrl);
define('BASE_URL',      $baseUrl);
define('PROJECT_URL',   BASE_URL.'/.wiki');
define('JS_URL',        PROJECT_URL.'/js');
define('THEMES_URL',    PROJECT_URL.'/themes');
define('SCRIPTS_URL',   PROJECT_URL.'/scripts');




// Autoload classes
function __autoload($className)
{
    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    $relativeFilePath = str_replace('_', '/', $className).'.php';
    foreach ($includePaths as $includePath) {
        $filePath = $includePath.'/'.$relativeFilePath;
        if (file_exists($filePath)) {
            include_once $filePath;
        }
    }
}
set_include_path(get_include_path().PATH_SEPARATOR.INC_PATH);



/**
 * Get the configuration
 *
 * @return  array   The configuration
 */
function getConfig()
{
    $default = array(
        'theme'     => 'default',
        'syntax'    => 'textile'
    );

    $configFile = PROJECT_PATH.'/config.ini';
    if (is_readable($configFile)) {
        $config = parse_ini_file($configFile);
        return array_merge($default, $config);
    }

    return $default;
}

/**
 * Get the theme url
 *
 * @return  string      The theme url
 */
function getThemeUrl()
{
    $config = getConfig();
    $themeName = $config['theme'];
    return THEMES_URL.'/'.$themeName;
}

/**
 * Get the common HTML header
 *
 * @return  string      The common HTML header
 */
function getCommonHtmlHeader()
{
    $html = '';

    // Theme
    $themeUrl = getThemeUrl();
    $html .= '<link rel="stylesheet" type="text/css" href="'.$themeUrl.'/screen.css" media="screen"/>';
    $html .= '<link rel="stylesheet" type="text/css" href="'.$themeUrl.'/print.css" media="print"/>';
    $html .= '<!--[if lt IE 9]>';
    $html .= '<link rel="stylesheet" type="text/css" href="'.$themeUrl.'/ie.css"/>';
    $html .= '<![endif]-->';

    // Head JS
    $html .= '<script type="text/javascript" src="'.JS_URL.'/head.min.js"></script>';

    return $html;
}

/**
 * Get the toolbar HTML header
 *
 * @return  string      The HTML header
 */
function getToolbarHeader()
{
    $html = '<script type="text/javascript">';
    $html .= 'head.js(';
    $html .= '"'.JS_URL.'/toolbar/jquery-1.4.4.min.js",';
    $html .= '"'.JS_URL.'/toolbar/toolbar.js"';
    $html .= ');';
    $html .= 'var BASE_URL      = "'.BASE_URL.'";';
    $html .= 'var SCRIPTS_URL   = "'.SCRIPTS_URL.'";';
    
    // Config
    $html .= 'config                = {};';
    $html .= 'config.isFile         = true;';
    $html .= 'config.isDirectory    = false;';
    $html .= 'config.isHome         = false;';
    $html .= '</script>';
    
    return $html;
}

/**
 * Check if the path is outside wiki
 *
 * @param   string  $path       The path (file or directory)
 * @return  boolean             true if the path is outside wiki, false otherwise
 */
function isOutsideWiki($path)
{
    if (!is_readable($path)) {
        return true;
    }
    $path = realpath($path);
    if (strpos($path, BASE_PATH) === false) {
        return true;
    }
    if (strpos($path, PROJECT_PATH) !== false) {
        return true;
    }

    return false;
}

/**
 * Get the file url from the file path
 *
 * @param   string  $filePath   The file path
 * @return  string              The file url
 */
function getFileUrl($filePath)
{
    $relativePath = substr($filePath, strlen(BASE_PATH));
    return BASE_URL.$relativePath;
}

/**
 * Recurvise glob (see glob function)
 *
 * @param   string  $pattern    The file pattern
 * @param   string  $path       The base path
 * @return  array               File list matching the pattern
 */
function rglob($pattern, $path)
{
    $paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path.$pattern);
    foreach ($paths as $path) {
        $files = array_merge($files, rglob($pattern, $path));
    }
    return $files;
}


