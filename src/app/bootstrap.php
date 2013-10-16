<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jcarmony
 * Date: 10/15/13
 * Time: 11:14 AM
 * To change this template use File | Settings | File Templates.
 */

require_once 'config/config.php';

// Load Composer

require_once __DIR__.'/../vendor/autoload.php';

//Register an autoloader
$loader = new Phalcon\Loader();
$loader->registerDirs(array(
    __DIR__.'/controllers/',
    __DIR__.'/models/',
    __DIR__.'/library/'
))->register();

