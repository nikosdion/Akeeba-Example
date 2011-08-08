<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class AkeebaControllerList extends JController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerDefaultTask('showList');
	}
	
	public function showList($cachable = false, $urlparams = false) {
		$app = JCli::getInstance('Akeeba');
		$host = $app->input->get('host');
		$secret = $app->input->get('secret');
		$tpl = $app->input->get('tpl', null);
		
		$from = $app->input->get('from',0);
		$to = $app->input->get('to', 50);
		
		if(empty($host) || empty($secret)) {
			die('TODO : Host and secret not defined :(');
		}
		
		$fooDbo = new JObject();
		$model = $this->createModel('List','AkeebaModel',array('dbo' => $fooDbo));
		$model->setState('host',	$host);
		$model->setState('secret',	$secret);
		$model->setState('from',	$from);
		$model->setState('to',		$to);
		
		$view = $this->getView('List','txt','AkeebaView');
		$view->setModel($model, true);
		
		$view->display($tpl);
	}
}