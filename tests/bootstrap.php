<?php

$autoload = require_once __DIR__. '/../vendor/autoload.php';
require_once __DIR__. '/../../../../vendor/autoload.php';

$autoload->add('Bernard\\Laravel\\Tests\\', __DIR__. '/lib');
$autoload->register();