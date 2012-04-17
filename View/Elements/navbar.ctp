<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<?php
			echo $this->Html->link(
				Configure::read('Oven.config.title'),
				'/admin',
				array('class' => 'brand')
			);
			?>
			<ul class="nav">
				<?php
				$nav = Configure::read('Oven.config.nav');
				if (empty($nav)) {
					$nav = array_keys(Configure::read('Oven.recipe'));
				}
				?>
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
						echo '<li class="dropdown" data-toggle="dropdown">';
						echo $this->Html->link($val['title'] . '<b class="caret"></b>', '#', array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'));
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
			</ul>
		</div>
	</div>
</div>