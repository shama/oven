<?php
App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('HtmlHelper', 'View/Helper');
App::uses('OvenFormHelper', 'Oven.View/Helper');

class ContactTestController extends Controller {
	public $name = 'ContactTest';
	public $uses = null;
}

/**
 * Oven Form Helper Test
 * 
 * @package Oven
 * @author Kyle Robinson Young <kyle at dontkry.com>
 * @copyright 2012 Kyle Robinson Young
 */
class OvenFormHelperTest extends CakeTestCase {
/**
 * setup
 */
	public function setUp() {
		parent::setUp();
		$this->Controller = new ContactTestController();
		$this->View = new View($this->Controller);
		$this->Form = new OvenFormHelper($this->View);
		$this->Form->Html = new HtmlHelper($this->View);
		$this->Form->request = new CakeRequest('contacts/add', false);
		$this->Form->request->here = '/contacts/add';
		$this->Form->request['action'] = 'add';
		$this->Form->request->webroot = '';
		$this->Form->request->base = '';
	}

/**
 * tearDown method
 * @return void
 */
	public function tearDown() {
		unset($this->Form);
		parent::tearDown();
	}

/**
 * testInput
 */
	public function testInput() {
		$result = $this->Form->input('body', array(
			'type' => 'ckeditor',
		));
		$expected = '<div class="input textarea"><label for="body">Body</label><textarea name="data[body]" cols="30" rows="6" id="body"></textarea></div>';
		$this->assertEqual($result, $expected);
	}
}