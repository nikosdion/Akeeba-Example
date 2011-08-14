<?php
defined('_JEXEC') or die();

$app = JCli::getInstance('Akeeba');

foreach($this->items as $record)
{
	$id = sprintf('%6u',$record->id);
	$status = str_pad($record->status, 8);
	$app->out($id."|{$record->backupstart}|$status|{$record->description}");
}