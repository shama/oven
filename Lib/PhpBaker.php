<?php
App::uses('File', 'Utility');

/**
 * PhpBaker
 * For reading/writing/merging CakePHP classes
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 * 
 * @todo Use CakePHP core bake for merge-able default templates
 */
class PhpBaker {

/**
 * Our class name
 *
 * @var string
 */
	public $class = '';

/**
 * Our path name
 *
 * @var string
 */
	public $path = '';

/**
 * Last data obtained
 *
 * @var array
 */
	public $data = array();

/**
 * Our temp clones files to be deleted
 *
 * @var array
 */
	protected $_Clones = array();

/**
 * Holds an instance of our Reflection
 *
 * @var ReflectionClass
 */
	protected $_Reflection = null;

/**
 * Holds an instance of our Class
 *
 * @var Class
 */
	protected $_Class = null;

/**
 * Holds an instance of our file
 *
 * @var File
 */
	protected $_File = null;

/**
 * __construct
 */
	public function __construct($class = '', $path = '') {
		if (!empty($class) && !empty($path)) {
			$this->read($class, $path);
		}
	}

/**
 * __destruct
 * Clean up temp files and unset things
 */
	public function __destruct() {
		foreach ($this->_Clones as $file) {
			unlink($file);
		}
		if (isset($this->_File)) {
			$this->_File->close();
			unset($this->_File);
		}
		unset($this->_Reflection);
		unset($this->_Class);
	}

/**
 * If file doesn't exist; create it, use it then read it
 *
 * @param string $class Name of class to create or use
 * @param string $path Path to class, use App::uses() style
 * @return array
 * @todo ignore core paths (for PagesController)
 * @throws CakeException
 */
	public function read($class = '', $path = '') {
		if (!empty($class)) {
			$this->class = $class;
		}
		if (!empty($path)) {
			$this->path = $path;
		}
		if ($this->path == 'View') {
			throw new CakeException(__d('Oven', 'Oven view baking not supported.'));
		}

		// GET FILENAME
		$paths = App::path($this->path);
		$filename = $this->class . '.php';
		$create = true;
		foreach ($paths as $p) {
			if (file_exists($p . $filename)) {
				$create = false;
				break;
			}
		}

		// CREATE OR USE ACTUAL FILE
		unset($this->_File);
		if ($create) {
			$this->_File = new File(current($paths) . $filename, true);
			$this->_File->write("<?php\nclass $this->class {\n\n}\n");
		} else {
			$this->_File = new File(current($paths) . $filename);
		}

		// CREATE CLONE
		$cloneClass = $this->_createClone();
		App::uses($cloneClass, $this->path);
		if ($this->path == 'Model') {
			$this->_Class = new $cloneClass(array(
				'table' => Inflector::tableize($this->class),
			));
		} else {
			$this->_Class = new $cloneClass();
		}
		$this->_Reflection = new ReflectionClass($cloneClass);

		// RETURN INFO ABOUT CLASS
		return $this->data = array(
			'class' => $this->_getClass(),
			'properties' => $this->_getProperties(),
			'methods' => $this->_getMethods(),
		);
	}

/**
 * Write a file from an array
 *
 * @param array $data A PhpBaker style array
 * @return boolean
 */
	public function write($data = array()) {
		$data = Set::merge($this->data, $data);
		$data['class'] = Set::merge(array(
			'doc' => "/**\n * " . $this->class . "\n * \n */",
			'class' => '',
			'uses' => '',
		), $data['class']);
		$code = '<?php' . "\n";
		if (!empty($data['class']['uses'])) {
			$uses = array_unique($data['class']['uses']);
			$code .= implode("\n", $uses) . "\n\n";
		}
		$code .= $data['class']['doc'] . "\n";
		$code .= $data['class']['class'] . " {\n";
		foreach ($data['properties'] as $prop => $arr) {
			$type = is_array($arr['value']) ? 'array' : 'string';
			$arr = Set::merge(array(
				'doc' => "/**\n * $prop\n * \n * @var $type\n */",
				'value' => '',
				'access' => 'public',
			), $arr);
			$code .= $arr['doc'] . "\n";
			if (is_array($arr['value'])) {
				$val = $this->arrayToCode($arr['value'], 2);
			} else {
				$val = "'" . $arr['value'] . "'";
			}
			$code .= "\t" . $arr['access'] . " \$$prop = $val;\n\n";
		}
		foreach ($data['methods'] as $meth => $arr) {
			$arr = Set::merge(array(
				'doc' => "/**\n * $meth\n * \n */",
				'value' => "\tpublic function $meth() {\n\t\t\n\t}",
			), $arr);
			$code .= $arr['doc'] . "\n";
			$code .= $arr['value'] . "\n";
		}
		$code .= '}';
		file_put_contents($this->_File->pwd(), $code);
		return true;
	}

/**
 * Merge two classes together
 *
 * @param array $one Class/Path to merge from.
 * @param array $two Class/Path to merge to. If empty will use previously loaded.
 * @return boolean
 */
	public function merge($one = array(), $two = array()) {
		if (empty($two)) {
			$two = array($this->class, $this->path);
		}
		if (count($one) != 2 || count($two) != 2) {
			return false;
		}
		$merge1 = $this->read($one[0], $one[1]);
		$merge2 = $this->read($two[0], $two[1]);
		unset($merge1[$one[0]]);
		$data = Set::merge($merge1, $merge2);
		$data['class']['uses'] = array_unique($data['class']['uses']);
		$this->data = array();
		return $this->write($data);
	}

/**
 * Turns an array into php code
 *
 * @param array $arr Array of code
 * @param integer $deep How many tabs to indent
 * @return string
 */
	public function arrayToCode($arr = array(), $deep = 1) {
		if ($deep > 1) {
			$out = "array(\n";
		} else {
			$out = "array(";
		}
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				if (is_array($v)) {
					$out .= str_repeat("\t", $deep) . "'$k' => ";
					$out .= $this->arrayToCode($v, ++$deep);
					$out .= ",\n";
				} else {
					if (is_string($k)) {
						$out .= "\n" . str_repeat("\t", $deep) . "'$k' => '$v',\n";
					} else {
						$out .= str_repeat("\t", $deep) . "'$v',\n";
					}
				}
			}
			if (!empty($arr[0])) {
				$out .= "\t";
			}
		}
		$out .= str_repeat("\t", $deep - 2) . ")";
		return $out;
	}

