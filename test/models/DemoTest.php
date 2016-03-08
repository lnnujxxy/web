<?php
/**
 * @backupGlobals disabled
 */
class DemoTest extends PHPUnit_Framework_TestCase {

	public function testDemo() {
		$testModel = new TestModel();
		$data = ['value' => 'test'];
		$this->assertTrue($testModel->add($data) == 1);
	}

}
?>