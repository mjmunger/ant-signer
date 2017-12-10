#!/usr/bin/env php
<?php

namespace PHPAnt\Core;
use PHPAnt\Core\Publisher;

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

$Publisher->publish($argv[1]);