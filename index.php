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
		jimport('joomla.log.log');
		$options = array(
			'text_file'			=> 'remote.log',
			'text_file_path'	=> dirname(__FILE__).'/logs',
			'text_file_no_php'	=> true,
			'text_entry_format'	=> '{DATETIME}	{PRIORITY}	{MESSAGE}'
		);
		JLog::addLogger($options);
		
		JLog::add('Initialising component', JLog::DEBUG);
		require_once JPATH_COMPONENT.'/akeeba.php';
	}
}

JCli::getInstance('Akeeba')->execute();