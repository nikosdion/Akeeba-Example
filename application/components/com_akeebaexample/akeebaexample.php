<?php
defined('_JEXEC') or die();

// When running in Joomla! context, _CLIAPP is not defined; force it to false
if(!defined('_CLIAPP')) define('_CLIAPP', false);

if(_CLIAPP) {
	$app = JCli::getInstance('Akeebaexample');
	$view = 'list';
} else {
	$app = JFactory::getApplication();
	$view = $app->input->get('view','list','cmd');
}
JLog::add('Setting view to '.$view, JLog::DEBUG);
$task = $app->input->get('task','default');
JLog::add('Task is now '.$task, JLog::DEBUG);

$view = strtolower($view);
$c = 'AkeebaexampleController'.ucfirst($view);

jimport('joomla.filesystem.file');
$filename = JPATH_COMPONENT.'/controllers/'.$view.'.php';
JLog::add('Controller filename is '.$filename, JLog::DEBUG);
if(!JFile::exists($filename)) {
	JLog::add('Controller file not found', JLog::CRITICAL);
	JError::raise(E_ERROR, 404, 'View not found');
} else {
	JLog::add('Importing controller', JLog::DEBUG);
	require_once $filename;
	JLog::add('Instanciating controller', JLog::DEBUG);
	$controller = new $c();
	JLog::add('Executing controller', JLog::DEBUG);
	$controller->execute($task);
}