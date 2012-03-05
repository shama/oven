<?php
App::uses('OvenAppController', 'Oven.Controller');

/**
 * Bases Controller
 * A base controller to extend from.
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class BasesController extends OvenAppController {
/**
 * name
 *
 * @var string
 */
	public $name = 'Bases';

/**
 * uses
 *
 * @var array
 */
	public $uses = array();

/**
 * paginate
 *
 * @var array
 */
	public $paginate = array(
		'limit' => 20,
	);

/**
 * __construct
 * @param object $request
 * @param object $response
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		$this->_setupPaths();
	}

/**
 * beforeFilter
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('type', Inflector::humanize(Inflector::underscore($this->name)));
		$this->set('modelClass', $this->modelClass);
		$this->set('table', $this->{$this->modelClass}->table);
	}

/**
 * admin_index
 */
	function admin_index() {
		$data = $this->paginate();
		$this->set(compact('data'));
	}

/**
 * admin_edit
 *
 * @param integer $id 
 */
	function admin_edit($id=null) {
		if (!empty($this->request->data)) {
			if ($this->{$this->modelClass}->saveAll($this->data)) {
				$this->Session->setFlash(__d('oven', 'Saved!', true));
				if (!empty($this->request->data['continue_editing'])) {
					$this->redirect(array('action' => 'edit', $this->{$this->modelClass}->id));
				} else {
					$this->redirect(array('action' => 'index'));
				}
				exit;
			}
		} else {
			$this->request->data = $this->{$this->modelClass}->read(null, $id);
		}
	}

/**
 * admin_delete
 *
 * @param integer $id 
 */
	function admin_delete($id=null) {
		$this->{$this->modelClass}->delete($id);
		$this->Session->setFlash(__d('oven', 'Deleted.', true));
		$this->redirect(array('action' => 'index'));
		exit;
	}

/**
 * admin_sort
 *
 * @todo Write me
 */
	function admin_sort() {
		
	}

/**
 * _setupPaths
 */
	protected function _setupPaths() {
		//$this->viewPath = 'Bases';
	}
	
/**
 * render
 * Override render() to use plugin view if custom view does not exist.
 * @param type $view
 * @param type $layout
 */
	public function render($view = null, $layout = null) {
		//return parent::render($view, $layout);
		
		// DEFAULT LAYOUT
		if (is_null($layout)) {
			$layout = $this->layout;
		}
		if (empty($layout)) {
			$layout = 'default';
		}
		if (!file_exists(APP . 'View' . DS . 'Layouts' . DS . $layout . '.ctp')) {
			$this->plugin = 'Oven';
		}
		
		// DEFAULT VIEW
		if (is_null($view)) {
			$view = $this->action;
		}
		if ($view !== false) {
			if (!file_exists(APP . 'View' . DS . $this->name . DS . $view . '.ctp')) {
				$this->viewPath = 'Bases';
			}
		}
		
		return parent::render($view, $layout);
	}
}