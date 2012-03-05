<?php
App::uses('DboSource', 'Model/Datasource');

/**
 * SchemaBaker
 * Used for building database tables
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class SchemaBaker {
/**
 * defaults
 * 
 * @var array
 */
	public $defaults = array(
		'id' => array(
			'type' => 'primary',
		),
		'created' => array(
			'type' => 'datetime',
		),
		'modified' => array(
			'type' => 'datetime',
		),
	);

/**
 * tables
 *
 * @var array
 */
	public $tables = array();

/**
 * tablePrefix
 *
 * @var string
 */
	public $tablePrefix = '';

/**
 * _db
 *
 * @var Datasource
 */
	protected $_db = null;

/**
 * _construct
 */
	public function __construct($dbConfig = 'default') {
		$this->_db = ConnectionManager::getDataSource($dbConfig);
		$this->_getTables();
	}

/**
 * bake
 *
 * @param array $config
 */
	public function bake($config = array()) {
		if (empty($config['recipe'])) {
			return false;
		}
		$this->_getTables();
		foreach ($config['recipe'] as $table => $node) {
			$table = $this->tablePrefix . $table;
			if (empty($node['schema'])) {
				continue;
			}
			$node['schema'] = Set::merge($this->defaults, $node['schema']);
			try {
				$res = $this->_db->query('DESCRIBE ' . $table);
				
				// ALTER TABLE
				$exists = array();
				foreach ($res as $row) {
					$exists[] = $row['COLUMNS']['Field'];
				}
				$keys = array_keys($node['schema']);
				$remove = array_diff($exists, $keys);
				$add = array_diff($keys, $exists);
				foreach ($add as $val) {
					$type = $this->_mapType($val, $node['schema'][$val]);
					$sql = 'ALTER TABLE `' . $table . '` ADD ' . $type . ';';
					$this->_db->query($sql);
				}
				$clean_tables = !empty($config['config']['clean_tables']) ? true : false;
				if ($clean_tables) {
					foreach ($remove as $val) {
						$sql = 'ALTER TABLE `' . $table . '` DROP `' . $val . '`;';
						$this->_db->query($sql);
					}
				}
				
			} catch (PDOException $e) {
				
				// CREATE TABLES
				$sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (';
				foreach ($node['schema'] as $key => $arr) {
					$arr['type'] = !empty($arr['type']) ? $arr['type'] : 'string';
					$sql .= $this->_mapType($key, $arr['type']) . ' , ';
				}
				$sql = substr($sql, 0, -3);
				$sql .= ');';
				$this->_db->query($sql);
				
			}
		}
		$this->_getTables();
		return true;
	}

/**
 * _mapType
 * Maps field types to DB field type
 *
 * @param string $key
 * @param string $type 
 * @return boolean
 */
	protected function _mapType($key=null, $type=null) {
		$null = 'NULL';
		switch ($key) {
			case 'primary':
				$null = 'NOT NULL';
			break;
		}
		switch ($type) {
			case 'primary':
				return '`'.$key.'` INT( 11 ) '.$null.' AUTO_INCREMENT PRIMARY KEY';
			case 'datetime':
				return '`'.$key.'` DATETIME '.$null;
			case 'text':
			case 'ckeditor':
			case 'wysihat':
				return '`'.$key.'` TEXT '.$null;
			case 'boolean':
				return '`'.$key.'` TINYINT( 1 ) '.$null;
			case 'price':
				return '`'.$key.'` DECIMAL( 10,2 ) '.$null;
			default:
				return $key.' VARCHAR( 255 ) '.$null;
		}
	}

/**
 * _getTables
 * @return array
 */
	protected function _getTables() {
		$this->tables = array();
		$tables = Set::extract('/TABLE_NAMES/.', $this->_db->query('SHOW TABLES'));
		foreach ($tables as $table) {
			$this->tables = array_merge($this->tables, array_values($table));
		}
		return $this->tables;
	}
}