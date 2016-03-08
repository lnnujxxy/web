<?php

class Crypt {

	public static function execute($str, $crypt = 'encrypt', $key = null) {
		if (!$key) {
			// $key = 'OeE3iNfiBCT32nA3ceTf4xQMCZwjDrBHhTi7V+keQ0E=';
			$key = Yaf_Registry::get('config')['application']['cryptkey'];
		}
		return $crypt === 'encrypt' ?
		self::encrypt($str, $key) : self::decrypt($str, $key);
	}

	/**
	 *
	 * 加密函数
	 * @param $data String
	 * @param $key String 密钥
	 * @return String
	 */
	public static function encrypt($data, $key = null) {
		if (!$key) {
			$key = Yaf_Registry::get('config')['application']['cryptkey'];
		}

		$key = md5($key);
		$data = base64_encode($data);
		$x = 0;
		$len = strlen($data);
		$l = strlen($key);
		$char = $str = '';

		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) {
				$x = 0;
			}

			$char .= substr($key, $x, 1);
			$x++;
		}
		for ($i = 0; $i < $len; $i++) {
			$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
		}
		return base64_encode($str);
	}

	/**
	 *
	 * 解密函数
	 * @param $data String
	 * @param $key String 密钥
	 * @return String
	 */
	public static function decrypt($data, $key = null) {
		if (!$key) {
			$key = Yaf_Registry::get('config')['application']['cryptkey'];
		}

		$data = base64_decode($data);
		$key = md5($key);
		$x = 0;
		$len = strlen($data);
		$l = strlen($key);
		$char = $str = '';

		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) {
				$x = 0;
			}

			$char .= substr($key, $x, 1);
			$x++;
		}
		for ($i = 0; $i < $len; $i++) {
			if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
				$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			} else {
				$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
		}
		return base64_decode($str);
	}
}
?>