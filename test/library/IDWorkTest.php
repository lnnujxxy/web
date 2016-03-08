<?php
/**
 * @backupGlobals disabled
 */
class IDWorkTest extends PHPUnit_Framework_TestCase {
	public function testIDWork() {
		for ($i = 0; $i < 100; $i++) {
			$id = IDWork::getInstance()->nextId();
			echo $id . "\n";
		}

		$this->assertTrue($id > 0);
	}
}
?>