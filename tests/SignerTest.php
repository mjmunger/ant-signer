<?php

use PHPUnit\Framework\TestCase;
use PHPAnt\Core\PHPAntSigner;

class SignerTest extends TestCase
{
    public function testSignerSetup() {
        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';

        $options = [];
        $options['antConfigPath'] = $configPath;

        $Signer = new PHPAntSigner($options);
        $this->assertInstanceOf('PHPAnt\Core\PHPAntSigner',$Signer);
        $this->assertSame($configPath     , $Signer->antConfigPath  );
        $this->assertSame($privateKeyPath , $Signer->privateKeyPath );
        $this->assertSame($publicKeyPath  , $Signer->publicKeyPath  );
    }

    public function testGenKeys() {

        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';


        //Clean up!
        if(file_exists($privateKeyPath)) unlink($privateKeyPath);
        if(file_exists($publicKeyPath))  unlink($publicKeyPath);

        $options = [];
        $options['antConfigPath'] = $configPath;

        $Signer = new PHPAntSigner($options);
        $Signer->genKeys(false);

        $this->assertFileExists($privateKeyPath);
        $this->assertFileExists($publicKeyPath);

    }

    /**
     * @covers PHPAntSigner::getAppMeta()
     */

    public function testGetAppMeta() {
        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';

        $options = [];
        $options['antConfigPath'] = $configPath;

        $Signer = new PHPAntSigner($options);
        $name        = $Signer->getAppMeta(__DIR__ . '/testapp/app.php','name');
        $description = $Signer->getAppMeta(__DIR__ . '/testapp/app.php','description');
        $version     = $Signer->getAppMeta(__DIR__ . '/testapp/app.php','version');

        $this->assertSame('FooApp',$name);
        $this->assertSame($description, 'Allows us to test the app signer.');
        $this->assertSame($version,'1.0');
    }

    /**
     * @covers PHPAntSigner::cleanAppCredentials()
     * @throws Exception
     */

    public function testCleanCredentials() {
        $removeTheseFiles = [ __DIR__ . '/testapp/manifest.xml'
                            , __DIR__ . '/testapp/manifest.sig'
                            , __DIR__ . '/testapp/public.key'
                            ];

        foreach($removeTheseFiles as $targetFile) {
            $fh = fopen($targetFile,'w');
            fwrite($fh,"Delete me!");
            fclose($fh);
        }

        foreach($removeTheseFiles as $targetFile) {
            $this->assertFileExists($targetFile);
        }

        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';

        $options = [];
        $options['antConfigPath'] = $configPath;

        $Signer = new PHPAntSigner($options);
        $Signer->setApp(__DIR__ . '/testapp/');
        $return = $Signer->cleanAppCredentials();
        $this->assertTrue($return);

        foreach($removeTheseFiles as $targetFile) {
            $this->assertFileNotExists($targetFile);
        }

    }

    /**
     * @covers PHPAntSigner::setApp()
     */

    public function testSetapp() {
        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';

        $options = [];
        $options['antConfigPath'] = $configPath;

        $Signer = new PHPAntSigner($options);
        $Signer->setApp(__DIR__ . "/testapp/");
        $this->assertSame(__DIR__ . '/testapp/', $Signer->appPath);
        $this->assertSame(__DIR__ . '/testapp/manifest.xml', $Signer->manifestPath);

    }

    /**
     * @covers PHPAntSigner::saveDerivedPublicKey()
     * @covers PHPAntSigner::derivePublicKey()
     */

