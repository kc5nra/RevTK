<?php
# Don't change this file!
# Applications are configured in apps/<appname>/config/settings.php 
define('CORE_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('CORE_APP',         'core');
define('CORE_ENVIRONMENT', 'dev');
define('CORE_DEBUG',       true);

require_once(CORE_ROOT_DIR.'/apps/'.CORE_APP.'/config/config.php');
$configuration = new coreConfiguration(CORE_ENVIRONMENT, CORE_DEBUG, CORE_ROOT_DIR);
coreContext::createInstance($configuration);
coreContext::getInstance()->getController()->dispatch();
