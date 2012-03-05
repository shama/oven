<?php
$config = Configure::read('Oven.recipe');
$config = !empty($config[$table]) ? $config[$table] : array();
?>
<?php if (!empty($config)): ?>
	<div class="sidebar">
	</div><!-- /.sidebar -->
	<div class="content">
		<h3><?php echo (empty($this->data)) ? 'New '.$modelClass : 'Editing '.$modelClass; ?></h3>
		
		<?php if ($config['hasChildren']): ?>
			<ul class="tabs">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#">+</a></li>
			</ul>
		<?php endif; ?>

		<?php
		echo $this->Form->create($modelClass, array(
			'url' => array('plugin' => false, 'action' => 'edit'),
			'enctype' => 'multipart/form-data',
			'class' => 'form-stacked',
		));
		
		if (!empty($this->data[$modelClass]['id'])) {
			echo $this->Form->hidden('id', array('value' => $this->data[$modelClass]['id']));
		}
		
		foreach ($config['schema'] as $key => $val) {
			echo $this->Form->input($key, $val);
		}
		
		echo '<div class="actions">';
			echo $this->Form->submit('Save', array(
				'class' => 'btn primary',
				'div' => false,
			));
			echo '&nbsp;';
			$this->Form->unlockField('continue_editing');
			echo $this->Form->button('Save & Continue Editing', array(
				'name' => 'continue_editing',
				'value' => true,
				'class' => 'btn',
				'div' => false,
			));
			echo '&nbsp;&nbsp;';
			echo $this->Html->link(
				__d('oven', 'Cancel', true),
				array('action' => 'index'),
				array('class' => 'btn')
			);
			if (!empty($this->data[$modelClass]['id']) && $config['allowDelete']) {
				echo $this->Html->link(
					__d('oven', 'Delete', true),
					array('action' => 'delete', $this->data[$modelClass]['id']),
					array('class' => 'btn error pull-right confirm')
				);
			}
		echo '</div>';
		echo $this->Form->end();
		?>
	</div><!-- /.content -->
<?php endif; ?>