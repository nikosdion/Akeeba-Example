<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class AkeebaViewList extends JView
{
	public function display($tpl = null) {
		$model = $this->getModel();
		$list = $model->getList();
		
		$this->assignRef('items', $list);

		parent::display($tpl);
	}
}