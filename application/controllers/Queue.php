<?php
/**
 * 队列处理
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */

/**
 * queue脚本
 * /usr/local/php/bin/php /opt/chanjian.api.shiyuehehu.com/cli.php request_uri="/queue/sendSms"
 * /usr/local/php/bin/php /opt/chanjian.api.shiyuehehu.com/cli.php request_uri="/queue/syncAvatarToOSS"
 */

class QueueController extends Yaf_Controller_Abstract {
	/**
	 * @uses
	 */
	public function indexAction() {
		echo "queue";
		return false;
	}

}
