<?php

namespace PHPAnt\Core;

/**
 * {CLASS SUMMARY}
 *
 * Date: 12/9/17
 * Time: 5:49 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

class Publisher
{
    public $phpAntConfigDir     = null;
    public $commandLineOptions = false;

    public function __construct($options, $phpAntConfigDir)
    {
        $this->commandLineOptions = $options;
        $this->phpAntConfigDir    = $phpAntConfigDir;
    }


    function printTitle() {

        echo "";
        echo "======================================================================" . PHP_EOL;
        echo "PHP-Ant application publisher and code signer." . PHP_EOL;
        echo "Version: 1.0" . PHP_EOL;
        echo "Report issues to: https://github.com/mjmunger/ant-signer" . PHP_EOL;
        echo "======================================================================" . PHP_EOL;
        echo "";
    }

    function showHelp() {
        $this->printTitle();
        ?>

SUMMARY:
    This application will sign and publish PHP-Ant apps.

SYNTAX:
    publish.php [options] /path/to/app/

OPTIONS:
    -k     Generate new private key. Stored in ~/.phpant/ant_id

<?php
exit(0);
    }

    function showKeyWarning($privateKeyPath) {
        $this->printTitle();
        ?>

=PRIVATE KEY MISSING=

I was not able to find your private signing key, which should be located here: <?= $privateKeyPath ?>.

You cannot sign / publish apps without a private signing key. You can generate one with the command:

        publish.php -k.

<?php

    }

    public function checkConfigDir() {
        if(file_exists($this->phpAntConfigDir) == false) mkdir($this->phpAntConfigDir,0700,true);

    }

    public function checkKeys() {
        //Check existence of a private key
        $privateKeyPath = $this->phpAntConfigDir . '/ant_id';
        if(file_exists($privateKeyPath) == false) $this->showKeyWarning($privateKeyPath);

        //Make sure we have a corresponding public key. Regenerate if necessary.
        if(file_exists($this->phpAntConfigDir . '/ant_id.pub') == false) $this->regeneratePublicKey();
    }

    public function regeneratePublicKey() {
        $options = [];
        $options['antConfigPath'] = $this->phpAntConfigDir;
        $Signer = new PHPAntSigner($options);
        $Signer->saveDerivedPublicKey();

    }

    public function genKeys($force = false) {
        $this->checkConfigDir();
        $this->printTitle();
        $privateKeyPath = $this->phpAntConfigDir . '/ant_id';
        if(file_exists($privateKeyPath) == true && $force == false) {
            echo PHP_EOL;
            echo "A private key already exists in " . $privateKeyPath . PHP_EOL;
            echo "Use the -f switch to override and create a new key, or " . PHP_EOL;
            echo "backup this key to a different location." . PHP_EOL;
            echo PHP_EOL;
            exit(0);
        }

        $options['antConfigPath'] = $this->phpAntConfigDir;
        $Signer = new \PHPAnt\Core\PHPAntSigner($options);
        $Signer->genKeys(true);
    }

    public function publish($options, $appPath) {
        $this->checkConfigDir();

        $options['antConfigPath'] = $this->phpAntConfigDir;
        $Signer = new \PHPAnt\Core\PHPAntSigner($options);


        //Generate new keys and exit.
        if(in_array('k',array_keys($options)) == true) {
            $Signer->genKeys(true);
            exit(0);
        }

        $Signer->publish($appPath);
    }
}