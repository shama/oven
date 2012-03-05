<?php
App::uses('OvenConfig', 'Oven.Lib');

/**
 * Oven App Model
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenAppModel extends AppModel {
/**
 * actsAs
 * @var array
 */
	public $actsAs = array(
		'Containable',
		//'Translate',
		'Oven.Upload',
		'Oven.Slug',
	);

/**
 * __construct
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		new OvenConfig();
	}
}