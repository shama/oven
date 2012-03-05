<?php
App::uses('SchemaBaker', 'Oven.Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * SchemaBaker Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class SchemaBakerTest extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SchemaBaker = new SchemaBaker();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SchemaBaker);
		parent::tearDown();
	}

/**
 * testBake
 * 
 * @return void
 */
	public function testBake() {
		$config = array(
			'recipe' => array(
				'pages' => array(
					'schema' => array(
						
					),
				),
				'tests' => array(
					'schema' => array(
						
					),
				),
			),
		);
		$this->SchemaBaker->bake($config);
	}
}