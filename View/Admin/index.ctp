<?php
$table = Inflector::tableize($this->name);
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
		<?php else: ?>
			<?php echo $this->Html->link('New ' . Inflector::singularize($typeTitle), array('action' => 'edit'), array('class' => 'btn btn-primary')); ?>
		<?php endif; ?>
	</div>
	<div class="span10">
		<?php if ($this->fetch('content')): ?>
			<?php echo $this->fetch('content'); ?>
		<?php else: ?>
			<h3><?php echo $typeTitle; ?></h3>
			<?php if (!empty($data)): ?>
				<table class="table table-striped">
				<tr>
					<?php foreach ($config['schema'] as $key => $val): ?>
						<?php
						if (is_int($key)) {
							$key = $val;
						}
						$hideOn = key_exists('hideOn', (array) $val) ? $val['hideOn'] : array();
						if (in_array('admin_index', $hideOn)) {
							continue;
						}
						?>
						<th><?php echo $this->Paginator->sort($key); ?></th>
					<?php endforeach; ?>
					<th><?php echo __d('oven', 'Actions'); ?></th>
				</tr>
				<?php foreach($data as $item): ?>
					<tr>
						<?php foreach ($config['schema'] as $key => $val): ?>
							<?php
							if (is_int($key)) {
								$key = $val;
							}
							$hideOn = key_exists('hideOn', (array) $val) ? $val['hideOn'] : array();
							if (in_array('admin_index', $hideOn)) {
								continue;
							}
							?>
							<td><?php echo substr($item[$modelClass][$key], 0, 255); ?></td>
						<?php endforeach; ?>
						<td width="10%"><?php
						echo $this->Html->link(
							__d('oven', 'Edit'),
							array('action' => 'edit', $item[$modelClass]['id']),
							array('class' => 'btn btn-small')
						);
						echo '&nbsp;';
						$allowDelete = isset($config['allowDelete']) ? $config['allowDelete'] : true;
						if ($allowDelete) {
							echo $this->Html->link(
								__d('oven', 'Delete'),
								array('action' => 'delete', $item[$modelClass]['id']),
								array('class' => 'btn btn-small btn-danger'),
								__d('oven', 'Are you sure?')
							);
						}
						?></td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>