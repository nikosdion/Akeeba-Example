<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.cli');

class Akeeba extends JCli {
	public function execute()
	{
		jimport('joomla.log.log');
		$options = array(
			'text_file'			=> 'remote.log',
			'text_file_path'	=> JPATH_BASE.'/logs',
			'text_file_no_php'	=> true,
			'text_entry_format'	=> '{DATETIME}	{PRIORITY}	{MESSAGE}'
		);
		JLog::addLogger($options);
		
		JLog::add('Initialising component', JLog::DEBUG);
		require_once JPATH_COMPONENT.'/akeeba.php';
	}
}