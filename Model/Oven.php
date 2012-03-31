<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('SchemaBaker', 'Oven.Lib');
App::uses('PhpBaker', 'Oven.Lib');

/**
 * Oven Model
 * Converts an Oven recipe into tables and files
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class Oven extends OvenAppModel {

/**
 * name
 *
 * @var string
 */
	public $name = 'Oven';

/**
 * useTable
 *
 * @var boolean
 */
	public $useTable = false;

/**
 * List of models
 *
 * @var array
 */
	public $models = array();

/**
 * List of controllers
 *
 * @var array
 */
	public $controllers = array();

/**
 * _SchemaBaker
 *
 * @var SchemaBaker
 */
	public $SchemaBaker = null;

/**
 * Default values in config array
 *
 * @var array
 */
	protected $_defaultConfig = array(
		'recipe' => array(),
		'config' => array(),
		'oven' => array(),
	);

/**
 * __construct
 *
 * @param type $id
 * @param type $table
 * @param type $ds 
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->SchemaBaker = new SchemaBaker($this->useDbConfig);
	}

/**
 * Run all bakes
 *
 * @param array $config 
 * @return boolean
 */
	public function bake($config=array()) {
		if (empty($config['recipe'])) {
			return false;
		}
		$this->bakeTables($config);
		$this->bakeModels($config);
		$this->bakeControllers($config);
		return true;
	}

/**
 * Bake/alter tables
 *
 * @param array $config
 * @return boolean
 * 
 * TODO: Check field types to alter existing fields
 */
	public function bakeTables($config = array()) {
		return $this->SchemaBaker->bake($config);
	}

/**
 * Turn config into bake array for Model
 *
 * @param array $config
 * @return boolean
 */
	public function bakeModels($config = array()) {
		$config = Set::merge($this->_defaultConfig, $config);
		if (empty($config['recipe'])) {
			return false;
		}
		foreach ($config['recipe'] as $name => $node) {
			$className = Inflector::classify($name);
			$props = array(
				'name' => array(
					'doc' => "/**\n * name\n *\n * @var string\n */",
					'value' => $className,
					'access' => 'public',
				),
			);
			if (!empty($node['model'])) {
				foreach ($node['model'] as $key => $val) {
					$props[$key] = array(
						'value' => $val,
					);
				}
			}
			// DEFAULT CODE HEADER DOC
			if (empty($config['config']['code_header']['doc'])) {
				$config['config']['code_header']['doc'] = $className . ' Model';
			}
			$bake = array(
				'class' => array(
					'class' => 'class ' . $className . ' extends OvenBase',
					'doc' => $this->_buildDocHeader($config),
					'uses' => array(
						"App::uses('OvenBase', 'Oven.Model');",
					),
				),
				'properties' => $props,
				'methods' => array(),
			);
			$PhpBaker = new PhpBaker($className, 'Model');
			$PhpBaker->write($bake);
		}
		$this->_getModels();
		return true;
	}

/**
 * Turn config into bake array for Controllers
 *
 * @param array $config
 * @return boolean
 */
	public function bakeControllers($config = array()) {
		$config = Set::merge($this->_defaultConfig, $config);
		if (empty($config['recipe'])) {
			return false;
		}
		foreach ($config['recipe'] as $name => $node) {
			$className = Inflector::camelize($name);
			$class = $className . 'Controller';
			$props = array(
				'name' => array(
					'doc' => "/**\n * name\n *\n * @var string\n */",
					'value' => $className,
					'access' => 'public',
				),
			);
			if (!empty($node['controller'])) {
				foreach ($node['controller'] as $key => $val) {
					$props[$key] = array(
						'value' => $val,
					);
				}
			}
			$bake = array(
				'class' => array(
					'class' => 'class ' . $class . ' extends BasesController',
					'doc' => "/**\n * $className Controller\n *\n */",
					'uses' => array(
						"App::uses('BasesController', 'Oven.Controller');",
					),
				),
				'properties' => $props,
				'methods' => array(),
			);
			$PhpBaker = new PhpBaker($class, 'Controller');
			$PhpBaker->write($bake);
		}
		$this->_getControllers();
		return true;
	}

/**
 * Get models
 *
 * @return array
 */
	protected function _getModels() {
		$this->models = array();
		$dir = new Folder(current(App::path('Model')));
		$files = $dir->find('.+\.php');
		foreach ($files as $file) {
			$class = Inflector::classify(str_replace('.php', '', $file));
			$this->models[$file] = $class;
		}
		return $this->models;
	}

/**
 * Get controllers
 *
 * @return array
 */
	protected function _getControllers() {
		$this->controllers = array();
		$dir = new Folder(current(App::path('Controller')));
		$files = $dir->find('.+\.php');
		foreach ($files as $file) {
			$class = Inflector::classify(str_replace('.php', '', $file));
			$this->controllers[$file] = $class;
		}
		return $this->controllers;
	}

/**
 * Constructs a doc header from config
 *
 * @param array $config
 * @return string
 */
	protected function _buildDocHeader($config = array()) {
		$header = Set::merge(array(
			'doc' => '',
			'author' => '',
		), (array)$config['config']['code_header']);
		$doc = "/**\n";
		$doc .= " * " . str_replace("\n", "\n * ", $header['doc']) . "\n";
		$doc .= " *\n";
		unset($header['doc']);
		foreach ($header as $key => $val) {
			$doc .= " * @$key $val\n";
		}
		$doc .= " */";
		return $doc;
	}

}