<?php

namespace PHPAnt\Core;
use PHPAnt\Core\Publisher;
use PHPUnit\Runner\Exception;
use \SplFileInfo;

/**
 * Publishes a PHP-Ant app from teh command line independent of the CLI environment.
 */

include('classes/bootstrap.php');

$options         = getopt("k::f::");
$home            = getenv("HOME");
$phpAntConfigDir = $home . "/.phpant";

$Publisher = new Publisher($options, $phpAntConfigDir);

if(in_array('k',array_keys($options))) {
    $Publisher->genKeys(in_array('f',array_keys($options)));
    exit(0);
}

if($argc !== 2) $Publisher->showHelp();

$path = $argv[1];
//if(file_exists($path) == false) die("Cannot find specified path ($path). Try again.");
"Trying to publish path: $path";

try {
    $appPath = new SplFileInfo($path);
} catch (Exception $e) {
    echo "Caught Exception: ", $e->getMessage(), PHP_EOL;
    die('Quitting.');
}

$Publisher->publish($options, $appPath->getRealPath());