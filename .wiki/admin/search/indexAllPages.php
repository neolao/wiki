<?php
include(dirname(__FILE__).'/../../inc/common.php');

$filePaths = rglob('*.wiki', BASE_PATH);

$search = new Wiki_Search();
foreach ($filePaths as $filePath) {
    $search->index($filePath);
}

header('Location: ..');