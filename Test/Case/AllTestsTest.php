<?php
class AllTestsTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Oven') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}
}