<?php
defined('_JEXEC') or die();

JLoader::import('includes.custom.view.cli', JPATH_BASE);

class AkeebaViewList extends CustomViewCli
{
	public function display($tpl = null) {
		$model = $this->getModel();
		$list = $model->getList();
		
		$this->assignRef('items', $list);
		
		parent::display($tpl);
	}
}