<?php

define('BASE_PATH',     realpath(dirname(__FILE__).'/../../'));
define('PROJECT_PATH',  BASE_PATH.'/.wiki');
define('INC_PATH',      PROJECT_PATH.'/inc');
define('JS_PATH',       PROJECT_PATH.'/js');
define('THEMES_PATH',   PROJECT_PATH.'/themes');
define('SCRIPTS_PATH',  PROJECT_PATH.'/scripts');

$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '/'));
$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
define('BASE_URL',      $baseUrl);
define('PROJECT_URL',   BASE_URL.'/.wiki');
define('JS_URL',        PROJECT_URL.'/js');
define('THEMES_URL',    PROJECT_URL.'/themes');
define('SCRIPTS_URL',   PROJECT_URL.'/scripts');


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
    $html .= 'var BASE_URL = "'.BASE_URL.'";';
    $html .= 'var SCRIPTS_URL = "'.SCRIPTS_URL.'";';
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
