<?php
App::uses('AppHelper', 'View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('ImageHelper', 'Oven.View/Helper');

/**
 * Oven Html Helper
 * 
 * Override for Html Helper with
 *	public $helpers = array(
 *		'Html' => array('className' => 'Oven.OvenHtml'),
 *	);
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenHtmlHelper extends HtmlHelper {

/**
 * __construct
 * @param View $View
 * @param array $settings
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->Image = new ImageHelper($View, $settings);
	}

/**
 * image
 * With auto sizing/formatting built in.
 * @param string $path
 * @param array $ops
 * @return string
 */
	public function image($path, $opts = array()) {
		if (isset($opts['w']) || isset($opts['h'])) {
			$path = $this->Image->make($path, $opts);
			foreach ($this->Image->phpThumb as $key => $val) {
				unset($opts[$key]);
			}
		}
		$base = substr(Router::url('/'), 0, -1);
		if (!empty($base) && substr($path, 0, strlen($base)) == $base) {
			$path = substr($path, strlen($base));
		}
		return parent::image($path, $opts);
	}

}