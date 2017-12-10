<?php
namespace PHPAnt\Core;

/**
 * App Name: FooApp
 * App Description: Allows us to test the app signer.
 * App Version: 1.0
 * App Action: cli-load-grammar -> loadAppManager       @ 90
 * App Action: cli-init         -> declareMySelf        @ 50
 * App Action: cli-command      -> processCommand       @ 50
 * App Action: load_loaders     -> AppManagerAutoLoader @ 50
 * App Action: launch-foo-bar   -> launchFooBar         @ 50
 *
 * App URI: '%^\/foo\/bar\/[0-9]{1,}$%'   -> launch-foo-bar @ 50
 */

/**
 * @package      PHPAnt
 * @subpackage   Apps
 * @author       Michael Munger <michael@highpoweredhelp.com>
 */

class FooApp extends \PHPAnt\Core\AntApp implements \PHPAnt\Core\AppInterface  {

}