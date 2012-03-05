<?php
App::uses('OvenTestCase', 'Oven.TestSuite');
App::uses('OvenConfig', 'Oven.Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * OvenConfig Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenConfigTest extends OvenTestCase {
/**
 * Path to config folder
 * @var string
 */
	public $path = null;

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->path = App::pluginPath('Oven') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		if (file_exists($this->path . 'oven.php')) {
			unlink($this->path . 'oven.php');
		}
		if (file_exists($this->path . 'oven.json')) {
			unlink($this->path . 'oven.json');
		}
	}

/**
 * testConfig
 */
	public function testConfig() {
		// MANUALLY SET CONFIG
		$config = array(
			'recipe' => array(
				'pages' => array(),
			),
		);
		new OvenConfig($config);
		$expected = array(
			'recipe' => array(
				'pages' => array(
					'allowDelete' => true,
					'showLanguages' => true,
					'hasChildren' => false,
					'controller' => array(),
					'model' => array(),
					'view' => array(),
				),
			),
			'config' => array(),
			'oven' => array(
				'clean_tables' => false,
			),
		);
		$this->assertEquals($expected, Configure::read('Oven'));
		Configure::delete('Oven');
		
		// READ PHP CONFIG
		$file = new File($this->path . 'oven.php', true);
		$contents = <<<END
<?php
\$config = array(
	'Oven' => array(
		'recipe' => array(
			'pages' => array(
				'schema' => array(
					'title' => array(),
					'body' => array('type' => 'textarea'),
				),
			),
		),
	),
);
END;
		$file->write($contents);
		new OvenConfig(null, $this->path);
		$this->assertEquals('text', Configure::read('Oven.recipe.pages.schema.title.type'));
		$this->assertEquals('textarea', Configure::read('Oven.recipe.pages.schema.body.type'));
		Configure::delete('Oven');
		$file->delete();
		$file->close();
		
		// READ JSON CONFIG
		$file = new File($this->path . 'oven.json', true);
		$contents = <<<END
{
	"recipe": {
		"pages": {
			"schema": {
				"title": {
				},
				"body": {
					"type": "html"
				}
			}
		}
	}
}
END;
		$file->write($contents);
		new OvenConfig(null, $this->path);
		$this->assertEquals('text', Configure::read('Oven.recipe.pages.schema.title.type'));
		$this->assertEquals('html', Configure::read('Oven.recipe.pages.schema.body.type'));
		Configure::delete('Oven');
		$file->delete();
		$file->close();
	}
}