    public function testSaveDerivedKey() {
        $configPath = '/tmp/.phpant';
        $privateKeyPath = $configPath . '/ant_id';
        $publicKeyPath  = $configPath . '/ant_id.pub';

        $options = [];
        $options['antConfigPath'] = $configPath;

        unlink($publicKeyPath);
        $this->assertFileNotExists($publicKeyPath);
        $Signer = new PHPAntSigner($options);
        $Signer->genKeys(false);
        $this->assertFileExists($publicKeyPath);

        //Hash the original.
        $hash1 = md5_file($publicKeyPath);

        //Remove the original.
        unlink($publicKeyPath);
        $this->assertFileNotExists($publicKeyPath);

        //Recreate it.
        $Signer->saveDerivedPublicKey();
        $hash2 = md5_file($publicKeyPath);

        $this->assertSame($hash1,$hash2);
    }

    /**
     * @covers PHPAntSigner::generateManifestFile()
     */
    public function testGenerateManifestFile() {
        $configPath = '/tmp/.phpant';
        $privateKeyPath   = $configPath . '/ant_id';
        $publicKeyPath    = $configPath . '/ant_id.pub';
        $manifestTarget   = __DIR__ . '/testapp/manifest.xml';
        $manifestExpected = __DIR__ .'/expected/expected-generateManifestFile.xml';
        $options = [];
        $options['antConfigPath'] = $configPath;
        $Signer = new PHPAntSigner($options);
        $Signer->setApp(__DIR__ . '/testapp/');
        $Signer->generateManifestFile();

        $hash1 = md5_file($manifestExpected);
        $hash2 = md5_file($manifestTarget);

        $this->assertSame($hash1,$hash2,"The generated manifest.xml file does not match the expected one!" . __FILE__ . ":" . __LINE__);

        //cleanup
        unlink($manifestTarget);
    }

    /**
     * @covers PHPAntSigner::getAppActions()
     */

    public function testGetAppActions() {
        $configPath = '/tmp/.phpant';
        $appPath    = __DIR__ . '/testapp/app.php';

        $options = [];
        $options['antConfigPath'] = $configPath;
        $Signer = new PHPAntSigner($options);
        $actions = $Signer->getAppActions($appPath);

        $expected = [];
        $expected['cli-load-grammar'] = ['loadAppManager'       => '90'];
        $expected['cli-init']         = ['declareMySelf'        => '50'];
        $expected['cli-command']      = ['processCommand'       => '50'];
        $expected['load_loaders']     = ['AppManagerAutoLoader' => '50'];
        $expected['launch-foo-bar']   = ['launchFooBar'         => '50'];

        $this->assertSame($expected,$actions);
    }

    /**
     * @covers PHPAntSigner::registerHook()
     */

    public function testRegisterHook() {
        $configPath = '/tmp/.phpant';
        $appPath    = __DIR__ . '/testapp/app.php';
        $manifestTarget   = __DIR__ . '/testapp/manifest.xml';
        $manifestExpected = __DIR__ . '/expected/expected-registerhook.xml';

        $options = [];
        $options['antConfigPath'] = $configPath;
        $Signer = new PHPAntSigner($options);
        $Signer->setApp(__DIR__ . '/testapp/');
        $Signer->generateManifestFile();
        $Signer->registerHook('hook','function',50);

        $hash1 = md5_file($manifestExpected);
        $hash2 = md5_file($manifestTarget);

        $this->assertsame($hash1,$hash2);

    }

    /**
     * @covers PHPAntSigner::registerAllActions()
     */

    public function testRegisterAllActions() {
        $configPath = '/tmp/.phpant';
        $appPath    = __DIR__ . '/testapp/app.php';
        $manifestTarget   = __DIR__ . '/testapp/manifest.xml';
        $manifestExpected = __DIR__ . '/expected/expected-registerAllActions.xml';

        $options = [];
        $options['antConfigPath'] = $configPath;
        $Signer = new PHPAntSigner($options);
        $Signer->setApp(__DIR__ . '/testapp/');
        $Signer->generateManifestFile();
        $actions = $Signer->getAppActions($appPath);
        $Signer->registerAllActions($actions);

        $hash1 = md5_file($manifestExpected);
        $hash2 = md5_file($manifestTarget);

        $this->assertsame($hash1,$hash2);

        unlink($manifestTarget);

    }
}