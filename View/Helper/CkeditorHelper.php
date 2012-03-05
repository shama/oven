<?php
/**
 * Ckeditor Helper
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class CkeditorHelper extends AppHelper {
/**
 * helpers
 * @var array
 */
	public $helpers = array('Html');

/**
 * REPLACE
 * @param str $id
 * @param array $options
 * @return str
 */
	function replace($id = null, $options = array()) {
		$options = Set::merge(array(
			'path' => '',
		), $options);
		echo $this->Html->script(array(
			$options['path'] . 'ckeditor/ckeditor',
			$options['path'] . 'ckfinder/ckfinder',
		), array('inline' => false, 'once' => true));
		if (!empty($id)) {
			if (isset($options)) {
				$options = json_encode($options);
				$code = "var ckeditor_$id = CKEDITOR.replace('$id', $options); \n";
			} else {
				$code = "var ckeditor_$id = CKEDITOR.replace('$id'); \n";
			}
			$code .= "CKFinder.SetupCKEditor( ckeditor_$id, '" . $this->Html->url('/js/ckfinder/') . "' ); \n";
			return $this->output($this->Html->scriptBlock($code, array('inline' => false)));
		} else {
			return $this->output($this->Html->scriptBlock("CKEDITOR.replaceAll();", array('inline' => false)));
		}
	}

/**
 * REPLACE ALL
 */
	function replaceAll() {
		return $this->output($this->replace());
	}
}