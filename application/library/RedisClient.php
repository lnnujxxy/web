<?php
/**
 * Redis 处理类
 *
 * @author lnnujxxy@gmail.com
 * @version   1.0
 *
 */
class RedisClient {
	private static $hander;
	private static $instances;
	private $configs;
	private $config;

	/**
	 * 实例化
	 */
	public function __construct($key) {
		$this->configs = Yaf_Registry::get('config')['redis'][$key];
		// $this->configs = $this->configs[$key];
		if (count($this->configs) === 1) {
			$this->config = $this->configs[0];
		}
		return $this;
	}

	public static function getInstance($key, $isMaster = true) {
		$isMaster = 1;
		if (!self::$hander[$key][$isMaster]) {
			self::$hander[$key][$isMaster] = (new self($key))->getMSConfig($isMaster)->getRedis();
		}

		return self::$hander[$key][$isMaster];
	}

	/*
		 * 主从方式
	*/
	public function getMSConfig($isMaster = false) {
		if (count($this->configs) > 1) {
			if ($isMaster) {
				$this->config = $this->configs[0];
			} else {
				array_shift($this->configs);
				$this->config = $this->configs[array_rand($this->configs)];
			}
		}
		return $this;
	}

	/*
		 * Hash方式
	*/
	public function getHashConfig($hashKey = null) {
		if (count($this->configs) > 1 && $hashKey) {
			$count = count($this->configs);
			$this->config = $this->configs[$hashKey % $count];
		}
		return $this;
	}

	/**
	 * 连接redis
	 */
	public function getRedis() {
		if (!$this->config) {
			throw new Exception('redis config is empty');
		}
		$key = md5(serialize($this->config));
		try {
			$redis = new Redis();
			for ($i = 0; $i < 2; $i++) {
				if ($redis->connect($this->config['host'], $this->config['port'], 0.5, NULL, 100)) {
					break;
				}
			}
			if (isset($this->config['auth'])) {
				$redis->auth($this->config['auth']);
			}

			if (isset($this->config['prefix'])) {
				// Utility::debug('prefix = ' . $this->config['prefix']);
				$redis->setOption(Redis::OPT_PREFIX, $this->config['prefix']);
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		if (!is_object($redis)) {
			throw new Exception('redis connect is failure');
		}

		return $redis;
	}

	private function pingRedis($instance) {
		if ($instance instanceof Redis) {
			return '+PONG' === $instance->ping() ? true : false;
		}
		return false;
	}
}
?>

