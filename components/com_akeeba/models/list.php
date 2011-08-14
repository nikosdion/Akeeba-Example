<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class AkeebaModelList extends JModel
{
	private $_list = null;
	
	public function getList()
	{
		if(is_null($this->_list)) {
			// Get an Akeeba Remote API Helper instance
			require_once JPATH_COMPONENT.'/helpers/api.php';
			$api = new AkeebaHelperApi();
			$api->host = $this->getState('host');
			$api->secret = $this->getState('secret');

			// Try to figure out the best way to communicate to the site
			$works = false;
			$exception = null;
			foreach(array('GET','POST') as $verb) {
				if($works) break;
				$api->verb = $verb;

				foreach(array('raw','html') as $format) {
					if($works) break;
					$api->format = $format;

					try {
						$ret = $api->doQuery('getVersion', array());
						$works = true;
						$this->_versionInfo = $ret->body->data;
					} catch (JException $e) {
						$exception = $e;
					}
				}
			}

			if(!$works) {
				throw new JException('There is no way I can connect to your site; everything crashes and burns!');
			} else {
				// Check the response
				if($ret->body->status != 200) {
					throw new JException('Error '.$ret->body->status." - ".$ret->body->data);
				}

				// Check the API version
				if($ret->body->data->api < 306) {
					throw new JException('You need to install a newer version of Akeeba Backup on your site');
				}
			}
			
			// Get the data
			$from = $this->getState('from', 0);
			$limit = $this->getState('limit', 50);

			$data = $api->doQuery('listBackups', array('from' => $from, 'limit' => $limit));

			if($data->body->status != 200) {
				throw new JException("Could not list backup records");
			} else {
				$this->_list = $data->body->data;
			}
		}
		
		return $this->_list;
	}
}