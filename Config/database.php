<?php
class DATABASE_CONFIG {
/**
 * Enter the IP of your development server to detect and use dev database
 *
 * @var string
 */
	public $devServerAddress = '127.0.0.1';

/**
 * Production/Live Database Settings
 *
 * @var array
 */
	public $prd = array(
		'datasource' => '{{datasource}}',
		'persistent' => false,
		'host' => '{{host}}',
		'login' => '{{login}}',
		'password' => '{{password}}',
		'database' => '{{database}}',
		'prefix' => '{{prefix}}',
	);

/**
 * Development Database Settings
 *
 * @var array
 */
	public $dev = array(
		'datasource' => '{{datasource}}',
		'persistent' => false,
		'host' => '{{host}}',
		'login' => '{{login}}',
		'password' => '{{password}}',
		'database' => '{{database}}',
		'prefix' => '{{prefix}}',
	);

	public $default = array();

/**
 * Test Database Settings
 *
 * @var array
 */
	public $test = array(
		'datasource' => '{{datasource}}',
		'persistent' => false,
		'host' => '{{host}}',
		'login' => '{{login}}',
		'password' => '{{password}}',
		'database' => '{{database}}',
		'prefix' => 'test_{{prefix}}',
	);

/**
 * Check if a dev environment or production
 */
	public function __construct() {
		$server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $this->devServerAddress;
		if (php_sapi_name() == 'cli') {
			$server = $this->devServerAddress;
		}
		$this->default = ($server == $this->devServerAddress) ? $this->dev : $this->prd;
	}

/**
 * DATABASE_CONFIG
 */
	public function DATABASE_CONFIG() {
		$this->__construct();
	}
}