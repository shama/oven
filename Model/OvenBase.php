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
 * name
 * @var string
 */
	public $name = 'OvenBase';

/**
 * actsAs
 * @var array
 */
	public $actsAs = array(
		'Oven.Upload',
		'Oven.Slug',
	);

}