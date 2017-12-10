<?php

use PHPUnit\Framework\TestCase;
use PHPAnt\Core\Publisher;

class PublisherTest extends TestCase
{
    /**
     * Recursively remove directories and their contents.
     * @param $src
     * http://php.net/manual/en/function.rmdir.php
     */

    function rrmdir($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    rrmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    public function testPublisherSetup() {
        $options = [];
        $phpAntConfigDir = '/tmp/.phpant/';
        $Publisher = new Publisher($options, $phpAntConfigDir);

        $this->assertSame($options, $Publisher->commandLineOptions);
        $this->assertSame($phpAntConfigDir,$Publisher->phpAntConfigDir);
    }

    public function testCreateConfigDir() {
        $options = [];
        $phpAntConfigDir = '/tmp/.phpant';

        if(file_exists($phpAntConfigDir) == true) $this->rrmdir($phpAntConfigDir);

        $this->assertFileNotExists($phpAntConfigDir);

        $Publisher = new Publisher($options, $phpAntConfigDir);
        $Publisher->checkConfigDir();

        $this->assertFileExists($phpAntConfigDir);
    }
}