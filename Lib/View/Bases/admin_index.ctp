<?php
$config = Configure::read('Oven.recipe');
$config = !empty($config[$table]) ? $config[$table] : array();
?>
<?php if (!empty($config)): ?>
	<div class="sidebar">
		<?php echo $this->Html->link('New '.$modelClass, array('action' => 'edit'), array('class' => 'btn primary')); ?>
	</div><!-- /.sidebar -->
	<div class="content">
		<h3><?php echo $type; ?></h3>
		<table>
			<tr>
				<?php foreach ($config['schema'] as $key => $val): ?>
					<?php
					$hideOn = !empty($val['hideOn']) ? $val['hideOn'] : array();
					if (in_array('admin_index', $hideOn)) {
						continue;
					}
					?>
					<th width="10%"><?php echo $this->Paginator->sort($key); ?></th>
				<?php endforeach; ?>
				<th>Actions</th>
			</tr>
			<?php foreach($data as $item): ?>
				<tr>
					<?php foreach ($config['schema'] as $key => $val): ?>
						<?php
						$hideOn = !empty($val['hideOn']) ? $val['hideOn'] : array();
						if (in_array('admin_index', $hideOn)) {
							continue;
						}
						?>
						<td><?php echo substr($item[$modelClass][$key], 0, 255); ?></td>
					<?php endforeach; ?>
					<td width="10%"><?php
					echo $this->Html->link('Edit', array('action' => 'edit', $item[$modelClass]['id']), array('class' => 'btn small'));
					echo '&nbsp;';
					if ($config['allowDelete']) {
						echo $this->Html->link('Delete', array('action' => 'delete', $item[$modelClass]['id']), array('class' => 'btn small error'), __d('oven', 'Are you sure?', true));
					}
					?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div><!-- /.content -->
<?php endif; ?>