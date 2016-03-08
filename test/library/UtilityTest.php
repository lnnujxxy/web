<?php
/**
 * @backupGlobals disabled
 */
class UtilityTest extends PHPUnit_Framework_TestCase {
	// public function testDate() {
	// 	$preDate = '2015-11-29';
	// 	var_dump(Utility::getUserWeek($preDate));
	// 	exit;
	// }

	// public function testUserWeek() {
	// 	$preDate = '2015-12-22';
	// 	var_dump(Utility::getUserWeek($preDate));
	// 	exit;
	// }

	// public function testSendSms() {
	// 	$mobile = '13401069598';
	// 	$content = '孕妈XXX，您已成功预约XX月XX日XX医院上门产检，将会在XX月XX日跟您进行电话确认，请您保持电话畅通。';

	// 	var_dump(Utility::sendSms($mobile, $content));
	// }
	//
	public function testSyncToOSS() {
		$url = "http://wx.qlogo.cn/mmopen/OWKvvaemJG0G9UYoeDlyv0HnpID6poU71mK8Q78YpEZtNONicLGHo1NLv6wgHNFoicYjtK3xibqxKbusrdJE7IGcaqfwM8pShHZ/0";
		$ret = Utility::syncToOSS($url);
		var_dump($ret);exit;
	}
}