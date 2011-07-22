<?php
include(dirname(__FILE__).'/inc/common.php');

$phpSelf = $_SERVER['PHP_SELF'];
$baseUrl = substr($phpSelf, 0, strrpos($phpSelf, '/'));
$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
$host = HOST_URL.$baseUrl;


$filePaths = rglob('*.wiki', BASE_PATH);

echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.sitemaps.org/schemas/sitemap-image/1.1" xmlns:video="http://www.sitemaps.org/schemas/sitemap-video/1.1">';

foreach ($filePaths as $filePath) {
    echo '<url>';
    echo    '<loc>', $host, getFileUrl($filePath), '</loc>';
    echo '</url>';
}
echo '</urlset>';