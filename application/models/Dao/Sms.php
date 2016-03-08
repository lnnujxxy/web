<?php
/**
 * 短信model
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class Dao_SmsModel extends Dao_BaseModel {

	public static $instance;
	public function __construct() {
		$this->table = 'hh_sms';
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function addSms($mobile, $content, $sendtime = null) {
		if (is_null($sendtime)) {
			$sendtime = date('Y-m-d H:i:s');
		}

		$data = [
			'mobile' => $mobile,
			'content' => $content,
			'sendtime' => $sendtime,
			'channel' => 2, //约友医短信通道
		];
		return $this->add($data);
	}

	public function cancelSms($mobile) {
		$mobile = intval($mobile);
		$sql = "DELETE FROM " . $this->getTable() . " WHERE mobile = $mobile AND issend = 0 AND
				sendtime > NOW() ORDER BY id DESC LIMIT 1";
		$db = \Mysql::getDB($sql);
		$db->exec($sql);
	}
}
