<?php defined('_JEXEC') or die();

jimport('joomla.utilities.date');
$jNow = new JDate();
$year = $jNow->year;
$copydate = $year > 2011 ? '&mdash;'.$year : '';

?>
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
		<h1><?php echo JText::_('TPL_DEFAULT_TITLE') ?></h1>
	</header>
	<div id="main-content">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</div>
	<footer>
		<p><?php echo JText::sprintf('TPL_DEFAULT_COPYRIGHT', $copydate)?></p>
	</footer>
</body>
</html>