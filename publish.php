<?php

namespace PHPAnt\Core;
use PHPAnt\Core\Publisher;
use Symfony\Component\Finder\SplFileInfo;

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
if(file_exists($path) == false) die("Cannot find specified path. Try again.");

$appPath = new SplFileInfo($argv[1]);

$Publisher->publish($options, $appPath->getRealPath());