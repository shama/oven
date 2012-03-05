<?php
App::uses('Folder', 'Utility');

/**
 * Upload Behavior
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class UploadBehavior extends ModelBehavior {
/**
 * settings
 * @var array
 */
	public $settings = array(
		'uploadFields' => array(),
		'uploadLocation' => '',
	);

/**
 * _uploadLater
 * Holds fields to upload later
 * @var array
 */
	protected $_uploadLater = array();

/**
 * Flag if we are saving
 *
 * @var boolean
 */
	protected $_saving = false;

/**
 * setup
 *
 * @param Model $Model
 * @param array $settings
 * @return void
 */
	public function setup($Model, $settings = array()) {
		$recipe = Configure::read('Oven.recipe');
		if (!empty($recipe)) {
			foreach ($recipe as $key => $type) {
				$modelName = Inflector::classify($key);
				if (!isset($this->settings['uploadFields'][$modelName])) {
					$this->settings['uploadFields'][$modelName] = array();
				}
				if (!empty($type['schema'])) {
					foreach ($type['schema'] as $field => $val) {
						if (empty($val['type'])) {
							continue;
						}
						if ($val['type'] == 'file') {
							$this->settings['uploadFields'][$modelName][] = $field;
						}
					}
				}
			}
		}
		if (empty($settings['uploadLocation'])) {
			$settings['uploadLocation'] = Configure::read('Oven.config.upload_location');
			if (empty($settings['uploadLocation'])) {
				$settings['uploadLocation'] = WWW_ROOT . 'files' . DS . 'uploads' . DS;
			}
		}
		if (!file_exists($settings['uploadLocation'])) {
			new Folder($settings['uploadLocation'], true);
		}
		$this->settings = Set::merge($this->settings, $settings);
	}

/**
 * Save file types for upload afterSave
 *
 * @param Model $Model
 * @return boolean
 */
	public function beforeSave($Model) {
		if ($this->_saving) {
			return true;
		}
		$this->_uploadLater = array();
		$fields = !empty($this->settings['uploadFields'][$Model->alias]) ? $this->settings['uploadFields'][$Model->alias] : array();
		foreach ($Model->data[$Model->alias] as $key => $val) {
			if (in_array($key, $fields) && is_array($val)) {
				$this->_uploadLater[$Model->alias][$key] = $val;
				//$Model->data[$Model->alias][$key] = '';
				unset($Model->data[$Model->alias][$key]);
			}
		}
		return true;
	}

/**
 * Save each file to folder and update db
 *
 * @param Model $Model
 * @param boolean $created
 */
	public function afterSave($Model, $created) {
		if ($this->_saving) {
			return true;
		}
		$this->_saving = true;
		if (!empty($this->_uploadLater)) {
			foreach ($this->_uploadLater as $key => $fields) {
				foreach ($fields as $name => $file) {
					if (!is_uploaded_file($file['tmp_name'])) {
						//continue;
					}
					$ext = strtolower(strrchr($file['name'], '.'));
					$filename = md5($file['name'] . $file['size']) . $ext;
					$dest = $this->settings['uploadLocation'] . Inflector::tableize($Model->alias) . DS;
					if (!file_exists($dest)) {
						new Folder($dest, true);
					}
					move_uploaded_file($file['tmp_name'], $dest . $filename);
					$Model->saveField($name, $filename);
				}
			}
			$this->_uploadLater = array();
		}
		$this->_saving = false;
		$Model->data = $Model->findById($Model->id);
		return true;
	}
}