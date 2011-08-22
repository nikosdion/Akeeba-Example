<?php
defined('_JEXEC') or die('');
?>
<h2>Connect to your site</h2>

<p>
	Before you can see a list of your backups, you need to provide the conenction
	information to your site below. Please remember to turn on the <em>Enable
	remote and front-end backups</em> option in your Akeeba Backup Component 
	Parameters before using this application.
</p>

<form action="index.php" method="get">
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="task" value="showList" />
	<fieldset>
		<legend>Connection parameters</legend>
		
		<label for="host">Hostname (e.g. <em>www.example.com</em>)</label>
		<input type="text" name="host" id="host" value="" autocomplete="false" />
		<br/>
		
		<label for="secret">Secret key</label>
		<input type="password" name="secret" id="secret" value="" autocomplete="false" />
		<br/>

		<label>&nbsp;</label>
		<input type="submit" />
	</fieldset>
</form>