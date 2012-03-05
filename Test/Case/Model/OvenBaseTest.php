<?php
App::uses('OvenTestCase', 'Oven.TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('OvenAppModel', 'Oven.Model');
App::uses('OvenBase', 'Oven.Model');
App::uses('Oven', 'Oven.Model');
App::uses('OvenConfig', 'Oven.Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * OvenBase Test Case
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenBaseTest extends OvenTestCase {
/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->_setupPaths();
		$appPath = App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS;
		$config = array(
			'recipe' => array(
				'pages' => array(
					'schema' => array(
						'title' => array(),
						'body' => array('type' => 'ckeditor'),
						'slug' => array('type' => 'slug'),
						'image' => array('type' => 'file'),
					),
				),
			),
			'config' => array(
				'upload_location' => $appPath . 'webroot' . DS . 'files' . DS,
			),
		);
		new OvenConfig($config);
		$this->Oven = ClassRegistry::init('Oven.Oven');
		$this->Oven->appPath = $appPath;
		$this->Oven->bake($config);
		App::uses('Page', 'Model');
		$this->Page = ClassRegistry::init('Page');
		new OvenConfig($config);
	}

/**
 * tearDown
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$this->_clearTables(array('pages'));
		$this->_clearFiles();
		unset($this->Oven);
	}

/**
 * testSaveFile
 */
	public function testSaveFile() {
		$data = array(
			'Page' => array(
				'title' => 'Title',
				'body' => '<p>This is a test page.</p>',
				'slug' => '',
				'image' => array(
					'name' => '4df14a7d677.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/phpX6Iota',
					'error' => 0,
					'size' => 33575,
				),
			),
		);
		$result = $this->Page->save($data);
		$this->assertEquals('24eb8c4cdef7a0c9f51d23351df02dfb.jpg', $result['Page']['image']);
		$dir = new Folder(Configure::read('Oven.config.upload_location') . 'pages');
		// TODO: Check if file created in folder
		$dir->delete();
	}

/**
 * testSlug
 */
	public function testSlug() {return;
		$data = array(
			'Page' => array(
				'title' => 'This is my super long title',
				'body' => '<p>This is a test page.</p>',
				'slug' => '',
			),
		);
		$result = $this->Page->save($data);
		$this->assertEqual(Set::extract('/Page/slug', $result), array('this-is-my-super-long-title'));
	}
}