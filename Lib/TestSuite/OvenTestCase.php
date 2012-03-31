<?php
/**
 * Oven Test Case
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenTestCase extends CakeTestCase {

/**
 * Setup paths to test_app
 */
	protected function _setupPaths() {
		App::build(array(
			'Model' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'Model' . DS,
			'Controller' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'Controller' . DS,
			'View' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'View' . DS,
		), App::RESET);
		CakePlugin::loadAll();
	}

/**
 * Clear test files
 */
	protected function _clearFiles() {
		$path = current(App::path('Model'));
		$dir = new Folder($path);
		foreach ($dir->find() as $file) {
			unlink($path . $file);
		}
		$path = current(App::path('Controller'));
		$dir = new Folder($path);
		foreach ($dir->find() as $file) {
			if ($file == 'TestsController.php') {
				continue;
			}
			unlink($path . $file);
		}
	}

/**
 * Clear test tables
 *
 * @param array $tables
 */
	protected function _clearTables($tables = array()) {
		if (!isset($this->Oven)) {
			return;
		}
		foreach ($tables as $table) {
			if (in_array($table, $this->Oven->SchemaBaker->tables)) {
				$this->Oven->query('DROP TABLE `' . $table . '`');
			}
		}
	}

}