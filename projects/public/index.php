<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// if (本番サーバーだったら) {
// define('SMARTY_TEMPLATE_DIR', '');
// } else {
define('BASE_URL', '');
define('PROJECT_PATH','');
define('TEMPLATE_DIR', '');
//}

define('APP_CONFIG_DIR_PATH', APPLICATION_PATH . '/configs/');
define('APP_CONFIGS_FILE_NAME', 'application.ini');

// constants read
require_once(APP_CONFIG_DIR_PATH . "constants.php");


/*
define('SMARTY_TEMPLATE_DIR', TEMPLATE_DIR . 'templates');
define('SMARTY_COMPILE_DIR', TEMPLATE_DIR . 'templates_c');
define('SMARTY_CACHE_DIR', TEMPLATE_DIR . 'cache'); 
define('SMARTY_CONFIG_DIR', TEMPLATE_DIR . 'configs/');
 */

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
require_once 'Zend/Application/Module/Autoloader.php';
$moduleLoader = new Zend_Application_Module_Autoloader(array(
	'basePath'  =>  APPLICATION_PATH . '/module',
	'namespace' =>  'Module'
));
Zend_Loader_Autoloader::getInstance();

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APP_CONFIG_DIR_PATH . APP_CONFIGS_FILE_NAME
);
$application->bootstrap()
            ->run();
