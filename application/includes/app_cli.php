<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.cli');

class Akeebaexample extends JCli {
	public function execute()
	{
		// Get the locale from the environment
		$locale = setlocale(LC_ALL, NULL);
		if(empty($locale) || ($locale == 'C')) $locale = 'en-GB.utf8';
		$localeParts = explode('.', $locale, 2);
		$userLangCode = $localeParts[0];
		
		// Load the language. If the user preferred language does not exist, we
		// load en-GB instead. If the user preferred language exists, we first
		// load en-GB, then the user's language. This way we won't ever have any
		// untranslated strings ;)
		jimport('joomla.filesystem.folder');
		$path = JPATH_BASE.'/language/'.$userLangCode;
		if(JFolder::exists($path)) {
			JFactory::getConfig()->set('language',$userLangCode);
		} else {
			$userLangCode = 'en-GB';
			JFactory::getConfig()->set('language','en-GB');
		}
		$lang = JFactory::getLanguage();
		$lang->load('com_akeebaexample', JPATH_BASE, 'en-GB', true);
		if($userLangCode != 'en-GB') {
			$lang->load('com_akeebaexample', JPATH_BASE, $userLangCode, true);
		}
		
		// Start logging. If we are running off a PHAR archive, the log file
		// will be placed in the directory where the PHAR archive is in.
		// Otherwise we'll just use the logs directory.
		if(class_exists('Phar')) {
			$inPhar = Phar::running(false);
			$pharFilename = substr(Phar::running(true), 7);
		} else {
			$inPhar = false;
		}
		jimport('joomla.log.log');
		$options = array(
			'text_file'			=> 'remote.log',
			'text_file_path'	=> $inPhar ? dirname($pharFilename) : JPATH_BASE.'/logs',
			'text_file_no_php'	=> true,
			'text_entry_format'	=> '{DATETIME}	{PRIORITY}	{MESSAGE}'
		);
		JLog::addLogger($options);
		
		// Launch the component
		JLog::add('Initialising component', JLog::DEBUG);
		require_once JPATH_COMPONENT.'/akeebaexample.php';
	}
}