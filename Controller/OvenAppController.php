<?php
App::uses('AppController', 'Controller');

/**
 * Oven App Controller
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenAppController extends AppController {

/**
 * helpers
 * @var array
 */
	public $helpers = array(
		'Session', 'Js', 'Cache', 'Time',
	);

/**
 * components
 * @var array
 */
	public $components = array(
		'Session',
		'Cookie',
		'Paginator',
		'Security',
	);

/**
 * __construct
 *
 * @param object $request
 * @param object $response
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		App::uses('OvenConfig', 'Oven.Lib');
		new OvenConfig();
		App::build(array('View' => array(
			App::pluginPath('Oven') . 'Lib' . DS . 'View' . DS,
		)), App::PREPEND);
	}

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->_setupAuth();
		if (!empty($this->request->params['admin'])) {
			$this->helpers['Html'] = array('className' => 'Oven.OvenHtml');
			$this->helpers['Form'] = array('className' => 'Oven.OvenForm');
		}
	}

/**
 * _setupAuth
 * If you have the Users plugin and Auth is not enabled use Users plugin
 *
 * @return boolean
 */
	protected function _setupAuth() {
		if (CakePlugin::loaded('Users') && !$this->Components->enabled('Auth')) {
			$this->Auth = $this->Components->load('Auth');
			$this->Auth->authenticate = array(
				'Form' => array(
					'fields' => array(
						'username' => 'email',
						'password' => 'password'
					),
					'userModel' => 'Users.User',
					'scope' => array('User.active' => 1),
				),
			);
			$this->Auth->loginAction = array('admin' => false, 'plugin' => 'users', 'controller' => 'users', 'action' => 'login');
		}
		return true;
	}

}