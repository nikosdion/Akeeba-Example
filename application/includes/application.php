<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

/**
 * Joomla! Application class
 *
 * Provide many supporting API functions
 * 
 * Nicholas says: I have basically copied the application.php from Joomla! 1.7
 * and modified it to use a hard-coded template name (see the getTemplate
 * method for my dirty workarounds).
 *
 * @package		Joomla.Site
 * @subpackage	Application
 */
final class JSite extends JApplication
{
	/**
	 * Currently active template
	 * @var object
	 */
	private $template = null;

	/**
	 * Option to filter by language
	 */
	private $_language_filter = false;

	/**
	 * Option to detect language by the browser
	 */
	private $_detect_browser = false;

	/**
	 * Class constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 * Recognized key values include 'clientId' (this list is not meant to be comprehensive).
	 */
	public function __construct($config = array())
	{
		$config['clientId'] = 0;
		parent::__construct($config);
	}

	/**
	 * Initialise the application.
	 *
	 * @param	array
	 */
	public function initialise($options = array())
	{
		$config = JFactory::getConfig();

		jimport('joomla.language.helper');

		if (empty($options['language'])) {
			$lang = JRequest::getString('language', null);
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if ($this->_language_filter && empty($options['language'])) {
			// Detect cookie language
			jimport('joomla.utilities.utility');
			$lang = JRequest::getString(JUtility::getHash('language'), null ,'cookie');
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if (empty($options['language'])) {
			// Detect user language
			$lang = JFactory::getUser()->getParam('language');
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if ($this->_detect_browser && empty($options['language'])) {
			// Detect browser language
			$lang = JLanguageHelper::detectLanguage();
			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
		}

		if (empty($options['language'])) {
			// Detect default language
			$params =  JComponentHelper::getParams('com_languages');
			$client	= JApplicationHelper::getClientInfo($this->getClientId());
			$options['language'] = $params->get($client->name, $config->get('language', 'en-GB'));
		}

		// One last check to make sure we have something
		if (!JLanguage::exists($options['language'])) {
			$lang = $config->get('language','en-GB');
			if (JLanguage::exists($lang)) {
				$options['language'] = $lang;
			}
			else {
				$options['language'] = 'en-GB'; // as a last ditch fail to english
			}
		}

		// Execute the parent initialise method.
		parent::initialise($options);

		// Load Library language
		$lang = JFactory::getLanguage();
		$lang->load('lib_joomla', JPATH_SITE)
		|| $lang->load('lib_joomla', JPATH_ADMINISTRATOR);

	}

	/**
	 * Route the application.
	 *
	 */
	public function route()
	{
		parent::route();

		$Itemid = JRequest::getInt('Itemid');
		$this->authorise($Itemid);
	}

	/**
	 * Dispatch the application
	 *
	 * @param	string
	 */
	public function dispatch($component = null)
	{
		try
		{
			// Get the component if not set.
			if (!$component) {
				$component = JRequest::getCmd('option');
			}

			$document	= JFactory::getDocument();
			$user		= JFactory::getUser();
			$router		= $this->getRouter();
			$params		= $this->getParams();

			switch($document->getType())
			{
				case 'html':
					// Get language
					$lang_code = JFactory::getLanguage()->getTag();

					$document->setMetaData('keywords', $this->getCfg('MetaKeys'));
					$document->setMetaData('rights', $this->getCfg('MetaRights'));
					if ($router->getMode() == JROUTER_MODE_SEF) {
						$document->setBase(htmlspecialchars(JURI::current()));
					}
					break;

				case 'feed':
					$document->setBase(htmlspecialchars(JURI::current()));
					break;
			}

			$document->setTitle($params->get('page_title'));
			$document->setDescription($params->get('page_description'));
			$contents = self::renderComponent($component);
			$document->setBuffer($contents, 'component');

			// Trigger the onAfterDispatch event.
			$this->triggerEvent('onAfterDispatch');
		}
		// Mop up any uncaught exceptions.
		catch (Exception $e)
		{
			$code = $e->getCode();
			JError::raiseError($code ? $code : 500, $e->getMessage());
		}
	}

	/**
	 * Display the application.
	 */
	public function render()
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		// get the format to render
		$format = $document->getType();

		switch ($format)
		{
			case 'feed':
				$params = array();
				break;

			case 'html':
			default:
				$template	= $this->getTemplate(true);
				$file		= JRequest::getCmd('tmpl', 'index');

				if (!$this->getCfg('offline') && ($file == 'offline')) {
					$file = 'index';
				}

				if ($this->getCfg('offline') && !$user->authorise('core.login.offline')) {
					$uri		= JFactory::getURI();
					$return		= (string)$uri;
					$this->setUserState('users.login.form.data',array( 'return' => $return ) );
					$file = 'offline';
					JResponse::setHeader('Status', '503 Service Temporarily Unavailable', 'true');
				}
				if (!is_dir(JPATH_THEMES . '/' . $template->template) && !$this->getCfg('offline')) {
					$file = 'component';
				}
				$params = array(
					'template'	=> $template->template,
					'file'		=> $file.'.php',
					'directory'	=> JPATH_THEMES,
					'params'	=> $template->params
				);
				break;
		}

		// Parse the document.
		$document = JFactory::getDocument();
		$document->parse($params);

		$caching = false;
		if ($this->getCfg('caching') && $this->getCfg('caching',2) == 2 && !$user->get('id')) {
			$caching = true;
		}

		// Render the document.
		JResponse::setBody($document->render($caching, $params));

		// Trigger the onAfterRender event.
		$this->triggerEvent('onAfterRender');
	}

	/**
	 * Login authentication function
	 *
	 * @param	array	Array('username' => string, 'password' => string)
	 * @param	array	Array('remember' => boolean)
	 *
	 * @see JApplication::login
	 */
	public function login($credentials, $options = array())
	{
		 // Set the application login entry point
		if (!array_key_exists('entry_url', $options)) {
			$options['entry_url'] = JURI::base().'index.php?option=com_users&task=user.login';
		}

		// Set the access control action to check.
		$options['action'] = 'core.login.site';

		return parent::login($credentials, $options);
	}

	/**
	 * @deprecated 1.6	Use the authorise method instead.
	 */
	public function authorize($itemid)
	{
		return $this->authorise($itemid);
	}

	/**
	 * Check if the user can access the application
	 */
	public function authorise($itemid)
	{
		$menus	= $this->getMenu();
		$user	= JFactory::getUser();

		if (!$menus->authorise($itemid))
		{
			if ($user->get('id') == 0)
			{
				// Redirect to login
				$uri		= JFactory::getURI();
				$return		= (string)$uri;

				$this->setUserState('users.login.form.data',array( 'return' => $return ) );

				$url	= 'index.php?option=com_users&view=login';
				$url	= JRoute::_($url, false);

				$this->redirect($url, JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
			}
			else {
				JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
	}

	/**
	 * Get the appliaction parameters
	 *
	 * @param	string	The component option
	 * @return	object	The parameters object
	 * @since	1.5
	 */
	public function getParams($option = null)
	{
		static $params = array();

		$hash = '__default';
		if (!empty($option)) {
			$hash = $option;
		}
		if (!isset($params[$hash]))
		{
			// Get component parameters
			if (!$option) {
				$option = JRequest::getCmd('option');
			}
			// Get new instance of component global parameters
			//$params[$hash] = clone JComponentHelper::getParams($option);
			$params[$hash] = JRegistry::getInstance('foo');

			// Get language
			$lang_code = JFactory::getLanguage()->getTag();

			$title = $this->getCfg('sitename');
			$description = $this->getCfg('MetaDesc');
			$rights = $this->getCfg('MetaRights');

			$params[$hash]->def('page_title', $title);
			$params[$hash]->def('page_description', $description);
			$params[$hash]->def('page_rights', $rights);
		}

		return $params[$hash];
	}

	/**
	 * Get the application parameters
	 *
	 * @param	string	The component option
	 *
	 * @return	object	The parameters object
	 * @since	1.5
	 */
	public function getPageParameters($option = null)
	{
		return $this->getParams($option);
	}

	/**
	 * Get the template
	 *
	 * @return string The template name
	 * @since 1.0
	 */
	public function getTemplate($params = false)
	{
		if(is_object($this->template))
		{
			if ($params) {
				return $this->template;
			}
			return $this->template->template;
		}

		$id = 0;
		$condition = '';

		$tid = JRequest::getVar('templateStyle', 0);
		if (is_numeric($tid) && (int) $tid > 0) {
			$id = (int) $tid;
		}


		$cache = JFactory::getCache('com_templates', '');
		if ($this->_language_filter) {
			$tag = JFactory::getLanguage()->getTag();
		}
		else {
			$tag ='';
		}
		$templates = array((object)array(
			'template'	=> 'default',
			'params'	=> JRegistry::getInstance('foobarTemplateParams')
		));

		if (isset($templates[$id])) {
			$template = $templates[$id];
		}
		else {
			$template = $templates[0];
		}

		// Allows for overriding the active template from the request
		$template->template = JRequest::getCmd('template', $template->template);
		$template->template = JFilterInput::getInstance()->clean($template->template, 'cmd'); // need to filter the default value as well

		// Fallback template
		if (!file_exists(JPATH_THEMES . '/' . $template->template . '/index.php')) {
			JError::raiseWarning(0, JText::_('JERROR_ALERTNOTEMPLATE'));
		    $template->template = 'beez_20';
		    if (!file_exists(JPATH_THEMES . '/beez_20/index.php')) {
		    	$template->template = '';
		    }
		}

		// Cache the result
		$this->template = $template;
		if ($params) {
			return $template;
		}
		return $template->template;
	}

	/**
	 * Overrides the default template that would be used
	 *
	 * @param string	The template name
	 * @param mixed		The template style parameters
	 */
	public function setTemplate($template, $styleParams=null)
 	{
 		if (is_dir(JPATH_THEMES.DS.$template)) {
 			$this->template = new stdClass();
 			$this->template->template = $template;
			if ($styleParams instanceof JRegistry) {
				$this->template->params = $styleParams;
			}
			else {
				$this->template->params = new JRegistry($styleParams);
			}
 		}
 	}

	/**
	 * Return a reference to the JPathway object.
	 *
	 * @param	string	$name		The name of the application/client.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	object	JMenu.
	 * @since	1.5
	 */
	public function getMenu($name = null, $options = array())
	{
		$options	= array();
		$menu		= parent::getMenu('site', $options);
		return $menu;
	}

	/**
	 * Return a reference to the JPathway object.
	 *
	 * @param	string	$name		The name of the application.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	object JPathway.
	 * @since	1.5
	 */
	public function getPathway($name = null, $options = array())
	{
		$options = array();
		$pathway = parent::getPathway('site', $options);
		return $pathway;
	}

	/**
	 * Return a reference to the JRouter object.
	 *
	 * @param	string	$name		The name of the application.
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	JRouter
	 * @since	1.5
	 */
	static public function getRouter($name = null, array $options = array())
	{
		$config = JFactory::getConfig();
		$options['mode'] = $config->get('sef');
		$router = parent::getRouter('site', $options);
		return $router;
	}

	/**
	 * Return the current state of the language filter.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getLanguageFilter()
	{
		return $this->_language_filter;
	}

	/**
	 * Set the current state of the language filter.
	 *
	 * @return	boolean	The old state
	 * @since	1.6
	 */
	public function setLanguageFilter($state=false)
	{
		$old = $this->_language_filter;
		$this->_language_filter=$state;
		return $old;
	}
	/**
	 * Return the current state of the detect browser option.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getDetectBrowser()
	{
		return $this->_detect_browser;
	}

	/**
	 * Set the current state of the detect browser option.
	 *
	 * @return	boolean	The old state
	 * @since	1.6
	 */
	public function setDetectBrowser($state=false)
	{
		$old = $this->_detect_browser;
		$this->_detect_browser=$state;
		return $old;
	}

	/**
	 * Redirect to another URL.
	 *
	 * Optionally enqueues a message in the system message queue (which will be displayed
	 * the next time a page is loaded) using the enqueueMessage method. If the headers have
	 * not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param	string	The URL to redirect to. Can only be http/https URL
	 * @param	string	An optional message to display on redirect.
	 * @param	string  An optional message type.
	 * @param	boolean	True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 * @param	boolean	True if the enqueued messages are passed to the redirection, false else.
	 * @return	none; calls exit().
	 * @since	1.5
	 * @see		JApplication::enqueueMessage()
	 */
	public function redirect($url, $msg='', $msgType='message', $moved = false, $persistMsg = true)
	{
		if (!$persistMsg) {
			$this->_messageQueue = array();
		}
		parent::redirect($url, $msg, $msgType, $moved);
	}
	
	/**
	 * UGLY HACK! We try to get away without storing the session data in the
	 * database, as we don't want to use a database.
	 */
	protected function _createSession($name)
	{
		$options = array();
		$options['name'] = $name;

		$session = JFactory::getSession($options);

		$time = time();

		return $session;
	}

	// Ugly hack, once again...
	public static function renderComponent($option, $params = array())
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load template language files.
		$template = $app->getTemplate(true)->template;
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_BASE, $lang->getDefault(), false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", $lang->getDefault(), false, false);

		if (empty($option))
		{
			// Throw 404 if no component
			JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
			return;
		}

		// Record the scope
		$scope = $app->scope;
		// Set scope to component name
		$app->scope = $option;

		// Build the component path.
		$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		$file = substr($option, 4);

		// Define component path.
		define('JPATH_COMPONENT', JPATH_BASE . '/components/' . $option);
		define('JPATH_COMPONENT_SITE', JPATH_SITE . '/components/' . $option);
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . $option);

		// Get component path
		if ($app->isAdmin() && file_exists(JPATH_COMPONENT . '/admin.' . $file . '.php'))
		{
			$path = JPATH_COMPONENT . '/admin.' . $file . '.php';
		}
		else
		{
			$path = JPATH_COMPONENT . '/' . $file . '.php';
		}

		// If component is disabled throw error
		if (!file_exists($path))
		{
			JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}
		$task = JRequest::getString('task');

		// Load common and local language files.
		$lang->load($option, JPATH_BASE, null, false, false) || $lang->load($option, JPATH_COMPONENT, null, false, false)
			|| $lang->load($option, JPATH_BASE, $lang->getDefault(), false, false)
			|| $lang->load($option, JPATH_COMPONENT, $lang->getDefault(), false, false);

		// Handle template preview outlining.
		$contents = null;

		// Execute the component.
		ob_start();
		require_once $path;
		$contents = ob_get_contents();
		ob_end_clean();

		// Build the component toolbar
		jimport('joomla.application.helper');

		if (($path = JApplicationHelper::getPath('toolbar')) && $app->isAdmin())
		{
			// Get the task again, in case it has changed
			$task = JRequest::getString('task');

			// Make the toolbar
			include_once $path;
		}

		// Revert the scope
		$app->scope = $scope;

		return $contents;
	}
}