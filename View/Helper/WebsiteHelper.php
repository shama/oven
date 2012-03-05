<?php
/**
 * Website Helper
 * A generic helper for printing stuff on websites.
 *
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2011 Kyle Robinson Young
 */
class WebsiteHelper extends AppHelper {

/**
 * groupByField
 * @param string $field
 * @param array $data
 * @return array
 */
	function groupByField($field=null, $data=null) {
		$model = key(current($data));
		$out = array();
		foreach ($data as $item) {
			$out[$item[$model][$field]][] = $item;
		}
		return $this->output($out);
	}
}