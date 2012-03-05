<?php
App::uses('OvenTestCase', 'Oven.TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('OvenAppModel', 'Oven.Model');
App::uses('Init', 'Oven.Model');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Init Model Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class InitTest extends OvenTestCase {
/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->_setupPaths();
		$this->Init = new Init();
		$this->Init->appPath = App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS;
	}

/**
 * tearDown method
 * @return void
 */
	public function tearDown() {
		unset($this->Init);
		parent::tearDown();
	}

/**
 * testInitCore
 */
	public function testInitCore() {
		$path = $this->Init->appPath . 'Config' . DS . 'core.php';
		
		// SAVE ORIGINAL
		$file = new File($path);
		$orig = $file->read();
		
		$this->assertTrue($this->Init->initCore());
		$contents = $file->read();
		
		// CHANGED SALT
		$expected = "Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');";
		$this->assertTrue((strpos($contents, $expected) === false));
		
		// CHANGED CIPHER SEED
		$expected = "Configure::write('Security.cipherSeed', '76859309657453542496749683645');";
		$this->assertTrue((strpos($contents, $expected) === false));
		
		// CHANGED ADMIN ROUTING
		$expected = "//Configure::write('Routing.prefixes', array('admin'));";
		$this->assertTrue((strpos($contents, $expected) === false));
		
		// REVERT TO ORIGINAL
		$file->write($orig);
		$file->close();
	}

/**
 * testInitDatabase
 */
	public function testInitDatabase() {
		$this->skipIf(true, 'Db init deprecated.');
		
		$path = $this->Init->appPath . 'Config' . DS . 'database.php';
		$file = new File($path);
		
		$data = array(
			'production' => array(
				'login' => 'user-prod',
				'password' => 'password',
				'database' => 'database',
			),
			'development' => array(
				'login' => 'user-dev',
				'password' => 'password',
				'database' => 'database',
			),
		);
		
		$this->assertTrue($this->Init->initDatabase($data));
		$contents = $file->read();
		// TODO: Test the file contents are good
		$file->close();
		
		// REMOVE FILE
		unlink($path);
	}
}