/**
 * Return an array of info about the class
 * 
 * @return array
 */
	protected function _getClass() {
		$class = '';
		if ($this->_Reflection->isFinal()) {
			$class .= 'final ';
		}
		if ($this->_Reflection->isAbstract() && !$this->_Reflection->isInterface()) {
			$class .= 'abstract ';
		}
		if ($this->_Reflection->isInterface()) {
			$class .= 'interface ';
		} else {
			$class .= 'class ';
		}
		$class .= $this->class . ' ';
		if ($this->_Reflection->getParentClass()) {
			$class .= 'extends ' . $this->_Reflection->getParentClass()->getName();
		}
		$interfaces = $this->_Reflection->getInterfaces();
		$number = count($interfaces);
		if ($number > 0) {
			$implements = '';
			foreach ($interfaces as $int) {
				$intName = $int->getName();
				if ($intName == 'CakeEventListener') {
					continue;
				}
				$implements .= $int->getName() . ' ';
			}
			if (!empty($implements)) {
				$class .= ' implements ' . $implements;
			}
		}
		$file = $this->_File->read();
		preg_match_all('/App::(uses|import)\(.*\);/i', $file, $out);
		$uses = !empty($out[0]) ? $out[0] : array();
		return array(
			'class' => $class,
			'doc' => $this->_Reflection->getDocComment(),
			'uses' => $uses,
		);
	}

/**
 * Returns an array of Class properties
 * 
 * @return array
 */
	protected function _getProperties() {
		$class = $this->_Reflection->getName();
		$properties = array();
		foreach ($this->_Reflection->getProperties() as $property) {
			if ($property->getDeclaringClass()->name != $class) {
				continue;
			}
			$name = $property->getName();
			$doc = $property->getDocComment();
			$value = $this->_Class->{$name};
			$access = 'public';
			if ($property->isPrivate()) {
				$access = 'private';
			}
			if ($property->isProtected()) {
				$access = 'protected';
			}
			if ($property->isStatic()) {
				$access .= ' static';
			}
			$properties[$name] = array(
				'doc' => $doc,
				'value' => $value,
				'access' => $access,
			);
		}
		return $properties;
	}

/**
 * Returns an array of methods
 * 
 * @return array
 */
	protected function _getMethods() {
		$class = $this->_Reflection->getName();
		$file = file($this->_Reflection->getFileName());
		$methods = array();
		foreach ($this->_Reflection->getMethods() as $method) {
			if ($method->getDeclaringClass()->name != $class) {
				continue;
			}
			$name = $method->getName();
			$doc = $method->getDocComment();
			$access = 'public';
			if ($method->isPrivate()) {
				$access = 'private';
			}
			if ($method->isProtected()) {
				$access = 'protected';
			}
			if ($method->isStatic()) {
				$access .= ' static';
			}
			$start = $method->getStartLine() - 1;
			$end = $method->getEndLine();
			$value = implode('', array_slice($file, $start, $end - $start));
			$methods[$name] = array(
				'doc' => $doc,
				'value' => $value,
				'access' => $access,
			);
		}
		return $methods;
	}

/**
 * Create a clone for Reflection
 * Allows reflecting the same class as its modified
 *
 * @return string New class name
 */
	protected function _createClone() {
		if (!isset($this->_File) || !isset($this->class)) {
			return false;
		}
		$class = uniqid($this->class);
		$clone = dirname($this->_File->path) . DS . $class . '.php';
		$this->_File->copy($clone);
		$file = new File($clone);
		$contents = $file->read();
		$contents = str_ireplace('class ' . $this->class, 'class ' . $class, $contents);
		$file->write($contents);
		$file->close();
		$this->_Clones[] = $clone;
		return $class;
	}

}