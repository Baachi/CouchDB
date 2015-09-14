<?php

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo 'You must run "composer.phar install" to run the testsuite!';
    die();
}
$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add('CouchDB\\Tests', __DIR__);
