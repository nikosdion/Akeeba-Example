<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.view');

// =========================================================================
// JView was never designed to be CLI-friendly or do as much as run outside
// of Joomla!, the CMS. We have to do a certain amount of fine trickery,
// overriding JView's functions which are tied to the CMS with code which
// does take into account that *there is no CMS running*.
// 
// IMPORTANT: The default layout is "txt" instead of "default", for convenience
// sake (so that you can have dual mode CLI + web apps)
// =========================================================================


class CustomViewCli extends JView
{
	public function __construct($config = array())
	{
		// Set the view name
		if (empty($this->_name))
		{
			if (array_key_exists('name', $config))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$this->_name = $this->getName();
			}
		}

		// Set the charset (used by the variable escaping functions)
		if (array_key_exists('charset', $config))
		{
			$this->_charset = $config['charset'];
		}

		// User-defined escaping callback
		if (array_key_exists('escape', $config))
		{
			$this->setEscape($config['escape']);
		}

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = JPATH_COMPONENT;
		}

		// Set the default template search path
		if (array_key_exists('template_path', $config))
		{
			// User-defined dirs
			$this->_setPath('template', $config['template_path']);
		}
		else
		{
			$this->_setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');
		}

		// Set the default helper search path
		if (array_key_exists('helper_path', $config))
		{
			// User-defined dirs
			$this->_setPath('helper', $config['helper_path']);
		}
		else
		{
			$this->_setPath('helper', $this->_basePath . '/helpers');
		}

		// Set the layout
		if (array_key_exists('layout', $config))
		{
			$this->setLayout($config['layout']);
		}
		else
		{
			$this->setLayout('txt');
		}
	}
	
	protected function _setPath($type, $path)
	{
		JLog::add('In '.__CLASS__.'::'.__METHOD__, JLog::DEBUG);
		jimport('joomla.application.helper');
		$component = 'com_example';

		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->_addPath($type, $path);
	}
	
	public function loadTemplate($tpl = null)
	{
		JLog::add('In '.__CLASS__.'::'.__METHOD__, JLog::DEBUG);
		// clear prior output
		$this->_output = null;

		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;
		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = JFactory::getLanguage();

		// Load the template script
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $file));
		$this->_template = JPath::find($this->_path['template'], $filetofind);

		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false)
		{
			$filetofind = $this->_createFileName('', array('name' => 'txt' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		if ($this->_template != false)
		{
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else
		{
			return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
		}
	}
}