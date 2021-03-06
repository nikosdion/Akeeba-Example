<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class AkeebaexampleControllerList extends JController
{
	public function __construct($config = array()) {
		jimport('joomla.environment.uri');
		parent::__construct($config);
		
		$this->registerDefaultTask(_CLIAPP ? 'showList' : 'params');
	}
	
	public function showList($cachable = false, $urlparams = false) {
		JLog::add('In '.__CLASS__.'::'.__METHOD__, JLog::DEBUG);
		// This trick allows us to get a global application instance, depending
		// on whether we are running under the CLI or a web interface
		if(_CLIAPP) {
			$app = JCli::getInstance('Akeebaexample');
		} else {
			$app = JFactory::getApplication();
		}
		
		// Fetch all parameters using the app's JInput instance. Note how we do
		// filtering by passing the appropriate filter in the third parameter.
		// This also demonstrates how NOT to use JRequest, therefore making your
		// code compatible with both CLI and web applications at the same time.
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
			JLog::add('Empty hostname or secret key', JLog::WARNING);
			if(_CLIAPP) {
				JLog::add('Redirecting to showUsage', JLog::DEBUG);
				$this->showUsage();
				return;
			} else {
				// Redirect
				JLog::add('Redirecting to params', JLog::DEBUG);
				$app = JApplication::getInstance(0);
				$msg = JText::_('COM_AKEEBAEXAMPLE_ERR_NOHOST');
				$this->setRedirect('index.php?option=com_akeebaexample&view=list&task=params', $msg);
				$this->redirect();
				return;
			}
		}
		
		JLog::add('Instanciating model', JLog::DEBUG);
		// We need this fake object so that JModel doesn't try to connect to a
		// database (we don't use any in our example app)
		$fooDbo = new JObject();
		// And so, we create the model, populate its state and do nothing more
		$model = $this->createModel('List','AkeebaexampleModel',array('dbo' => $fooDbo));
		$model->setState('host',	$host);
		$model->setState('secret',	$secret);
		$model->setState('from',	$from);
		$model->setState('to',		$to);
		
		JLog::add('Instanciating view', JLog::DEBUG);
		// For the CLI app we use a special hard-coded "format" identifier, txt.
		$format = _CLIAPP ? 'txt' : $app->get('format','html','cmd');
		// Get the view object and attach the model object to it
		$view = $this->getView('List',$format,'AkeebaexampleView');
		JLog::add('Attaching model to view', JLog::DEBUG);
		$view->setModel($model, true);
		// Finally, ask the view object to render itself.
		JLog::add('Rendering view object', JLog::DEBUG);
		$view->display($tpl);
	}
	
	public function showUsage($cachable = false, $urlparams = false)
	{
		JLog::add('In '.__CLASS__.'::'.__METHOD__, JLog::DEBUG);
		// This is a task which only runs in CLI mode, when there is no host
		// or secret key defined.
		if(!_CLIAPP) die(JText::_('COM_AKEEBAEXAMPLE_ERR_CLIONLY'));
		$view = $this->getView('Usage','txt','AkeebaexampleView');
		$view->display();
	}
	
	public function params($cachable = false, $urlparams = false) {
		JLog::add('In '.__CLASS__.'::'.__METHOD__, JLog::DEBUG);
		// Converesely, this task only executes in the web mode, showing an
		// interface for the user to enter site connection information
		if(_CLIAPP) die(JText::_('COM_AKEEBAEXAMPLE_ERR_WEBONLY'));
		
		$view = $this->getView('Params','html','AkeebaexampleView');
		$view->display();
	}
}