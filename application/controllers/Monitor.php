<?php
/**
 * @name MonitorController
 * @author zhouweiwei
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class MonitorController extends Yaf_Controller_Abstract {

	public function checkNginxFPMAction() {
		$res = 'yes';
		exit($res);
	}

	public function checkRedisAction() {
		$redis = RedisClient::getInstance(Constant::REDIS_INSTANCE_DEFAULT);
		$redis->set('check_redis', 1);
		$res = 'yes';

		if (!$redis->get('check_redis')) {
			$res = 'no';
		}

		$infos = $redis->info();
		$usedMemory = $infos['used_memory'];

		//大于4G报警
		if ($usedMemory > 4294967296) {
			$res = 'no';
		}

		exit($res);
	}

	public function checkMysqlAction() {
		$db = Mysql::getInstance(true);
		$key = IDWork::getInstance()->nextId();

		$sql = "INSERT INTO hh_monitor SET `key` = '$key', `value` = 1";
		$sth = $db->prepare($sql);
		$sth->execute();

		if ($db->errorCode() != '00000') {
			exit('no');
		}

		$sql = "SELECT `value` FROM hh_monitor WHERE `key` = '$key'";
		$sth = $db->prepare($sql);
		$sth->execute();
		$value = $sth->fetchColumn();
		if (!$value) {
			exit('no');
		}

		exit('yes');
	}

}
