<?php
/**
 * 通用函数封装
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */

class Utility {

	public static function microtime() {
		return intval(microtime(true) * 1000);
	}

	public static function uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),

			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,

			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	public static function arrayOrderBy() {
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row) {
					$tmp[$key] = $row[$field];
				}

				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}

	public static function getClientIP() {
		$clientIp = '';

		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$clientIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$clientIp = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$clientIp = $_SERVER["REMOTE_ADDR"];
		}

		return $clientIp;
	}

	/**
	 * @desc:按照字符编码截取汉字，中文按两个宽度，英文一个宽度
	 */
	public static function utfSubstr($str, $position, $length, $type = 0) {
		$startPos = strlen($str);
		$startByte = 0;
		$endPos = strlen($str);
		$count = 0;
		for ($i = 0, $len = strlen($str); $i < $len; $i++) {
			if ($count >= $position && $startPos > $i) {
				$startPos = $i;
				$startByte = $count;
			}
			if (($count - $startByte) >= $length) {
				$endPos = $i;
				break;
			}
			$value = ord($str[$i]);
			if ($value > 127) {
				$count++;
				if ($value >= 192 && $value <= 223) {
					$i++;
				} elseif ($value >= 224 && $value <= 239) {
					$i = $i + 2;
				} elseif ($value >= 240 && $value <= 247) {
					$i = $i + 3;
				} else {
					//logger
				}
				//else return self::raiseError("\"$str\" Not a UTF-8 compatible string", 0, __CLASS__, __METHOD__, __FILE__, __LINE__);
			}
			$count++;
		}
		if ($type == 1 && ($endPos - 6) > $length) {
			return substr($str, $startPos, $endPos - $startPos) . "...";
		} else {
			return substr($str, $startPos, $endPos - $startPos);
		}
	}

	public static function utf8_strlen($str) {
		$count = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			$value = ord($str[$i]);
			if ($value > 127) {
				$count++;
				if ($value >= 192 && $value <= 223) {
					$i++;
				} elseif ($value >= 224 && $value <= 239) {
					$i = $i + 2;
				} elseif ($value >= 240 && $value <= 247) {
					$i = $i + 3;
				} else {
					die('Not a UTF-8 compatible string');
				}

			}
			$count++;
		}
		return $count;
	}

	public static function utf8Strlen($string) {
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}

	public static function utf8Substr($string, $length) {
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return join('', array_slice($match[0], 0, $length));
	}

	public static function debug($msg) {
		if ($_SERVER['env'] != 'product') {
			$name = 'debug_' . $_SERVER['HTTP_HOST'];
			Logger::getInstance(Yaf_Registry::get('config')['application']['logdir'], Logger::DEBUG, $name)->logDEBUG($msg);
		}
	}

	public static function log($name, $msg) {
		$name = $name . '_' . $_SERVER['HTTP_HOST'];
		Logger::getInstance(Yaf_Registry::get('config')['application']['logdir'], Logger::DEBUG, $name)->logInfo($msg);
	}

	public static function errorlog($msg) {
		$name = 'error_' . $_SERVER['HTTP_HOST'];
		Logger::getInstance(Yaf_Registry::get('config')['application']['logdir'], Logger::ERR, $name)->logError($msg);
	}

	public static function logError($errno, $errstr, $errfile, $errline) {
		self::logException(new ErrorException($errstr, $errno, 1, $errfile, $errline));
	}

	public static function logException(Exception $e) {
		$log = sprintf("%s:%d %s (%d) [%s]\n", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
		self::errorlog($log);
	}

	public static function baseEncode($val, $base = 62, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		if (!isset($base)) {
			$base = strlen($chars);
		}

		$str = '';
		do {
			$m = bcmod($val, $base);
			$str = $chars[$m] . $str;
			$val = bcdiv(bcsub($val, $m), $base);
		} while (bccomp($val, 0) > 0);
		return $str;
	}

	public static function baseDecode($str, $base = 62, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		if (!isset($base)) {
			$base = strlen($chars);
		}

		$len = strlen($str);
		$val = 0;
		$arr = array_flip(str_split($chars));
		for ($i = 0; $i < $len; ++$i) {
			$val = bcadd($val, bcmul($arr[$str[$i]], bcpow($base, $len - $i - 1)));
		}
		return $val;
	}

	/**
	 * 替换说明文本中变量
	 *
	 * @param String $text 文本字符串
	 * @param Array $data map数组
	 *
	 * @return String 替换后字符串
	 */
	public static function replaceText($text, $data) {
		return str_replace(array_keys($data), array_values($data), $text);
	}

	public static function isUnit() {
		return $_REQUEST['unit'] || $_SERVER['phpunit'] ? true : false;
	}

	public static function mapData($row, $map) {
		$data = [];
		foreach ($map as $key => $val) {
			if (isset($row[$key])) {
				$data[$val] = $row[$key];
			}
		}
		return $data;
	}

	/**
	 * 判断项目中的合法ID
	 * @param  Mixed  $id
	 * @return boolean
	 */
	public static function isValidId($id) {
		return is_numeric($id) && $id > 0;
	}

	public static function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
	}

	public static function isVcode($vcode) {
		return $vcode >= 1000 && $vcode <= 9999;
	}

	public static function phpslashes($string, $type = 'add') {
		if ($type == 'add') {
			if (get_magic_quotes_gpc()) {
				return $string;
			} else {
				if (function_exists('addslashes')) {
					return self::daddslashes($string);
				} else {
					return mysql_real_escape_string($string);
				}
			}
		} else if ($type == 'strip') {
			return self::dstripslashes($string);
		} else {
			die('error in phpslashes (mixed,add | strip)');
		}
	}

	public static function daddslashes($string) {
		if (!is_array($string)) {
			return addslashes($string);
		}

		foreach ($string as $key => $val) {
			$string[$key] = self::daddslashes($val);
		}

		return $string;
	}

	public static function dstripslashes($value) {
		$value = is_array($value) ? array_map([self, "dstripslashes"], $value) : stripslashes($value);
		return $value;
	}

	public static function quit($code, $msg) {
		header('Content-type:application/json; charset=utf-8');
		$ret = [
			'code' => $code,
			'msg' => $msg,
		];

		$json = json_encode($ret);

		echo $json;
		exit;
	}

	/**
	 * DES 加密
	 *
	 * @param String $str 未加密字符串
	 * @param String $key 加密秘钥
	 *
	 * @return String 加密字符串
	 */
	public static function desEncrypt($str, $key) {
		$aes = new Des($key);
		return $aes->encrypt($str);
	}

	/**
	 * DES 解密
	 *
	 * @param String $str 加密字符串
	 * @param String $key 解密秘钥
	 *
	 * @return String 解密字符串
	 */
	public static function desDecrypt($str, $key) {
		$aes = new Des($key);
		return $aes->decrypt($str);
	}

	public static function int($s) {
		return ($a = preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $s)) ? $a : '0';
	}

	public static function queryUrl($url) {
		for ($i = 0; $i < 2; $i++) {
			$content = Network::get($url);
			if ($content) {
				break;
			}
		}

		if (!$content) {
			return [];
		}

		return json_decode($content, true);
	}

	public static function toUrlParams($values) {
		$buff = "";
		foreach ($values as $k => $v) {
			if ($k != "sign" && $v != "" && !is_array($v)) {
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}

}
