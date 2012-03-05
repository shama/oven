<?php
App::uses('View', 'View');

/**
 * Theme View
 * 
 * @deprecated This wont work in 2.1 nor should it.
 */
class ThemeView extends View {
/**
 * Constructor for ThemeView sets $this->theme.
 *
 * @param Controller $controller Controller object to be rendered.
 */
	public function __construct($controller) {
		parent::__construct($controller);
		if ($controller) {
			$this->theme = $controller->theme;
		}
	}

/**
 * Return view paths in order for overriding Oven views.
 * 
 * @param string $plugin The name of the plugin views are being found for.
 * @param boolean $cached Set to true to force dir scan.
 * @return array paths
 */
	protected function _paths($plugin = null, $cached = true) {
		$paths = array(
			APP . 'View' . DS,
			APP . 'views' . DS,
		);
		if (!empty($this->theme)) {
			$paths[] = APP . 'View' . DS . 'Themed' . DS . $this->theme . DS . 'Plugin' . DS . 'Oven' . DS;
			$paths[] = App::pluginPath('Oven') . 'View' . DS . 'Themed' . DS . $this->theme . DS;
		}
		$paths[] = App::pluginPath('Oven') . 'View' . DS;
		return $paths;
	}
}
