<?php
App::uses('OvenAppShell', 'Oven.Console/Command');;

/**
 * Watch Shell
 * Watches for changes to config and bakes.
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class WatchShell extends OvenAppShell {
/**
 * uses
 * @var array
 */
	public $uses = array('Oven.Oven');

/**
 * Last file checksum
 *
 * @var integer
 */
	protected $_lastMd5 = '';

/**
 * main
 */
	public function main() {
		$this->_header();
		$this->out("Watching for changes (press Ctrl+C to quit)...");
		while(1) {
			if ($this->_hasChanged()) {
				$this->out("<info>Recipe has changed, baking... </info>", 0);
				try {
					$this->Oven->bake($this->Config->config);
				} catch (Exception $e) {
					$this->out('<bad>failed!</bad>');
					$this->out('<bad>' . $e->getMessage() . '</bad>');
					continue;
				}
				$this->out('<good>done!</good>');
			}
			sleep(1);
		}
	}

/**
 * getOptionParser
 *
 * @return array
 */
	public function getOptionParser() {
		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'Oven.watch',
			'description' => array('Watches for changes to a recipe then bakes'),
		));
	}

/**
 * Check if recipe has changed
 *
 * @return boolean
 */
	protected function _hasChanged() {
		$md5 = md5(file_get_contents($this->Config->path));
		if ($md5 != $this->_lastMd5) {
			$this->_lastMd5 = $md5;
			return true;
		}
		return false;
	}
}