<?php
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'Oven.phpThumb', array('file' => 'phpThumb' . DS . 'phpthumb.class.php'));

/**
 * Image Helper
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2011 Kyle Robinson Young
 * 
 * Inspired by Daniel Salazar
 *	http://bakery.cakephp.org/articles/view/phpthumb-helper-2
 */
class ImageHelper extends AppHelper {

/**
 * phpThumb
 * 
 * @var Object
 */
	public $phpThumb = null;

/**
 * settings
 * 
 * @var array
 */
	public $settings = array(
		'imagePath' => null,
		'cachePath' => null,
		'urlBase' => null,
		'notFound' => null,
		'w' => 100,
		'h' => 100,
		'zc' => 'C',
	);

/**
 * Setup phpThumb and default settings
 * 
 * @param View $View
 * @param array $settings 
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->phpThumb = new phpThumb();
		$this->_default($settings);
	}

/**
 * Makes an image using phpThumb
 * 
 * @param string $src
 * @param array $opts
 * @return string
 */
	public function make($src = null, $opts = array()) {
		if (empty($src)) {
			return '';
		}
		$this->_default($opts);
		if ($src{0} !== '/') {
			$src = $this->settings['imagePath'] . $src;
		}
		if (!file_exists($src)) {
			$src = $this->settings['notFound'];
		}
		
		// MAKE SURE CACHE PATH EXISTS
		if (!file_exists($this->settings['cachePath'])) {
			if (is_writable(dirname($this->settings['cachePath']))) {
				mkdir($this->settings['cachePath'], 0755, true);
			} else {
				throw new Exception(__d('oven', 'ImageHelper: cachePath does not exist nor is writable.'));
				return '';
			}
		}
		
		// CHECK IF CACHED
		$cacheParts = array();
		$cacheThese = array('w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'fltr');
		foreach ($opts as $key => $val) {
			if (in_array($key, $cacheThese)) {
				$cacheParts[$key] = $val;
			}
		}
		$lastMod = filectime($src);
		$ext = strrchr($src, '.');
		$cache = md5(implode('', $cacheParts) . $lastMod) . $ext;
		if (file_exists($this->settings['cachePath'] . $cache)) {
			return $this->settings['urlBase'] . $cache;
		}
		
		// MAKE IMAGE
		$opts['src'] = $src;
		foreach($this->phpThumb as $key => $val) {
			if (isset($opts[$key])) {
				$this->phpThumb->setParameter($key, $opts[$key]);
			}
		}
		if ($this->phpThumb->GenerateThumbnail()) {
			$this->phpThumb->RenderToFile($this->settings['cachePath'] . $cache);
		} else {
			throw new Exception(ereg_replace("[^A-Za-z0-9\/: .]", "", $this->phpThumb->fatalerror));
			return '';
		}
		
		return $this->settings['urlBase'] . $cache;
	}

/**
 * Defaults settings
 * 
 * @param array $settings
 * @return boolean
 */
	protected function _default($settings = array()) {
		$this->settings = Set::merge($this->settings, $settings);
		if (is_null($this->settings['imagePath'])) {
			$this->settings['imagePath'] = APP . 'webroot' . DS . 'img' . DS;
		}
		if (is_null($this->settings['cachePath'])) {
			$this->settings['cachePath'] = APP . 'webroot' . DS . 'img' . DS . 'imagecache' . DS;
		}
		if (is_null($this->settings['urlBase'])) {
			$this->settings['urlBase'] = Router::url('/img/imagecache/');
		}
		if (is_null($this->settings['notFound'])) {
			$this->settings['notFound'] = APP . 'webroot' . DS . 'img' . DS . 'not_found.jpg';
		}
		return true;
	}
}
