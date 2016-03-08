<?php
/**
 * 定期任务
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
class CronController extends Yaf_Controller_Abstract {

	public function indexAction() {
		echo "cron";
		return false;
	}

	/**
	 * 1 * * * * /usr/local/php/bin/php /opt/doctor.dev.shiyuehehu.com/cli.php request_uri="/cron/accessToken" env=dev
	 */
	public function accessTokenAction() {
		$appid = Utility::getWeixinAppid();
		$secret = Utility::getWeixinSecret();

		$weixin = new Weixin($appid, $secret);
		$accessToken = $weixin->storeGlobalAccessToken();

		echo date('Y-m-d H:i:s') . " $accessToken ok\n";
		return false;
	}

	/**
	 * 2 * * * * /usr/local/php/bin/php /opt/doctor.dev.shiyuehehu.com/cli.php request_uri="/cron/jsapiTicket" env=dev
	 */
	public function jsapiTicketAction() {
		$appid = Utility::getWeixinAppid();
		$secret = Utility::getWeixinSecret();

		$weixin = new Weixin($appid, $secret);
		$accessToken = $weixin->fetchGlobalAccessToken();
		$jsapiTicket = $weixin->storeJsapiTicket($accessToken);
		echo date('Y-m-d H:i:s') . " $jsapiTicket ok\n";
		return false;
	}

}
