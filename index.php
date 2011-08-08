<?php
define('_JEXEC', 1);

error_reporting(E_ALL | E_NOTICE);

define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', JPATH_BASE);
define('JPATH_COMPONENT', dirname(__FILE__).'/com_akeeba');

include_once 'libraries/import.php';

jimport('joomla.application.cli');

class Akeeba extends JCli {
	public function execute()
	{
		require_once JPATH_COMPONENT.'/example.php';
	}
}

JCli::getInstance('Akeeba')->execute();