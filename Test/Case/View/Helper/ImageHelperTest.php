<?php
App::uses('View', 'View');
App::uses('ImageHelper', 'Oven.View/Helper');
App::uses('Folder', 'Utility');

/**
 * Image Helper Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class ImageHelperTest extends CakeTestCase {

/**
 * setup
 */
	public function setUp() {
		parent::setUp();
		$controller = null;
		$this->View = new View($controller);
		$this->Image = new ImageHelper($this->View, array(
			'imagePath' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS . 'img' . DS,
			'cachePath' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS . 'img' . DS . 'imagecache' . DS,
			'urlBase' => '',
		));
	}

/**
 * tearDown method
 * @return void
 */
	public function tearDown() {
		$path = $this->Image->settings['cachePath'];
		$dir = new Folder($path);
		foreach ($dir->find() as $file) {
			unlink($path.$file);
		}
		if (is_dir($path)) {
			rmdir($path);
		}
		unset($this->Image);
		parent::tearDown();
	}

/**
 * testMake
 */
	public function testMake() {
		// MAKE IMAGE
		$img = $this->Image->make('test.jpg', array(
			'w' => 100,
			'h' => 100,
			'zc' => 'T',
		));
		$this->assertTrue(file_exists($this->Image->settings['cachePath'] . $img));
		
		// USE CACHED IMAGE
		$newimg = $this->Image->make('test.jpg', array(
			'w' => 100,
			'h' => 100,
			'zc' => 'T',
		));
		$this->assertEquals($newimg, $img);
	}
}