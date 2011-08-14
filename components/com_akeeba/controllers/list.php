<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class AkeebaControllerList extends JController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerDefaultTask(_CLIAPP ? 'showList' : 'params');
	}
	
	public function showList($cachable = false, $urlparams = false) {
		// This trick allows us to get a global application instance, depending
		// on whether we are running under the CLI or a web interface
		if(_CLIAPP) {
			$app = JCli::getInstance('Akeeba');
		} else {
			$app = JFactory::getApplication();
		}
		
		// Fetch all parameters using the app's JInput instance. Note how we do
		// filtering by passing the appropriate filter in the third parameter.
		$host = $app->input->get('host', '', 'string');
		$secret = $app->input->get('secret', '', 'string');
		$tpl = $app->input->get('tpl', null);

		$from = $app->input->get('from',0, 'int');
		$to = $app->input->get('to', 50, 'int');

		// Handle an invalid input case by delegating execution to another task
		// in the CLI app (a CLI app has to implement a continuous MVC pattern),
		// or redirecting to another URL in the web app (the web app provides a
		// "Model 2" kind of MVC implementation)
		if(empty($host) || empty($secret)) {
			if(_CLIAPP) {
				$this->showUsage();
				return;
			} else {
				// Redirect
				$app = JApplication::getInstance(0);
				$msg = 'You need to tell me which site to connect to';
				$this->setRedirect('index.php?option=com_akeeba&view=list&task=params', $msg);
				$this->redirect();
				return;
			}
		}
		
		// We need this fake object so that JModel doesn't try to connect to a
		// database (we don't use any in our example)
		$fooDbo = new JObject();
		// And so, we create the model, populate its state and do nothing more
		$model = $this->createModel('List','AkeebaModel',array('dbo' => $fooDbo));
		$model->setState('host',	$host);
		$model->setState('secret',	$secret);
		$model->setState('from',	$from);
		$model->setState('to',		$to);
		
		// For the CLI app we use a special "format" identified, txt.
		$format = _CLIAPP ? 'txt' : $app->get('format','html','cmd');
		// Get the view object and "tack" the model object to it
		$view = $this->getView('List','txt','AkeebaView');
		$view->setModel($model, true);
		// Finally, as the view object to render itself.
		$view->display($tpl);
	}
	
	public function showUsage($cachable = false, $urlparams = false)
	{
		if(!_CLIAPP) die("This view can not run in Web mode\n");
		$view = $this->getView('Usage','txt','AkeebaView');
		$view->display();
	}
	
	public function params($cachable = false, $urlparams = false) {
		if(_CLIAPP) die("This view can not run in CLI mode\n");
		
		jimport('joomla.environment.uri');
		$view = $this->getView('Params','html','AkeebaView');
		$view->display();
	}
}