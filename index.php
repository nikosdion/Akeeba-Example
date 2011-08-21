<?php
// Required in order for Joomla!-compatible code to run
define('_JEXEC', 1);

// Enable for debugging purposes only
/**
error_reporting(E_ALL | E_NOTICE);
/**/

// We start by defining all the paths required by the Joomla! platform in order
// to spawn an application. If you only need a CLI app, only JPATCH_LIBRARIES
// is required
define('JPATH_BASE',			dirname(__FILE__));
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

// Our code relies on a constant named _CLIAPP to determine if it's running in
// CLI or web mode. In this section we automatically detect if we are a CLI or a
// web app and set this constant accordingly. We do that with a bit of deep
// magic: the REQUEST_METHOD is only available when we are running under web
// mode.
if( array_key_exists('REQUEST_METHOD', $_SERVER) )
{
	// This is a web application
	define('_CLIAPP', 0);
	
	// Workarounds required for web applications
	@ini_set('magic_quotes_runtime', 0);
	// I am not sure this is required with PHP 5.2 or later...
	@ini_set('zend.ze1_compatibility_mode', '0');
	
	// These are required by JApplication, even though the directories do not
	// exist.
	define('JPATH_ADMINISTRATOR', JPATH_BASE.'/administrator');
	define('JPATH_INSTALLATION', JPATH_BASE.'/installation');
	define('JPATH_CACHE', JPATH_BASE.'/cache');
	
	// This is where you load the language files. Note that we have to load
	// the lib_joomla translation file manually so that any library errors are
	// proper English strings instead of language keys ;)
	JFactory::getConfig()->set('language', 'en-GB');
	JFactory::getLanguage()->load('lib_joomla');

	// OK, another case of deep magic here. JApplication supports three client
	// names. Using the "site" client name forces it to look in
	// JPATH_BASE.'/includes/application.php' for a class named JSite.
	// Essentially the platform is tied to the CMS, as it doesn't allow us to
	// specify custom applications.
	jimport('joomla.application.application');
	$app = JApplication::getInstance('site');
	// After instantiating the application object, we have to pass it to
	// JFactory so that we can retrieve it easily. The reason we don't just use
	// JFactory::getApplication('site') is that this generates the error message
	// "Not connected to server.". Huh!
	JFactory::$application = $app;
	// Next up, we dispatch the application. Note how we force the component
	// name.
	$app->dispatch('com_akeeba');
	// Let the application render itself (basically, mix the component output
	// with the template-generated HTML code)
	$app->render();
	// This final step is mandatory to deliver the output to the browser :)
	echo $app;
} else {
	// CLI application
	define('_CLIAPP', 1);
	
	// Things are much easier. Just include the CLI app class and execute it
	JLoader::import('includes.app_cli', JPATH_BASE);
	define('JPATH_COMPONENT', dirname(__FILE__).'/components/com_akeeba');
	JCli::getInstance('Akeeba')->execute();
}