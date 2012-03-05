<?php
App::uses('OvenTestCase', 'Oven.TestSuite');
App::uses('PhpBaker', 'Oven.Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * PhpBaker Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class PhpBakerTest extends OvenTestCase {
/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->_setupPaths();
	}

/**
 * tearDown method
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$this->_clearFiles();
	}

/**
 * endTest
 * @param string $method
 */
	public function endTest($method) {
		parent::endTest($method);
		$this->_clearFiles();
	}

/**
 * testRead
 */
	public function testRead() {
		$PhpBaker = new PhpBaker('TestsController', 'Controller');
		$result = $PhpBaker->read();
		$expected = array(
			'class' => array(
				'class' => 'class TestsController extends BasesController',
				'doc' => '/**
 * Tests Controller
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright Copyright 2011 Kyle Robinson Young. All rights reserved.
 */',
				'uses' => array(
					"App::uses('BasesController', 'Oven.Controller');",
					"App::import('Vendor', 'SomeTestVendor');",
				),
			),
			'properties' => array(
				'name' => array(
					'doc' => '/**
 * name
 * @var string
 */',
					'value' => 'Tests',
					'access' => 'public',
				),
				'theme' => array(
					'doc' => '/**
 * theme
 * @var string
 */',
					'value' => 'Gallery',
					'access' => 'public',
				),
				'_test' => array(
					'doc' => '',
					'value' => array(
						'something' => array(
							'nested' => 'in an array',
						),
					),
					'access' => 'protected',
				),
			),
			'methods' => array(
				'admin_index' => array(
					'doc' => '/**
 * admin_index
 */',
					'value' => '	public function admin_index($param1 = \'test\') {
		// LETS DO SOME MATH!
		$a = 5;
		$b = 4;
		$c = $a * $b + ($a - 6);
		$this->set(\'c\', $c);
	}
',
					'access' => 'public',
				),
			),
		);//$PhpBaker->write($expected);
		$this->assertEquals($expected, $result);
	}

/**
 * testWrite
 */
	public function testWrite() {
		$expected = array(
			'class' => array(
				'class' => 'class CommentsController extends BasesController',
				'doc' => '/**
 * Comments Controller
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright Copyright 2011 Kyle Robinson Young. All rights reserved.
 */',
				'uses' => array(
					"App::uses('BasesController', 'Oven.Controller');",
					"App::import('Vendor', 'SomeTestVendor');",
				),
			),
			'properties' => array(
				'name' => array(
					'doc' => '/**
 * name
 * @var string
 */',
					'value' => 'Comments',
					'access' => 'public',
				),
				'theme' => array(
					'doc' => '/**
 * theme
 * @var string
 */',
					'value' => 'Gallery',
					'access' => 'public',
				),
				'_test' => array(
					'doc' => '',
					'value' => array(
						'something' => array(
							'nested' => 'in an array',
						),
					),
					'access' => 'protected',
				),
			),
			'methods' => array(
				'admin_index' => array(
					'doc' => '/**
 * admin_index
 */',
					'value' => '	public function admin_index($param1 = \'test\') {
		// LETS DO SOME MATH!
		$a = 5;
		$b = 4;
		$c = $a * $b + ($a - 6);
		$this->set(\'c\', $c);
	}
',
					'access' => 'public',
				),
			),
		);
		$PhpBaker = new PhpBaker('CommentsController', 'Controller');
		$PhpBaker->write($expected);
		$result = $PhpBaker->read();
		$this->assertEquals($expected, $result);
	}

/**
 * testMerge
 */
	public function testMerge() {
		$from = array(
			'class' => array(
				'class' => 'class CommentsController extends BasesController',
				'doc' => '/**
 * Comments Controller
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright Copyright 2011 Kyle Robinson Young. All rights reserved.
 */',
				'uses' => array(
					"App::uses('BasesController', 'Oven.Controller');",
				),
			),
			'properties' => array(
			),
			'methods' => array(
				'admin_index' => array(
					'doc' => '/**
 * admin_index
 */',
					'value' => '	public function admin_index($param1 = \'test\') {
		// I SHOULD BE OVERWRITTEN
	}
',
					'access' => 'public',
				),
				'admin_edit' => array(
					'doc' => '/**
 * admin_edit
 */',
					'value' => '	public function admin_edit($id = null) {
		// I SHOULD BE MERGED
	}
',
					'access' => 'public',
				),
			),
		);
		$PhpBakerFrom = new PhpBaker('CommentsController', 'Controller');
		$PhpBakerFrom->write($from);
		
		$path = current(App::path('Controller'));
		copy($path . 'TestsController.php', $path . 'AnotherTestsController.php');
		$file = new File($path . 'AnotherTestsController.php');
		$contents = $file->read();
		$contents = str_ireplace('class TestsController', 'class AnotherTestsController', $contents);
		$file->write($contents);
		$file->close();
		
		$PhpBakerTo = new PhpBaker();
		$PhpBakerTo->merge(
			array('CommentsController', 'Controller'),
			array('AnotherTestsController', 'Controller')
		);
		$result = $PhpBakerTo->read();
		$expected = array(
			'class' => array(
				'class' => 'class AnotherTestsController extends BasesController',
				'doc' => '/**
 * Tests Controller
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright Copyright 2011 Kyle Robinson Young. All rights reserved.
 */',
				'uses' => array(
					"App::uses('BasesController', 'Oven.Controller');",
					"App::import('Vendor', 'SomeTestVendor');",
				),
			),
			'properties' => array(
				'name' => array(
					'doc' => '/**
 * name
 * @var string
 */',
					'value' => 'Tests',
					'access' => 'public',
				),
				'theme' => array(
					'doc' => '/**
 * theme
 * @var string
 */',
					'value' => 'Gallery',
					'access' => 'public',
				),
				'_test' => array(
					'doc' => '',
					'value' => array(
						'something' => array(
							'nested' => 'in an array',
						),
					),
					'access' => 'protected',
				),
			),
			'methods' => array(
				'admin_index' => array(
					'doc' => '/**
 * admin_index
 */',
					'value' => '	public function admin_index($param1 = \'test\') {
		// LETS DO SOME MATH!
		$a = 5;
		$b = 4;
		$c = $a * $b + ($a - 6);
		$this->set(\'c\', $c);
	}
',
					'access' => 'public',
				),
				'admin_edit' => array(
					'doc' => '/**
 * admin_edit
 */',
					'value' => '	public function admin_edit($id = null) {
		// I SHOULD BE MERGED
	}
',
					'access' => 'public',
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToCode
 */
	public function testArrayToCode() {
		$PhpBaker = new PhpBaker('Comment', 'Model');
		$in = array(
			'name' => 'Test',
			'actsAs' => array(
				'Translate' => array('name', 'body'),
			),
		);
		$result = $PhpBaker->arrayToCode($in);
		$expected = "array(
	'name' => 'Test',
	'actsAs' => array(
		'Translate' => array(
			'name',
			'body',
		),
	),
)";
		$this->assertEquals($expected, $result);
	}
}