<?php

defined('_JEXEC') or die();

$app = JCli::getInstance('Akeeba');
$task = $app->get('task','default');

require_once JPATH_COMPONENT.'/controllers/list.php';
$c = 'AkeebaControllerList';
$controller = new $c();
$controller->execute($task);