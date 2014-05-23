#! /usr/bin/env php
<?php

$loader = require_once __DIR__.'/vendor/autoload.php';
$loader->set('Profile', __DIR__.'/library');


use Emerald\CLI;

defined('LIB') || define('LIB', __DIR__.'/library');
defined('APP') || define('APP', 'Profile');
defined('ROOT') || define('ROOT', __DIR__);

CLI::getInstance()->execute($argv);

