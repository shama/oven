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
 * @param array $config 
 * @return boolean
 */
	public function initCore($config = array()) {
		$path = $this->appPath . 'Config' . DS . 'core.php';
		if (!file_exists($path)) {
			throw new Exception(__d('oven', 'Core config file could not be found.'));
			return false;
		}
		if (!is_writable($path)) {
			throw new Exception(__d('oven', 'Core config is not writable.'));
			return false;
		}
		
		$config = Set::merge(array(
			'salt' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 40),
			'cipherSeed' => str_shuffle(str_repeat('0123456789',3)),
		), $config);
		
		// READ FILE
		$file = new File($path);
		$contents = $file->read();
		
		// CHANGE SALT
		$replace = "Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');";
		$with = "Configure::write('Security.salt', '".$config['salt']."');";
		$contents = str_replace($replace, $with, $contents);
		
		// CHANGE CIPHERSEED
		$replace = "Configure::write('Security.cipherSeed', '76859309657453542496749683645');";
		$with = "Configure::write('Security.cipherSeed', '".$config['cipherSeed']."');";
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
 * initDatabase
 * Builds your Config/database.php file from Config
 * @param array $config 
 * @return boolean
 * 
 * @deprecated Dont do this or use the CakePHP core instead
 */
	public function initDatabase($config = array()) {
		$path = $this->appPath . 'Config' . DS . 'database.php';
		if (!is_writable(dirname($path))) {
			throw new Exception(__d('oven', 'Config folder is not writable.'));
			return false;
		}
		if (file_exists($path)) {
			return true;
		}
		$config = Set::merge(array(
			'production' => array(),
			'development' => array(),
			'test' => array(),
			'dev_server' => $_SERVER['SERVER_NAME'],
		), $config);
		$defaults = array(
			'datasource' => 'Database/Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'login' => '',
			'password' => '',
			'database' => '',
			'prefix' => '',
		);
		$PhpBaker = new PhpBaker();
		$contents = '';
		foreach ($config as $key => $db) {
			if ($key == 'dev_server') {
				continue;
			}
			if ($key == 'test') {
				$db['prefix'] = 'test_suite_';
			}
			$db = Set::merge($defaults, $db);
			$contents .= $PhpBaker->arrayToCode(array($key => $db));
		}
		$write = "class DATABASE_CONFIG {\n";
		$write .= $contents;
		$write .= <<<END
	function __construct () {
		if (isset(\$_SERVER['SERVER_NAME'])){
			switch(\$_SERVER['SERVER_NAME']){
				case 'dev':
					\$this->default = \$this->development;
				break;
				default:
					\$this->default = \$this->production;
				break;
			}
		} else {
			\$this->default = \$this->development;
		}
	}
END;
		$write .= "\n}";
		$file = new File($path);
		$file->write($write);
		$file->close();
		return true;
	}
}