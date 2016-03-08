<?php
/**
 * session model
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class SessionModel {
	const KEY_SESSION_PREFIX = 'key:session:'; //存储用户信息key
	const KEY_TTL = 86400 * 7; //默认过期时间
	const REDIS_INSTANCE_DEFAULT = 'default'; //默认redis实例
	const HASHKEY_LOGIN_FAIL = 'hashkey:login:fail'; //登录失败hashkey
	const MAX_LOGIN_FAIL = 5; //登录失败错误超过锁定

	const HASHKEY_LOGIN_COUNT = 'hashkey:login:count'; //记录登录次数

	const KEY_SETPW_COUNT = 'key:setpw:count:'; //记录设置密码次数
	const MAX_SETPW_COUNT = 5; //记录设置密码次数
	const REDIS_VALUE_PRE = 'user:roles:'; //redis存储用户权限前缀

	/**
	 * 存储Session hashkey
	 * @param  Int $sid 会话SID
	 * @return String
	 */
	private static function getSessionKey($sid) {
		$sid = is_numeric($sid) ? $sid : md5($sid);
		return self::KEY_SESSION_PREFIX . $sid;
	}

	/**
	 * 存储Session
	 * @param  Int $sid 会话SID
	 * @param  Array $row 用户信息
	 * @return Boolean
	 */
	public static function storeSession($sid, $row, $ttl = self::KEY_TTL) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		$key = self::getSessionKey($sid);
		$row['sid'] = $sid;
		$redis->set($key, json_encode($row));
		// $redis->setEx($key, $ttl, json_encode($row));
		return true;
	}

	/**
	 * 删除Session
	 * @param  Int $sid 会话SID
	 * @return Boolean
	 */
	public static function delSession($sid) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		$key = self::getSessionKey($sid);
		$redis->del($key);
		return true;
	}

	/**
	 * 获取Session
	 * @param  Int $sid 会话SID
	 * @return Array
	 */
	public static function getSession($sid) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT, false);
		$key = self::getSessionKey($sid);
		$ret = $redis->get($key);
		if ($ret) {
			return json_decode($ret, true);
		}
		return [];
	}

	public static function getActions($uid) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		$key = self::REDIS_VALUE_PRE . $uid;
		$ret = $redis->get($key);

		if ($ret) {
			return json_decode($ret, true);
		}
		return [];
	}

	public static function setActions($uid, $roles) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		$key = self::REDIS_VALUE_PRE . $uid;
		$actions = [];

		foreach ((array) $roles as $module => $rows) {
			foreach ((array) $rows as $row) {
				if (!$row['action']) {
					continue;
				}

				foreach (explode('|', $row['action']) as $action) {
					$actions[strtolower($action)] = 1;
				}
			}
		}

		$redis->set($key, json_encode($actions));
		return true;
	}

	/**
	 * 判断是否登录
	 * @param  Int  $sid 会话SID
	 * @return Boolean
	 */
	public static function isLogin($sid) {
		$session = self::getSession($sid);
		return isset($session['uid']) && $session['uid'] == $sid
		|| isset($session['sid']) && $session['sid'] == $sid;
	}

	public static function incrLoginFailNum($id, $num = 1) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		return $redis->hIncrby(self::HASHKEY_LOGIN_FAIL, $id, $num);
	}

	public static function checkLoginFailNum($id) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT, false);
		return $redis->hGet(self::HASHKEY_LOGIN_FAIL, $id) >= self::MAX_LOGIN_FAIL;
	}

	public static function incrLoginCount($id, $num = 1) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		// Utility::debug('login count = ' . $redis->hIncrby(self::HASHKEY_LOGIN_COUNT, $id, $num));
		return $redis->hIncrby(self::HASHKEY_LOGIN_COUNT, $id, $num);
	}

	public static function isFirstLogin($id) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT, false);
		return $redis->hGet(self::HASHKEY_LOGIN_COUNT, $id) <= 1;
	}

	public static function setNewPass($id, $num = 1) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT);
		$redis->incr(self::KEY_SETPW_COUNT . $id, $num);
		$redis->expire(self::KEY_SETPW_COUNT . $id, 86400);
		return true;
	}

	public static function isSetNewPass($id) {
		$redis = RedisClient::getInstance(self::REDIS_INSTANCE_DEFAULT, false);
		return $redis->get(self::KEY_SETPW_COUNT . $id) < self::MAX_SETPW_COUNT;
	}
}
