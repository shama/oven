<div class="topbar">
	<div class="topbar-inner">
		<div class="container-fluid">
			<?php
			echo $this->Html->link(Configure::read('Oven.config.title'), '/admin', array('class' => 'brand'));
			?>
			<ul class="nav">
				<?php $nav = Configure::read('Oven.config.nav'); ?>
				<?php if (!empty($nav)): ?>
					<?php foreach ($nav as $key => $val): ?>
						<?php
						if (!is_array($val)) {
							$val = array(
								'title' => Inflector::humanize($val),
								'url' => '/admin/' . $val,
							);
						}
						if (empty($val['children'])) {
							echo '<li>' . $this->Html->link($val['title'], $val['url']) . '</li>';
						} else {
							echo '<li class="dropdown" data-dropdown="dropdown">';
							echo $this->Html->link($val['title'], '#', array('class' => 'dropdown-toggle'));
							echo '<ul class="dropdown-menu">';
							foreach ($val['children'] as $k => $v) {
								$title = !empty($v['title']) ? $v['title'] : Inflector::humanize($k);
								$url = !empty($v['url']) ? $v['url'] : '/admin/' . $k;
								echo $this->Html->link($title, $url);
							}
							echo '</ul></li>';
						}
						?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>