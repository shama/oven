<?php
App::uses('OvenAppShell', 'Oven.Console/Command');

/**
 * Bake Shell
 * Bakes a recipe.
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class BakeShell extends OvenAppShell {
/**
 * uses
 * @var array
 */
	public $uses = array('Oven.Oven');

/**
 * main
 */
	public function main() {
		$this->_header();
		$this->out("Baking... ", 0);
		$this->Oven->bake($this->Config->config);
		$this->out("<good>Mmm oven fresh.</good>");
	}

/**
 * getOptionParser
 *
 * @return array
 */
	public function getOptionParser() {
		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'Oven.bake',
			'description' => array('Bakes an Oven recipe'),
		));
	}
}