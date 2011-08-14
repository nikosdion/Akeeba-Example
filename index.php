<?php
// Required in order for Joomla!-compatible code to run
define('_JEXEC', 1);

// For debugging purposes only
error_reporting(E_ALL | E_NOTICE);

define('JPATH_BASE', dirname(__FILE__));
define('JPATH_ROOT',			JPATH_BASE);
define('JPATH_SITE',			JPATH_ROOT);
define('JPATH_CONFIGURATION',	JPATH_ROOT);
define('JPATH_LIBRARIES',		JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS',			JPATH_ROOT . '/plugins'  );
define('JPATH_THEMES',			JPATH_BASE . '/templates');

// This is required to work around the lack of JLog during application
// initialization which throws a fatal error
define('_JREQUEST_NO_CLEAN',1);
include_once 'libraries/import.php';

// Automatically detect if we are a CLI or a web app. The REQUEST_METHOD is only
// available when we are running under web mode.
if( array_key_exists('REQUEST_METHOD', $_SERVER) )
{
	// Web application
	define('_CLIAPP', 0);
	
	// Workarounds required for web applications
	@ini_set('magic_quotes_runtime', 0);
	@ini_set('zend.ze1_compatibility_mode', '0');
	
	// These are required by JApplication, even though the directories do not
	// exist.
	define('JPATH_ADMINISTRATOR', JPATH_BASE.'/administrator');
	define('JPATH_INSTALLATION', JPATH_BASE.'/installation');
	define('JPATH_CACHE', JPATH_BASE.'/cache');
	
	jimport('joomla.application.application');
	$app = JApplication::getInstance('site');
	JFactory::$application = $app;
	$app->dispatch('com_akeeba'); // Note how we force the component name
	$app->render();
	echo $app;
} else {
	// CLI application
	define('_CLIAPP', 1);
	
	JLoader::import('includes.app_cli', JPATH_BASE);
	define('JPATH_COMPONENT', dirname(__FILE__).'/components/com_akeeba');
	JCli::getInstance('Akeeba')->execute();
}