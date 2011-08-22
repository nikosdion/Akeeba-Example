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
$task = $app->input->get('task','default');

$view = strtolower($view);
$c = 'AkeebaexampleController'.ucfirst($view);

jimport('joomla.filesystem.file');
$filename = JPATH_COMPONENT.'/controllers/'.$view.'.php';
if(!JFile::exists($filename)) {
	JError::raise(E_ERROR, 404, 'View not found');
} else {
	require_once $filename;
	$controller = new $c();
	$controller->execute($task);
}