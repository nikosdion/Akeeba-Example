<?php
defined('_JEXEC') or die();

/**
 * This is the heart of the example application. This is a small helper class
 * which communicates with a remote site's Akeeba Backup installation and
 * performes remote API calls. You don't have to know how it works, just trust
 * me when I say that it does work :p
 */
class AkeebaexampleHelperApi
{
	/** @var string The hostname to use */
	private $_host = '';
	
	/** @var The secret key to use */
	private $_secret = '';
	
	/** @var string The HTTP verb to use for communications */
	private $_verb = '';
	
	/** @var The format to use for communications */
	private $_format = '';

	/**
	 *
	 * @staticvar AkeebaexampleHelperApi $instance
	 * @return AkeebaexampleHelperApi
	 */
	public static function getInstance()
	{
		static $instance = null;
		
		if(!is_object($instance)) {
			$instance = new self();
		}
		
		return $instance;
	}
	
	/**
	 * Returns true if all necessary configuration values (hostname, secret key,
	 * verb and format) have been specified.
	 * 
	 * @return bool
	 */
	public function isConfigured()
	{
		return (!empty($this->_host) && !empty($this->_secret) && !empty($this->_verb) && !empty($this->_format));
	}
	
	/**
	 * Performs an API query and returns its (decoded) result
	 * 
	 * @param string $method Remote API method name
	 * @param array $params Any parameters you want to pass to the API call
	 * @param string $component [optional] Receiving component on the remote site, omit to use com_akeeba
	 * @return object
	 */
	public function doQuery($method, $params = array(), $component = 'com_akeeba')
	{
		JLog::add('Preparing to perform remote API call to '.$method, JLog::DEBUG);
		
		$url = $this->getURL();
		
		$query = $this->prepareQuery($method, $params, $component);

		$result = $this->executeJSONQuery($url, $query);
		
		$result->body->data = json_decode($result->body->data);

		if(is_null($result->body->data)) {
			JLog::add('Invalid (null) body data', JLog::ERROR);
			throw new Exception(JText::_('COM_AKEEBAEXAMPLE_APIERR_INVALIDBODYDATA'));
		}

		return $result;
	}
	
	/**
	 * Internal function to perform the actual remote API call
	 * 
	 * @param string $url Remote to the remote site
	 * @param array $query Query parameters in key=>value array format
	 * @return object
	 */
	private function executeJSONQuery($url, $query)
	{
		jimport('joomla.client.http');
		
		$http = new JHttp(array(
			'timeout'		=> 10
		));
		
		if($this->_verb == 'GET') {
			jimport('joomla.environment.uri');
			$uri = new JURI($url);
			if(!empty($query)) {
				foreach($query as $k => $v) {
					$uri->setVar($k, $v);
				}
			}
			$response = $http->get($uri->toString());
		} else {
			$response = $http->post($url, $query);
		}
		
		if($response->code != 200) {
			JLog::add('HTTP error '.$response->code, JLog::ERROR);
			throw new Exception(JText::sprintf('COM_AKEEBAEXAMPLE_APIERR_HTTPERROR',$response->code));
		}
		
		$raw = $response->body;

		$startPos = strpos($raw,'###') + 3;
		$endPos = strrpos($raw,'###');
		if( ($startPos !== false) && ($endPos !== false) ) {
			$json = substr($raw, $startPos, $endPos - $startPos);
		} else {
			$json = $raw;
		}
		$result = json_decode($json, false);
		
		if(is_null($result)) {
			JLog::add('JSON decoding error', JLog::ERROR);
			JLog::add($json, JLog::DEBUG);
			throw new Exception(JText::_('COM_AKEEBAEXAMPLE_APIERR_JSONDECODING'));
		}
		return $result;
	}
	
	/**
	 * Lame function to get the absolute URL to the site's index.php file...
	 * @return type 
	 */
	private function getURL()
	{
		return $this->_host.'/index.php';
	}
	
	/**
	 * Encodes the method parameters in a way that our remote API understands
	 * 
	 * @param string $method Which method of the remote API to use
	 * @param array $params A key=>value array with the method's parameters
	 * @param string $component [optional] Receiving component. Skip to use com_akeeba.
	 * @return array 
	 */
	public function prepareQuery($method, $params, $component = 'com_akeeba')
	{
		$body = array(
			'method'		=> $method,
			'data'			=> (object)$params
		);
		
		$salt = md5(microtime(true));
		$challenge = $salt.':'.md5($salt.$this->_secret);
		$body['challenge'] = $challenge;
		$bodyData = json_encode($body);

		$query = array(
			'option'	=>	$component,
			'view'		=>	'json',
			'json'		=> json_encode(array(
				'encapsulation'		=> 1,
				'body'				=> $bodyData
			))
		);
		
		if(empty($this->_format)) $this->_format = 'html';
		$query['format'] = $this->_format;
		if($this->_format == 'html') $query['tmpl'] = 'component';
		
		return $query;
	}
	
	/**
	 * Setter for the $host private property
	 * @param string $host 
	 */
	private function _setHost($host)
	{
		if( (strpos($host, 'http://') !== 0) && (strpos($host, 'https://') !== 0) ) {
			$host = 'http://'.$host;
		}
		$host = rtrim($host,'/');
		
		$this->_host = $host;
	}
	
	/**
	 * Getter for the $host private property
	 * @return string
	 */
	private function _getHost()
	{
		return $host;
	}
	
	/**
	 * Setter for the $secret private property
	 * @param string $secret
	 */
	private function _setSecret($secret)
	{
		$this->_secret = $secret;
	}
	
	/**
	 * Getter for the $secret private property
	 * @return string
	 */
	private function _getSecret()
	{
		return $this->_secret;
	}
	
	/**
	 * Setter for the $verb private property
	 * @param string $verb
	 */
	private function _setVerb($verb)
	{
		$verb = strtoupper($verb);
		
		if(!in_array($verb, array('GET','POST'))) {
			$verb = 'GET';
		}
		
		$this->_verb = $verb;
	}
	
	/**
	 * Getter for the $verb private property
	 * @return string
	 */
	private function _getVerb()
	{
		return $this->_verb;
	}
	
	/**
	 * Setter for the $format private property
	 * @param string $format
	 */
	private function _setFormat($format)
	{
		$format = strtolower($format);
		
		if(!in_array($format, array('raw','html'))) {
			$format = 'raw';
		}
		
		$this->_format = $format;
	}
	
	/**
	 * Getter for the $format private property
	 * @return string
	 */
	private function _getFormat()
	{
		return $this->_format;
	}
	
	/**
	 * Magic property getter
	 * 
	 * @param type $name
	 * @return type 
	 */
	public function __get($name) {
		$method = '_get'.ucfirst($name);
		if(method_exists($this, $method)) {
			return $this->$method();
		} else {
			JLog::add("Unknown property $name in ".__CLASS__, JLog::WARNING);
			user_error("Unknown property $name in ".__CLASS__, E_WARNING);
		}
	}
	
	/**
	 * Magic property setter
	 * 
	 * @param type $name
	 * @param type $value 
	 */
	public function __set($name, $value) {
		$method = '_set'.ucfirst($name);
		if(method_exists($this, $method)) {
			$this->$method($value);
		} else {
			JLog::add("Unknown property $name in ".__CLASS__, JLog::WARNING);
			user_error("Unknown property $name in ".__CLASS__, E_WARNING);
		}
	}
	
}