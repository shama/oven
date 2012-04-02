<?php
if (empty($config)) {
	$config = Configure::read('Oven.recipe');
	$config = !empty($config[$table]) ? $config[$table] : array();
}
$typeTitle = !empty($typeTitle) ? $typeTitle : $this->name;
?>
<div class="row-fluid">
	<div class="span2">
		<?php if ($this->fetch('sidebar')): ?>
			<?php echo $this->fetch('sidebar'); ?>
		<?php endif; ?>
	</div>
	<div class="span10">
		<?php if ($this->fetch('content')): ?>
			<?php echo $this->fetch('content'); ?>
		<?php else: ?>
			<h3><?php echo (empty($this->request->data)) ? 'New ' . Inflector::singularize($typeTitle) : 'Editing ' . Inflector::singularize($typeTitle); ?></h3>
			<?php
			echo $this->Form->create($modelClass, array(
				'url' => array('plugin' => false, 'action' => 'edit'),
				'enctype' => 'multipart/form-data',
				'class' => 'form-horizontal',
			));

			if (!empty($this->request->data[$modelClass]['id'])) {
				echo $this->Form->hidden('id', array('value' => $this->data[$modelClass]['id']));
			}

			if ($this->fetch('inputs')) {
				/**
				 * Use $this->Form->setEntity($modelClass, true);
				 * to have FormHelper automatically set request->data on inputs.
				 */
				echo $this->fetch('inputs');
			} else {
				foreach ($config['schema'] as $key => $val) {
					echo $this->Form->input($key, $val);
				}
			}

			echo '<div class="actions">';
				echo $this->Form->submit('Save', array(
					'class' => 'btn btn-primary',
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
				$allowDelete = isset($config['allowDelete']) ? $config['allowDelete'] : true;
				if (!empty($this->request->data[$modelClass]['id']) && $allowDelete) {
					echo $this->Html->link(
						__d('oven', 'Delete', true),
						array('action' => 'delete', $this->request->data[$modelClass]['id']),
						array('class' => 'btn btn-danger pull-right confirm')
					);
				}
			echo '</div>';
			echo $this->Form->end();
			?>
		<?php endif; ?>
	</div>
</div>