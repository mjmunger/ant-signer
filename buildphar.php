<?php

$phar = new Phar("ant-publish.phar", 0,'ant-publish.phar');
$phar->startBuffering();
$phar->buildFromDirectory('.');
$defaultStub = $phar->createDefaultStub('publish.php');
$stub = "#!/usr/bin/env php \n". $defaultStub;
$phar->setStub($stub);
$phar->stopBuffering();