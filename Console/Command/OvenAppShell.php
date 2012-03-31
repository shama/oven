<?php
App::uses('OvenConfig', 'Oven.Lib');
App::uses('Shell', 'Console');

/**
 * Oven App Shell
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenAppShell extends Shell {

/**
 * Config
 *
 * @var OvenConfig
 */
	public $Config = null;

/**
 * __construct
 * @param type $stdout
 * @param type $stderr
 * @param type $stdin
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->Config = new OvenConfig();
		$this->stdout->styles('good', array('text' => 'green'));
		$this->stdout->styles('bad', array('text' => 'red'));
		$this->stdout->styles('ok', array('text' => 'yellow'));
	}

/**
 * Print the Oven header
 */
	protected function _header() {
		$this->out(array(
			'<bad> @@@@@@  @@@  @@@ @@@@@@@@ @@@  @@@</bad>                      <info>v' . $this->Config->version . '</info>',
			'<bad>@@!  @@@ @@!  @@@ @@!      @@!@!@@@',
			'@!@  !@! @!@  !@! @!!!:!   @!@@!!@!',
			'!!:  !!!  !: .:!  !!:      !!:  !!!',
			' : :. :     ::    : :: ::  ::    :</bad>',
		));
		$this->hr();
	}

}