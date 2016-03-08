<?php
/**
 * @backupGlobals disabled
 */
class RedisClientTest extends PHPUnit_Framework_TestCase {
	private $redis;

	public function testMSRedis() {
		$this->redis = RedisClient::getInstance("default", true);
		$this->redis->set("foo1", "bar1");
		$this->assertTrue($this->redis->get("foo1") == "bar1");
	}

}
?>