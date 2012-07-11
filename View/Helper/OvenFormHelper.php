<?php
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
 * inputs
 *
 * @param mixed $fields
 * @param mixed $blacklist
 */
	public function inputs($fields = null, $blacklist = null) {
		$legend = $fieldsetClass = $out = '';
		if (isset($fields['legend'])) {
			$legend = $fields['legend'];
			unset($fields['legend']);
		}
		if (isset($fields['fieldset'])) {
			$fieldsetClass = $fields['fieldset'];
			unset($fields['fieldset']);
		}
		foreach ($fields as $field => $opts) {
			if (is_int($field)) {
				$field = $opts;
			}
			if (!is_array($opts)) {
				$opts = array();
			}
			$out .= $this->input($field, $opts);
		}
		return $this->Html->useTag('fieldset', $fieldsetClass, $this->Html->useTag('legend', $legend) . $out);
	}

/**
 * input
 *
 * @param string $fieldName
 * @param array $opts
 * 
 * @todo Checkboxes break with this
 */
	public function input($fieldName, $opts = array()) {
		$opts = Set::merge(array(
			'div' => array('class' => 'control-group'),
			'between' => '<div class="controls">',
			'after' => '</div>',
			'class' => 'input-xlarge',
			'label' => array('class' => 'control-label'),
		), $opts);
		if (!empty($opts['help'])) {
			$opts['after'] = '<p class="help-block">' . $opts['help'] . '</p>' . $opts['after'];
			unset($opts['help']);
		}
		if (is_string($opts['label'])) {
			$opts['label'] = array(
				'text' => $opts['label'],
				'class' => 'control-label',
			);
		}
		if (!empty($opts['type'])) {
			switch ($opts['type']) {
				case 'ckeditor':
					$opts['type'] = 'textarea';
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
 * 
 * @todo Fix to work with Bootstrap 2
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