<?php
/**
 * @backupGlobals disabled
 */
class SessionTest extends PHPUnit_Framework_TestCase {

	public function testSession() {
		$sessModel = new SessionModel();
		$sid = 10000;
		$data = ['sid' => $sid, 'key' => 'value'];

		$this->assertTrue($sessModel->storeSession($sid, $data));

		$this->assertTrue($sessModel->isLogin($sid));

		$this->assertTrue($sessModel->delSession($sid));

		$this->assertFalse($sessModel->isLogin($sid));
	}

}

?>