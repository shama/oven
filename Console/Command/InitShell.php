<?php
App::uses('OvenAppShell', 'Oven.Console/Command');

/**
 * Init Shell
 * Inits Oven
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
 * main
 */
	public function main() {
		$this->_header();
		$this->out('Turning on the Oven... ', 0);
		try {
			$this->Init->all();
		} catch(Exception $e) {
			$this->out('<bad>failed!</bad>');
			$this->out($e->getMessage());
			return;
		}
		$this->out('<good>careful it\'s hot!</good>');
	}

/**
 * getOptionParser
 *
 * @return array
 */
	public function getOptionParser() {
		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'Oven.init',
			'description' => array('Automatically initialize Oven'),
		));
	}
}