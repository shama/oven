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
 *
 * @param CakeRequest $request
 * @param CakeResponse $response
 */
	public function __construct(CakeRequest $request = null, CakeResponse $response = null) {
		parent::__construct($request, $response);
		$this->set('type', Inflector::humanize(Inflector::underscore($this->name)));
		$this->set('modelClass', $this->modelClass);
		$this->set('table', $this->{$this->modelClass}->table);
	}

/**
 * admin_index
 */
	public function admin_index() {
		$data = $this->paginate();
		$this->set(compact('data'));
	}

/**
 * admin_edit
 *
 * @param integer $id 
 */
	public function admin_edit($id=null) {
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
	public function admin_delete($id=null) {
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
	public function admin_sort() {
	}

/**
 * Override render() to use plugin views if custom view does not exist.
 *
 * @param type $view
 * @param type $layout
 */
	public function render($view = null, $layout = null) {
		if (empty($layout) && !empty($this->layout)) {
			$layout = $this->layout;
		}
		if (empty($layout)) {
			$layout = 'oven';
		}
		if (!file_exists(APP . 'View' . DS . 'Layouts' . DS . $layout . '.ctp')) {
			$this->plugin = 'Oven';
		}
		if (is_null($view)) {
			$view = $this->action;
		}
		if ($view !== false) {
			$viewPath = substr(get_class($this), 0, -10);
			if (!file_exists(APP . 'View' . DS . $viewPath . DS . $view . '.ctp')) {
				$this->plugin = 'Oven';
				$this->viewPath = 'Bases';
			} else {
				$this->viewPath = $viewPath;
			}
		}
		return parent::render($view, $layout);
	}

}