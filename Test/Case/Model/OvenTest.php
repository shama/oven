<?php
App::uses('OvenTestCase', 'Oven.TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('OvenAppModel', 'Oven.Model');
App::uses('Oven', 'Oven.Model');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Oven Test Case
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 * 
 * @todo Better use the test datasource
 */
class OvenTest extends OvenTestCase {
/**
 * Test config
 *
 * @var array
 */
	public $config = array(
		'config' => array(
			'title' => 'CMS',
			'nav' => array(
				array('title' => 'Home', 'url' => '/'),
				array('title' => 'About', 'url' => '/about'),
			),
			'footer' => 'Copyright 2011 Kyle Robinson Young. All Rights Reserved.',
			'code_header' => array(
				'package' => 'cms',
				'author' => 'Kyle Robinson Young <kyle at dontkry.com>',
				'copyright' => '2012 Kyle Robinson Young',
			),
		),
		'recipe' => array(
			'pages' => array(
				'overwrite' => false,
				'schema' => array(
					'title' => array(
						'help' => 'Enter the title of the page.',
						'validate' => 'notEmpty',
					),
					'body' => array(
						'type' => 'ckeditor',
					),
					'slug' => array(
						'type' => 'slug',
						'help' => 'Leave blank to autofill from title.',
					),
				),
				'model' => array(
					'displayField' => 'title',
					'order' => array('Page.title ASC'),
				),
				'controller' => array(
				),
				'view' => array(
				),
			),
			'contacts' => array(
				'schema' => array(
					'title' => array(),
				),
			),
		),
		'oven' => array(
			'clean_tables' => true,
			'overwrite' => false,
		),
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->_setupPaths();
		$this->Oven = ClassRegistry::init('Oven.Oven');
		$this->Oven->appPath = App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$this->_clearTables(array('pages'));
		$this->_clearFiles();
		unset($this->Oven);
	}

/**
 * testBake
 */
	public function testBake() {return;
		// TEST SUPER SIMPLE SCHEMA
		$config = array(
			'recipe' => array(
				'pages' => array(
					'schema' => array(
						'title' => array(),
						'body' => array('type' => 'textarea'),
						'slug' => array(),
						'image' => array('type' => 'file'),
					),
				),
			),
		);
		$this->Oven->bake($config);
		$this->assertTrue(file_exists($this->Oven->appPath . 'Model' . DS . 'Page.php'));
		$this->assertTrue(file_exists($this->Oven->appPath . 'Controller' . DS . 'PagesController.php'));
		try {
			$res = $this->Oven->query('DESCRIBE pages');
			$fields = Set::extract('/COLUMNS/Field', $res);
			sort($fields);
			$expected = array('body', 'created', 'id', 'modified', 'slug', 'title');
			$this->assertEqual($fields, $expected);
		} catch (PDOException $e) {
			$this->assertTrue(false);
		}
		
		// TODO: TEST SUPER COMPLEX
	}

/**
 * testErrors
 */
	public function testErrors() {
		// TODO: Write me
	}

/**
 * testBakeTables
 */
	public function testBakeTables() {
		$prefix = $this->Oven->SchemaBaker->tablePrefix;
		
		// BUILD TABLE
		$this->assertTrue($this->Oven->bakeTables($this->config));
		$this->assertTrue(in_array($prefix . 'pages', $this->Oven->SchemaBaker->tables));
		$res = $this->Oven->query('DESCRIBE ' . $prefix . 'pages');
		$fields = Set::extract('/COLUMNS/Field/.', $res);
		$this->assertEqual($fields, array(
			'id', 'created', 'modified', 'title', 'body', 'slug',
		));
		
		// ALTER TABLE ADD
		$this->config['recipe']['pages']['schema']['add_field'] = array(
			'type' => 'text',
		);
		$this->assertTrue($this->Oven->bakeTables($this->config));
		$res = $this->Oven->query('DESCRIBE ' . $prefix . 'pages');
		$fields = Set::extract('/COLUMNS/Field/.', $res);
		$this->assertEqual($fields, array(
			'id', 'created', 'modified', 'title', 'body', 'slug', 'add_field',
		));
		
		// ALTER TABLE DROP - CLEAN_TABLES OFF
		$this->config['config']['clean_tables'] = false;
		unset($this->config['recipe']['pages']['schema']['add_field']);
		$this->assertTrue($this->Oven->bakeTables($this->config));
		$res = $this->Oven->query('DESCRIBE ' . $prefix . 'pages');
		$fields = Set::extract('/COLUMNS/Field/.', $res);
		$this->assertEqual($fields, array(
			'id', 'created', 'modified', 'title', 'body', 'slug', 'add_field',
		));
		
		// ALTER TABLE DROP - CLEAN_TABLES ON
		$this->config['config']['clean_tables'] = true;
		$this->assertTrue($this->Oven->bakeTables($this->config));
		$res = $this->Oven->query('DESCRIBE ' . $prefix . 'pages');
		$fields = Set::extract('/COLUMNS/Field/.', $res);
		$this->assertEqual($fields, array(
			'id', 'created', 'modified', 'title', 'body', 'slug',
		));
	}

/**
 * testBakeModels
 */
	public function testBakeModels() {
		// CREATE FILE
		$file = $this->Oven->appPath.'Model'.DS.'Page.php';
		$this->assertTrue($this->Oven->bakeModels($this->config));
		$this->assertTrue(file_exists($file));
		$code = implode('', file($file));
		$this->assertTrue(strpos($code, "public \$displayField = 'title';") !== false);
		
		// UPDATE FILE
		$this->config['recipe']['pages']['model']['displayField'] = 'slug';
		$this->assertTrue($this->Oven->bakeModels($this->config));
		$code = implode('', file($file));
		$this->assertTrue(strpos($code, "public \$displayField = 'slug';") !== false);
		
		// CHECK CODE HEADERS
		$this->assertTrue((strpos($code, '* @package cms') !== false));
		$this->assertTrue((strpos($code, '* @author Kyle Robinson Young <kyle at dontkry.com>') !== false));
		$this->assertTrue((strpos($code, '* @copyright 2012 Kyle Robinson Young') !== false));
		
		// TODO: Add duplicate class variables, make sure it only does one
	}
	
/**
 * testBakeControllers
 */
	public function testBakeControllers() {
		// CREATE FILE
		$file = current(App::path('Controller')) . 'PagesController.php';
		$this->assertTrue($this->Oven->bakeControllers($this->config));
		$this->assertTrue(file_exists($file));
		$code = implode('', file($file));
		$this->assertTrue(strpos($code, "public \$name = 'Pages';") !== false);
		
		// UPDATE FILE
		$this->config['recipe']['pages']['controller']['scaffold'] = 'admin';
		$this->assertTrue($this->Oven->bakeControllers($this->config));
		$code = implode('', file($file));
		$this->assertTrue(strpos($code, "public \$scaffold = 'admin';") !== false);
	}
}