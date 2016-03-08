<?php
/**
 * @backupGlobals disabled
 */
class MysqlTest extends PHPUnit_Framework_TestCase {
	public function testMysql() {
		$table = 'test';
		$sql = "INSERT INTO $table SET `value` = ?";

		$db = Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute(array('value'));

		$this->assertTrue($db->errorCode() == '00000');
	}
}