<?php
App::uses('View', 'View');
App::uses('OvenHtmlHelper', 'Oven.View/Helper');
App::uses('Folder', 'Utility');

/**
 * Oven Html Helper Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenHtmlHelperTest extends CakeTestCase {
/**
 * setup
 */
	public function setUp() {
		parent::setUp();
		$controller = null;
		$this->View = new View($controller);
		$this->Html = new OvenHtmlHelper($this->View, array(
			'imagePath' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS . 'img' . DS,
			'cachePath' => App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS . 'img' . DS . 'imagecache' . DS,
		));
		$this->Html->request = new CakeRequest(null, false);
		$this->Html->request->webroot = '';
	}

/**
 * tearDown method
 * @return void
 */
	public function tearDown() {
		$path = $this->Html->Image->settings['cachePath'];
		$dir = new Folder($path);
		foreach ($dir->find() as $file) {
			unlink($path.$file);
		}
		if (is_dir($path)) {
			rmdir($path);
		}
		unset($this->Html);
		parent::tearDown();
	}

/**
 * testImage
 */
	public function testImage() {
		$result = $this->Html->image('test.jpg', array(
			'w' => 100,
			'h' => 100,
			'alt' => 'Test',
		));
		$this->assertTrue((strpos($result, 'src="/img/imagecache/') !== false));
		$this->assertTrue((strpos($result, 'alt="Test"') !== false));
	}
}