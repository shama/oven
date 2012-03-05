<?php
App::uses('BasesController', 'Oven.Controller');
App::import('Vendor', 'SomeTestVendor');

/**
 * Tests Controller
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright Copyright 2011 Kyle Robinson Young. All rights reserved.
 */
class TestsController extends BasesController {
/**
 * name
 * @var string
 */
	public $name = 'Tests';

/**
 * theme
 * @var string
 */
	public $theme = 'Gallery';


	protected $_test = array(
		'something' => array(

			'nested' => 'in an array',
	),
	);

/**
 * admin_index
 */
	public function admin_index($param1 = 'test') {
		// LETS DO SOME MATH!
		$a = 5;
		$b = 4;
		$c = $a * $b + ($a - 6);
		$this->set('c', $c);
	}

}