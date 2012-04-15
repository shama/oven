<?php
App::uses('OvenAppShell', 'Oven.Console/Command');

/**
 * Init Shell
 * Inits Oven and creates a Config/database.php if it does not exist
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class InitShell extends OvenAppShell {

/**
 * uses
 *
 * @var array
 */
	public $uses = array('Oven.Init');

/**
 * Options available
 *
 * @var array
 */
	protected $_options = array(
		'datasource' => array(
			'short' => 's',
			'help' => 'CakePHP DataSource.',
			'default' => 'Database/Mysql',
		),
		'host' => array(
			'short' => 'o',
			'help' => 'Database host',
			'default' => 'localhost',
		),
		'login' => array(
			'short' => 'u',
			'help' => 'Database login/username',
			'default' => 'username',
		),
		'password' => array(
			'short' => 'p',
			'help' => 'Database password',
			'default' => 'password',
		),
		'database' => array(
			'short' => 'd',
			'help' => 'Database name',
			'default' => 'database',
		),
		'prefix' => array(
			'short' => 'x',
			'help' => 'Database table prefix',
			'default' => '',
		),
	);

/**
 * __construct
 *
 * @param ConsoleOutput $stdout
 * @param ConsoleOutput $stderr
 * @param ConsoleInput $stdin
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		foreach ($this->_options as $key => $option) {
			$this->_options[$key]['help'] = __d('oven', $option['help']);
		}
	}

/**
 * main
 */
	public function main() {
		$this->_header();
		$this->_gatherParams();
		$this->out('Turning on the Oven... ', 0);
		try {
			$this->Init->all($this->params);
		} catch(Exception $e) {
			$this->out('<bad>failed!</bad>');
			$this->out($e->getMessage());
			return;
		}
		$this->out('<good>careful it\'s hot!</good>');
	}

/**
 * Gather missing params
 *
 * @return boolean
 */
	protected function _gatherParams() {
		$required = array('login', 'password', 'database');
		foreach ($required as $key) {
			if ($this->params[$key] == $this->_options[$key]['default']) {
				$this->params[$key] = $this->in('Database ' . $key . '?', null, $this->params[$key]);
			}
		}
		return true;
	}

/**
 * getOptionParser
 *
 * @return array
 */
	public function getOptionParser() {
		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'Oven.init',
			'description' => array(__d('oven', 'Automatically initialize Oven')),
			'options' => $this->_options,
		));
	}

}