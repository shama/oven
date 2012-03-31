<?php
/**
 * Slug Behavior
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class SlugBehavior extends ModelBehavior {

/**
 * settings
 * @var array
 */
	public $settings = array(
		'slugFromField' => '',
		'slugFields' => array(),
	);

/**
 * setup
 *
 * @param object $model
 * @param array $settings
 * @return void
 */
	public function setup(&$model, $settings = array()) {
		$recipe = Configure::read('Oven.recipe');
		if (!empty($recipe)) {
			foreach ($recipe as $key => $type) {
				$modelName = Inflector::classify($key);
				if (!isset($this->settings['slugFields'][$modelName])) {
					$this->settings['slugFields'][$modelName] = array();
				}
				if (!empty($type['schema'])) {
					foreach ($type['schema'] as $field => $val) {
						if (empty($val['type'])) {
							continue;
						}
						if ($val['type'] == 'slug') {
							$this->settings['slugFields'][$modelName][] = $field;
						}
					}
				}
			}
		}
		$this->settings['slugFromField'] = $model->displayField;
		$this->settings = Set::merge($this->settings, $settings);
	}

/**
 * beforeSave
 * @param Object $model
 * @return boolean
 */
	public function beforeSave(&$model) {
		$fields = !empty($this->settings['slugFields'][$model->alias]) ? $this->settings['slugFields'][$model->alias] : array();
		foreach ($model->data[$model->alias] as $key => $val) {
			if (in_array($key, $fields) && empty($val)) {
				$model->data[$model->alias][$key] = $this->_slugify($model->data[$model->alias][$this->settings['slugFromField']]);
			}
		}
		return true;
	}

/**
 * _slugify
 * @param string $str 
 */
	protected function _slugify($str = null) {
		return str_replace("_", "-", strtolower(Inflector::slug($str)));
	}
}