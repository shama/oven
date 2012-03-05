<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	echo $this->Html->charset();
	echo $this->Html->meta('icon');
	echo $this->Html->css(array(
		'/oven/css/bootstrap.min.css',
		'/oven/css/admin',
	));
	echo $this->Html->script(array(
		'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
		'/oven/js/bootstrap-modal',
		'/oven/js/bootstrap-alerts',
		'/oven/js/bootstrap-twipsy',
		'/oven/js/bootstrap-popover',
		'/oven/js/bootstrap-dropdown',
		//'/oven/js/bootstrap-scrollspy',
		'/oven/js/bootstrap-tabs',
		'/oven/js/admin',
		'jquery.stream-1.2',
	));
	?>
</head>
<body>
<?php echo $this->element('topbar', array('plugin' => 'Oven')); ?>
<div class="container-fluid">
	<?php echo $this->Session->flash(); ?>
	<?php echo $content_for_layout; ?>
	<footer class="text-right">
		<p><?php echo Configure::read('Oven.config.footer'); ?></p>
	</footer>
</div><!-- /.container -->
<?php
echo $scripts_for_layout;
echo $this->Js->writeBuffer();
?>
</body>
</html>