<?php
/**
 * @backupGlobals disabled
 */
class TrackingTest extends PHPUnit_Framework_TestCase {

	public function testTracking() {
		$trackingModel = new TrackingModel();
		$ret = $trackingModel->tracking(Constant::SERVICE_ACTION_LOCK_UNPAY, ['order_id' => 1]);
		$this->assertTrue($ret);
	}

}
?>