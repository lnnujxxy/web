<?php
/**
 * @backupGlobals disabled
 */
class AesTest extends PHPUnit_Framework_TestCase {
	public function testAes() {
		$aes = new Aes('1234567812345678');
		$str = "hello";
		$str2 = $aes->decrypt($aes->encrypt($str));
		$this->assertTrue($str == $str2);

		// $aes = new Aes("1234567890qwerty");
		// $pm = 'MFgXH+2abVPA4mNkWRGYqQ==';
		// var_dump($aes->decrypt($pm));
	}
}