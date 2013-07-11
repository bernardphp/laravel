<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Bernard\\Tests\\Laravel\\', __DIR__);
$loader->register();

require __DIR__ . '/Bernard/Tests/Laravel/Fixtures/app/bootstrap/autoload.php';

