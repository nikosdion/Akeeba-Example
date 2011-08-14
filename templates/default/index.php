<?php defined('_JEXEC') or die();?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link href='http://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />

<?php if ($this->direction == 'rtl') : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.css" type="text/css" />
<?php endif; ?>
</head>
<body>
	<header>
		<h1>Akeeba Example Application</h1>
	</header>
	<div id="main-content">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</div>
	<footer>
		<p>Copyright &copy;2011 Nicholas K. Dionysopoulos</p>
	</footer>
</body>
</html>