<?php
App::uses('OvenAppModel', 'Oven.Model');

/**
 * OvenBase Model
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenBase extends OvenAppModel {

/**
 * actsAs
 * @var array
 */
	public $actsAs = array(
		'Oven.Upload',
		'Oven.Slug',
	);

}