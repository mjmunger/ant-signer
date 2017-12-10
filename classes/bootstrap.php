<?php

//define an autoloader so we can deal with classes we need.

function signer_autoloader($class) {
    $buffer = explode("\\", $class);
    $class = end($buffer);

    $file = sprintf("classes/%s.class.php",$class);
    if(file_exists($file)) require_once($file);
}

spl_autoload_register('signer_autoloader');