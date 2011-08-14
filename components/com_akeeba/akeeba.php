<?php

defined('_JEXEC') or die();

if(_CLIAPP) {
	$app = JCli::getInstance('Akeeba');
} else {
	$app = JFactory::getApplication();
}
$task = $app->input->get('task','default');

require_once JPATH_COMPONENT.'/controllers/list.php';
$c = 'AkeebaControllerList';
$controller = new $c();
$controller->execute($task);