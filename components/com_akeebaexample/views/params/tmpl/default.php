<?php
defined('_JEXEC') or die('');
?>
<h2><?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTTOYOURSITE') ?></h2>

<p><?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTIONINFO') ?></p>

<form action="index.php" method="get">
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="task" value="showList" />
	<fieldset>
		<legend><?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTIONPARAMETERS'); ?></legend>
		
		<label for="host"><?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTIONPARAMETERS_HOSTNAME') ?></label>
		<input type="text" name="host" id="host" value="" autocomplete="off" />
		<br/>
		
		<label for="secret"><?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTIONPARAMETERS_SECRET') ?></label>
		<input type="password" name="secret" id="secret" value="" autocomplete="off" />
		<br/>

		<label>&nbsp;</label>
		<input type="submit" value="<?php echo JText::_('COM_AKEEBAEXAMPLE_CONNECTIONPARAMETERS_GETLIST')?>" />
	</fieldset>
</form>