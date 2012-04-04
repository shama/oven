<?php
/**
 * Seo Helper
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2011 Kyle Robinson Young
 */
class SeoHelper extends AppHelper {

/**
 * helpers
 * 
 * @var array
 */
	public $helpers = array('Html');

/**
 * settings
 *
 * @var array
 */
	public $settings = array(
		'*' => array(
			'title' => '',
			'Keywords' => '',
			'Description' => '',
		),
	);

/**
 * meta
 *
 * @param string $name
 * @param string $content
 * @param string $page
 * @return boolean
 */
	public function meta($name = null, $content = null, $page = null) {
		if (!isset($page)) {
			$page = $this->_View->here;
		}
		$this->settings = Set::merge(
			$this->settings,
			array($page => array($name => $content))
		);
		return true;
	}

/**
 * title
 *
 * @param string $content
 * @param string $page
 * @return boolean
 */
	public function title($title = null, $page = null) {
		return $this->meta('title', $title, $page);
	}

/**
 * render
 *
 * @return string
 */
	public function render() {
		$out = '';
		$all = $this->settings['*'];
		$page = (!empty($this->settings[$this->_View->here])) ? $this->settings[$this->_View->here] : array();
		$meta = Set::merge($all, $page);
		foreach ($meta as $name => $content) {
			switch (strtolower($name)) {
				case 'title':
					$out .= $this->Html->tag('title', $content) . "\n";
					break;
				default:
					$out .= $this->Html->meta(compact('name', 'content')) . "\n";
					break;
			}
		}
		return $this->output($out);
	}
}