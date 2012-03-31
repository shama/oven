<?php
/**
 * OvenConfig
 * 
 * @package oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenConfig {

/**
 * Oven Version
 *
 * @var string
 */
	public $version = '0.0.1';

/**
 * config
 * Holds loaded configuration
 * @var array
 */
	public $config = array();

/**
 * Path to used config file
 *
 * @var string
 */
	public $path = null;

/**
 * defaults
 * @var array
 */
	public $defaults = array(
		'config' => array(),
		'recipe' => array(
			'allowDelete' => true,
			'showLanguages' => true,
			'hasChildren' => false,
			'controller' => array(),
			'model' => array(),
			'view' => array(),
		),
		'field' => array(
			'type' => 'text',
			'help' => '',
		),
		'oven' => array(
			'clean_tables' => false,
		),
	);

/**
 * __construct
 * @param array $config
 * @param string $path
 */
	public function __construct($config = array(), $path = null) {
		if (!$path) {
			$path = APP . 'Config' . DS;
		}
		Configure::delete('Oven');
		if (empty($config)) {
			$try = array(
				$path . 'oven.json',
				$path . 'oven.php',
			);
			foreach ($try as $cfg) {
				if (file_exists($cfg)) {
					$this->path = $cfg;
					$ext = strrchr($cfg, '.');
					switch ($ext) {
						case '.json':
							App::uses('File', 'Utility');
							$cfg = new File($cfg);
							$this->config = json_decode($cfg->read(), true);
						break;
						case '.yaml':
							App::import('Vendor', 'Oven.spyc/spyc');
							$this->config = Spyc::YAMLLoad($cfg);
						break;
						default:
							App::uses('PhpReader', 'Configure');
							Configure::config('oven', new PhpReader(dirname($cfg) . DS));
							Configure::load('oven', 'oven');
							$this->config = Configure::read('Oven');
						break;
					}
					break;
				}
			}
			// IF NO CONFIG COPY OVER SAMPLE
			if (empty($this->config)) {
				if (copy(
						CakePlugin::path('Oven') . 'Config' . DS . 'config.json',
						$path . 'oven.json'
					)) {
					$this->__construct();
				}
			}
		} else {
			$this->config = $config;
		}
		$this->config = $this->_default($this->config);
		Configure::write('Oven', $this->config);
	}

/**
 * _default
 * Defaults config options
 * 
 * @param array $config
 * @return array
 */
	protected function _default($config = array()) {
		$config = Set::merge(array(
			'recipe' => array(),
			'config' => $this->defaults['config'],
			'oven' => $this->defaults['oven'],
		), $config);
		foreach ($config['recipe'] as $key => $val) {
			$config['recipe'][$key] = Set::merge($this->defaults['recipe'], $val);
			if (!empty($val['schema'])) {
				foreach ($val['schema'] as $k => $val) {
					$config['recipe'][$key]['schema'][$k] = Set::merge($this->defaults['field'], $val);
				}
			}
		}
		return $config;
	}

}