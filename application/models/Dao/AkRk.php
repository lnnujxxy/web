<?php
/**
 * Ak RK数据获取类, 可以访问数据库，文件，其它系统等
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class Dao_AkRkModel extends Dao_BaseModel {
	const AK_TTL = 7200; //2小时
	const RK_TTL = 2592000; //一个月
	const PREV_TTL = 3; //提前过期时间

	const TYPE_AK = 1;
	const TYPE_RK = 2;

	const REDIS_INSTANCE_DEFAULT = 'default'; //默认redis实例

	const LOCK_AK_RK = 'lock:ak:rk:';

	public static $instance;

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->table = 'hh_ak_rk';
	}

	/**
	 * 生成AK
	 */
	public function genAK($uid) {
		$ak = md5(\Utility::uuid());
		$data = [
			'key' => $ak,
			'type' => self::TYPE_AK,
			'uid' => $uid,
			'expiretime' => date('Y-m-d H:i:s', time() + self::AK_TTL),
		];
		$this->add($data);
		return $ak;
	}

	/**
	 * 生成RK
	 */
	public function genRK($uid) {
		$ak = md5(\Utility::uuid());
		$data = [
			'key' => $rk,
			'type' => self::TYPE_RK,
			'uid' => $uid,
			'expiretime' => date('Y-m-d H:i:s', time() + self::RK_TTL),
		];
		$this->add($data);

		return $rk;
	}

	/**
	 * 刷新AK
	 */
	public function refreshAK($rk) {
		$sql = "SELECT uid, expiretime FROM " . $this->getTable() . " WHERE `key` = ? AND type = " . self::TYPE_RK;
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute([$rk]);
		$row = $sth->fetch(\PDO::FETCH_ASSOC);
		if ($row['uid'] && strtotime($row['expiretime']) > time()) {
			$ak = $this->genAK($row['uid']);
			return $ak;
		}
		return '';
	}

	public function getUidByAK($ak) {
		$sql = "SELECT uid, expiretime FROM " . $this->getTable() . " WHERE `key` = ? AND `type` = " . self::TYPE_AK;
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute([$ak]);
		$row = $sth->fetch(\PDO::FETCH_ASSOC);

		if (!$row) {
			//非法AK
			\Utility::quit(\Msg::ERR_NO_AK_INVALID, \Msg::ERR_MSG_AK_INVALID);
		} elseif (strtotime($row['expiretime']) + self::PREV_TTL <= time()) {
			$redis = \RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
			$lock = self::LOCK_AK_RK . $ak;

			if ($redis->setNx($lock, 1)) {
				//失效ak
				$expiretime = $row['expiretime'] + self::AK_TTL;
				$sql = "UPDATE " . $this->getTable() . " SET expiretime = ? WHERE `key` = ? AND `type` = " . self::TYPE_AK;
				$db = \Mysql::getDB($sql);
				$sth = $db->prepare($sql);
				$sth->execute([$expiretime, $ak]);
				$redis->del($lock);

				\Utility::quit(\Msg::ERR_NO_AK_EXPIRE, \Msg::ERR_MSG_AK_EXPIRE);
			}
		} else {
			return $row['uid'];
		}
	}

}
