<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('PhpBaker', 'Oven.Lib');

/**
 * For initializing Oven
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class Init extends OvenAppModel {

/**
 * name
 * @var string
 */
	public $name = 'Init';

/**
 * useTable
 * @var string
 */
	public $useTable = false;

/**
 * appPath
 * Location to build files
 * @var string
 */
	public $appPath = APP;

/**
 * Runs all the inits
 *
 * @param array $config
 * @return boolean
 */
	public function all($config = array()) {
		$this->initCore($config);
		//$this->initDatabase($config);
		return true;
	}

/**
 * initCore
 * Setup your Config/core.php
 *
 * @param array $config 
 * @return boolean
 * @throws CakeException
 */
	public function initCore($config = array()) {
		$path = $this->appPath . 'Config' . DS . 'core.php';
		if (!file_exists($path)) {
			throw new CakeException(__d('oven', 'Core config file could not be found.'));
		}
		if (!is_writable($path)) {
			throw new CakeException(__d('oven', 'Core config is not writable.'));
		}

		$config = Set::merge(array(
			'salt' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 40),
			'cipherSeed' => str_shuffle(str_repeat('0123456789', 3)),
		), $config);

		// READ FILE
		$file = new File($path);
		$contents = $file->read();

		// CHANGE SALT
		$replace = "Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');";
		$with = "Configure::write('Security.salt', '" . $config['salt'] . "');";
		$contents = str_replace($replace, $with, $contents);

		// CHANGE CIPHERSEED
		$replace = "Configure::write('Security.cipherSeed', '76859309657453542496749683645');";
		$with = "Configure::write('Security.cipherSeed', '" . $config['cipherSeed'] . "');";
		$contents = str_replace($replace, $with, $contents);

		// TURN ON ADMIN ROUTING
		$replace = "//Configure::write('Routing.prefixes', array('admin'));";
		$with = "Configure::write('Routing.prefixes', array('admin'));";
		$contents = str_replace($replace, $with, $contents);

		// WRITE FILE
		$file->write($contents);
		$file->close();

		return true;
	}

/**
 * Builds your Config/database.php file
 *
 * @param array $config
 * @return boolean
 */
	public function initDatabase($config = array()) {
		$dest = $this->appPath . 'Config' . DS . 'database.php';
		if (file_exists($dest)) {
			return false;
		}
		$config = array_merge(array(
			'datasource' => '',
			'host' => 'localhost',
			'login' => 'user',
			'password' => '',
			'database' => 'database',
			'prefix' => '',
		), $config);
		$src = CakePlugin::path('Oven') . 'Config' . DS . 'database.php';
		$File = new File($src);
		$code = $File->read();
		foreach ($config as $key => $val) {
			$code = str_replace("{{$key}}", $val, $code);
		}
		$File->close();
		$File = new File($dest, true);
		$File->write($code);
		$File->close();
		return true;
	}

}