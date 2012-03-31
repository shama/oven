<?php
App::uses('OvenAppShell', 'Oven.Console/Command');
App::uses('PhpBaker', 'Oven.Lib');

/**
 * Merge Shell
 * Merges classes.
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class MergeShell extends OvenAppShell {

/**
 * main
 */
	public function main() {
		$this->_header();
		$this->out("When a mommy and daddy class get together... ", 0);
		list($one, $two) = $this->args;
		$oneClass = substr($one, strrpos($one, '/') + 1);
		$onePath = substr($one, 0, strrpos($one, '/'));
		$twoClass = substr($two, strrpos($two, '/') + 1);
		$twoPath = substr($two, 0, strrpos($two, '/'));
		$PhpBaker = new PhpBaker();
		$PhpBaker->merge(
			array($oneClass, $onePath),
			array($twoClass, $twoPath)
		);
		$this->out("<good>;)</good>");
	}

/**
 * getOptionParser
 *
 * @return array
 */
	public function getOptionParser() {
		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'Oven.merge',
			'description' => array('Merges two classes.'),
			'arguments' => array(
				'from' => array('help' => __d('Oven', 'Class to merge from, `Controller/CommentsController`.'), 'required' => true),
				'to' => array('help' => __d('Oven', 'Class to merge to, `Controller/BlogsController`.'), 'required' => true),
			),
		));
	}

}