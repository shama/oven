<?php
class TwitterHelper extends AppHelper {
	public $helpers = array('Html');
	
	/**
	 * format
	 * @param string $status
	 */
	function format($status=null) {
		// FIX LINKS
		preg_match_all("/http:\/\/([^\s]+)/i", $status, $out);
		if (!empty($out[0])) {
			foreach ($out[0] as $link) {
				$status = str_replace($link, $this->Html->link($link, $link), $status);
			}
		}
		// FIX # & @
		preg_match_all("/(#|@)([^\s]+)/i", $status, $out);
		if (!empty($out[0])) {
			foreach ($out[0] as $key => $val) {
				$type = $out[1][$key];
				switch ($type) {
					case '@':
						$status = str_replace($val, $this->Html->link($val, 'http://twitter.com/'.$out[2][$key]), $status);
						break;
					case '#':
						$status = str_replace($val, $this->Html->link($val, 'http://twitter.com/search?q=%23'.$out[2][$key]), $status);
						break;
					default:
						break;
				}
			}
		}
		return $status;
	}
}