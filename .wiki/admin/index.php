<?php
include(dirname(__FILE__).'/../inc/common.php');

$search = new Wiki_Search();

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Textilewiki administration</title>
    </head>
    <body>
        <h1>Textilewiki administration</h1>
        
        <h2>Search engine</h2>
        <p>Document count : <?php echo $search->getDocumentCount(); ?></p>
    </body>
</html>
