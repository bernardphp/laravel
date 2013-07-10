<?php

$autoload = null;
foreach (array('..', '../..', '../../..', '../../../..') as $rel) {
    $file = __DIR__. '/'. $rel. '/vendor/autoload.php';
    if (is_file($file)) {
        $autoload = include_once $file;
    }
}


$autoload->add('Bernard\\Laravel\\Tests\\', __DIR__. '/lib');
$autoload->register();