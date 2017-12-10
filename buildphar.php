<?php

$phar = new Phar("antpub.phar", 0,'antpub.phar');
$phar->startBuffering();
$phar->buildFromDirectory('.');
$defaultStub = $phar->createDefaultStub('publish.php');
$stub = "#!/usr/bin/env php \n". $defaultStub;
$phar->setStub($stub);
$phar->stopBuffering();