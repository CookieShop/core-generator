<?php
/**
 * @TO-DO quitar esta linea
 */
//error_reporting(0);
// quitar hasta aqui

use Zend\Console\Console;
use ZF\Console\Application;

chdir(realpath(__DIR__.'/../'));

include 'vendor/autoload.php';

$application = new Application(
    "ZF-Console Skeleton",
    "1.0",
    include __DIR__.'/../config/module.config.php',
    Console::getInstance()
);

$exit = $application->run();

exit($exit);

