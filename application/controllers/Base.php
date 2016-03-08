<?php
/**
 * 基础控制器
 *
 * @authaesr lnnujxxy@gmail.com
 * @version  1.0
 */

class BaseController extends Yaf_Controller_Abstract {
	/**
	 * 统一格式输出
	 *
	 * @param Int $code 返回码
	 * @param String $msg 描述
	 * @param Mixed $content 返回数据
	 *
	 * @return Json
	 */
	protected function output($code, $msg, $content = null) {
		if ($_SERVER['env'] !== 'phpunit') {
			$this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$ret = [
			'code' => intval($code),
			'msg' => $msg,
			'content' => $content,
		];

		if (is_null($ret['content'])) {
			unset($ret['content']);
		}

		if ($ret['code'] == 0) {
			unset($ret['msg']);
		}
		$json = json_encode($ret, JSON_UNESCAPED_UNICODE);

		//echo $json;
		$this->getResponse()->setBody($json);
		$this->getResponse()->response();
		$this->getResponse()->clearBody();
		return false;
	}

	/**
	 * 错误返回输出
	 * @param  Mixed $code 错误码
	 * @param  String $msg 描述信息
	 * @return Json
	 */
	protected function error($code, $msg) {
		$ret = [
			'code' => $code,
			'msg' => $msg,
		];

		if ($_SERVER['env'] !== 'phpunit') {
			$this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$json = json_encode($ret, JSON_UNESCAPED_UNICODE);
		$this->getResponse()->setBody($json);
		$this->getResponse()->response();
		$this->getResponse()->clearBody();
		return false;
	}

	/**
	 * 根据module name判断请求来源设备
	 *
	 * @return String 设备名
	 */
	protected function getDevice() {
		if ($this->getModuleName() === Constants::MODULE_NAME_ANDROID) {
			return Constants::DEVICE_ANDROID;
		} elseif ($this->getModuleName() === Constants::MODULE_NAME_IOS) {
			return Constants::DEVICE_IOS;
		} else {
			return 'unkown';
		}
	}

	/**
	 * 升级接口
	 * @param  Array $params 参数数组
	 * @return
	 */
	protected function upgradeApi($params) {
		if (isset($params['sv']) && $params['sv'] > 1) {
			$action = $this->getRequest()->getActionName();
			$method = str_replace('Action', '', $action) . $params['sv'] . 'Action';

			if (method_exists($this, $method)) {
				$this->$method($params);
				exit;
			}
		}
	}

	/**
	 * 获取接口参数
	 *
	 * @return Array 参数数组
	 */
	protected function getParams() {
		$headers = $this->getallheaders();

		if (in_array($headers['From'], ['h5', 'ios', 'andriod']) && $headers['Content-Type'] == 'application/json') {
			$json = file_get_contents("php://input");
			$_params = ['ak' => $headers['Ak']];

			if ($json) {
				$params = json_decode($json, true);
			}

			$params = array_merge((array) $_params, (array) $params);
			return $params;
		} else {
			return $_REQUEST;
		}
	}

	private function getallheaders() {
		$headers = '';
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}

	protected function checkAuth($params) {
		$params['uid'] = Dao_AkRkModel::getInstance()->getUidByAK($params['ak']);
		Utility::debug('params = ' . print_r($params, true));
		if (!SessionModel::isLogin($params['uid'])) {
			Utility::quit(Msg::ERR_NO_NOLOGIN, Msg::ERR_MSG_NOLOGIN);
		}
		return $params;
	}

	protected function getAction() {
		return strtolower($this->getRequest()->getModuleName() .
			'_' . $this->getRequest()->getControllerName() .
			'_' . $this->getRequest()->getActionName());
	}

	/**
	 * DES 加密
	 *
	 * @param String $str 未加密字符串
	 * @param String $key 加密秘钥
	 *
	 * @return String 加密字符串
	 */
	protected function desEncrypt($str, $key) {
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
	protected function desDecrypt($str, $key) {
		$aes = new Des($key);
		return $aes->decrypt($str);
	}

}
