<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	echo $this->Html->charset();
	echo $this->Html->meta('icon');
	echo $this->Html->css(
		'/oven/less/bootstrap.less',
		'stylesheet/less',
		array('ext' => false)
	);
	echo $this->Html->script(array(
		'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
		'/oven/bootstrap/js/bootstrap-transition',
		'/oven/bootstrap/js/bootstrap-alert',
		'/oven/bootstrap/js/bootstrap-modal',
		'/oven/bootstrap/js/bootstrap-dropdown',
		'/oven/bootstrap/js/bootstrap-scrollspy',
		'/oven/bootstrap/js/bootstrap-tab',
		'/oven/bootstrap/js/bootstrap-tooltip',
		'/oven/bootstrap/js/bootstrap-popover',
		'/oven/bootstrap/js/bootstrap-button',
		'/oven/bootstrap/js/bootstrap-collapse',
		'/oven/bootstrap/js/bootstrap-carousel',
		'/oven/bootstrap/js/bootstrap-typeahead',
		'/oven/js/less-1.3.0.min',
	));
	?>
</head>
<body>
<?php echo $this->element('Oven.navbar'); ?>
<div class="container-fluid">
	<?php echo $this->Session->flash(); ?>
	<?php echo $content_for_layout; ?>
	<footer class="pull-right">
		<p><?php echo Configure::read('Oven.config.footer'); ?></p>
	</footer>
</div><!-- /.container -->
<?php
echo $scripts_for_layout;
echo $this->Js->writeBuffer();
?>
</body>
</html>