<?php
//App::uses('AppHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

/**
 * Oven Form Helper
 * 
 * Override for Form Helper with
 *	public $helpers = array(
 *		'Form' => array('className' => 'Oven.OvenForm'),
 *	);
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenFormHelper extends FormHelper {
/**
 * _View
 * @var View
 */
	protected $_View = null;

/**
 * __construct
 * @param View $View
 * @param array $settings 
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->_View = $View;
	}

/**
 * input
 * @param string $fieldName
 * @param array $opts 
 */
	public function input($fieldName, $opts = array()) {
		$opts = Set::merge(array(
			'div' => array('class' => 'clearfix'),
			'class' => 'span12',
		), $opts);
		if (!empty($opts['help'])) {
			$opts['data-content'] = $opts['help'];
			$opts['data-original-title'] = 'Help For '.Inflector::humanize($fieldName);
			unset($opts['help']);
		}
		if (!empty($opts['type'])) {
			switch ($opts['type']) {
				case 'ckeditor':
					$opts['type'] = 'textarea';
					$this->_View->loadHelper('Oven.Ckeditor');
					echo $this->_View->Ckeditor->replace($this->domId($fieldName), array(
						'path' => '/oven/js/',
					));
				break;
				case 'boolean':
					$opts['type'] = 'radio';
					$opts['multiple'] = true;
					$opts['options'] = array(
						1 => 'Yes',
						0 => 'No',
					);
				break;
				case 'slug':
					// TODO: ADD JS TO AUTO-FILL FROM DISPLAY FIELD
					$opts['type'] = 'text';
				break;
			}
		}
		return parent::input($fieldName, $opts);
	}

/**
 * radio
 * @param string $fieldName
 * @param array $options
 * @param array $attributes
 * @return string
 */
	public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);
		$legend = false;
		$disabled = array();

		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		} elseif (count($options) > 1) {
			$legend = __(Inflector::humanize($this->field()));
		}
		$label = true;

		if (isset($attributes['label'])) {
			$label = $attributes['label'];
			unset($attributes['label']);
		}
		$inbetween = null;

		if (isset($attributes['separator'])) {
			$inbetween = $attributes['separator'];
			unset($attributes['separator']);
		}

		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		} else {
			$value =  $this->value($fieldName);
		}

		if (isset($attributes['disabled'])) {
			$disabled = $attributes['disabled'];
		}

		$out = array();
		// TODO: Add field id to label
		$out[] = '<label>'.$lengend.'</label>';;
		$out[] = '<div class="input">';
		$out[] = '<ul class="input-list">';

		$hiddenField = isset($attributes['hiddenField']) ? $attributes['hiddenField'] : true;
		unset($attributes['hiddenField']);

		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue);

			if (isset($value) && $optValue == $value) {
				$optionsHere['checked'] = 'checked';
			}
			if (!empty($disabled) && in_array($optValue, $disabled)) {
				$optionsHere['disabled'] = true;
			}
			$tagName = Inflector::camelize(
				$attributes['id'] . '_' . Inflector::slug($optValue)
			);

			// TODO: WRAP LABELS IN SPAN TAGS
			if ($label) {
				$optTitle = $this->Html->useTag('span', $tagName, '', $optTitle);
			}
			$allOptions = array_merge($attributes, $optionsHere);
			$out[] = '<li><label>';
			$out[] = $this->Html->useTag('radio', $attributes['name'], $tagName,
				array_diff_key($allOptions, array('name' => '', 'type' => '', 'id' => '', 'class' => '')),
				$optTitle
			);
			$out[] = '</label></li>';
		}
		$hidden = null;

		if ($hiddenField) {
			if (!isset($value) || $value === '') {
				$hidden = $this->hidden($fieldName, array(
					'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
				));
			}
		}
		
		$out[] = '</ul>';
		$out[] = '</div>';
		
		$out = $hidden . implode($inbetween, $out);
		return $out;
	}
}