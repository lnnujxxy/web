<?php
/**
 * 微信公众号
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
class Weixin {
	const KEY_ACCESS_TOKEN_PREFIX = 'key:access:token:';
	const KEY_JSAPI_TICKET_PREFIX = 'key:jsapi:ticket:';

	private $appid;
	private $secret;

	public function __construct($appid, $secret) {
		$this->appid = $appid;
		$this->secret = $secret;
	}

	public function getAccessToken($code) {
		$params = [
			'appid' => $this->appid,
			'secret' => $this->secret,
			'code' => $code,
			'grant_type' => 'authorization_code',
		];
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?" . Utility::toUrlParams($params);

		return $this->queryUrl($url);
	}

	public function getUserInfo($accessToken, $openid) {
		$params = [
			'access_token' => $accessToken,
			'openid' => $openid,
			'lang' => 'zh_CN',
		];
		$url = "https://api.weixin.qq.com/sns/userinfo?" . Utility::toUrlParams($params);
		// Utility::debug('$url = ' . $url);
		return $this->queryUrl($url);
	}

	public function getUserInfoByOpenId($openid) {
		$accessToken = $this->fetchGlobalAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openid}&lang=zh_CN";
		Utility::debug('$url = ' . $url);
		return $this->queryUrl($url);
	}

	public function getGlobalAccessToken() {
		$params = [
			'grant_type' => 'client_credential',
			'appid' => $this->appid,
			'secret' => $this->secret,
		];
		$url = "https://api.weixin.qq.com/cgi-bin/token?" . Utility::toUrlParams($params);

		return $this->queryUrl($url);
	}

	public function storeGlobalAccessToken() {
		$accessToken = $this->getGlobalAccessToken()['access_token'];

		if ($accessToken) {
			$redis = RedisClient::getInstance(Constant::REDIS_INSTANCE_DEFAULT);
			$redis->set(self::KEY_ACCESS_TOKEN_PREFIX . $this->appid, $accessToken);
		}

		return $accessToken;
	}

	public function fetchGlobalAccessToken() {
		$redis = RedisClient::getInstance(Constant::REDIS_INSTANCE_DEFAULT);
		return $redis->get(self::KEY_ACCESS_TOKEN_PREFIX . $this->appid);
	}

	public function getJsapiTicket($accessToken) {
		$params = [
			'access_token' => $accessToken,
			'type' => 'jsapi',
		];
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?" . Utility::toUrlParams($params);

		return $this->queryUrl($url);
	}

	public function storeJsapiTicket($accessToken) {
		$ticket = $this->getJsapiTicket($accessToken)['ticket'];

		if ($ticket) {
			$redis = RedisClient::getInstance(Constant::REDIS_INSTANCE_DEFAULT);
			$redis->set(self::KEY_JSAPI_TICKET_PREFIX . $this->appid, $ticket);
		}

		return $ticket;
	}

	public function fetchJsapiTicket() {
		$redis = RedisClient::getInstance(Constant::REDIS_INSTANCE_DEFAULT);
		return $redis->get(self::KEY_JSAPI_TICKET_PREFIX . $this->appid);
	}

	public function download($mediaId) {
		$accessToken = $this->fetchGlobalAccessToken();
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $accessToken . '&media_id=' . $mediaId;
		$file = '/tmp/' . md5(microtime(true));
		file_put_contents($file, fopen($url, 'r'));
		return $file;
	}

	public function getMenu() {
		$accessToken = $this->fetchGlobalAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=" . $accessToken;
		return file_get_contents($url);
	}

	public function createMenu() {
		$menu = [
			'button' => [
				[
					'type' => 'view',
					'name' => '约医生',
					'url' => 'http://yyy.dev.shiyuehehu.com',
				],
				[
					'name' => '个人中心',
					'sub_button' => [
						[
							'type' => 'view',
							'name' => '我的订单',
							'url' => 'http://yyy.dev.shiyuehehu.com/user',
						],
						[
							'type' => 'view',
							'name' => '分享给好友',
							'url' => 'http://yyy.dev.shiyuehehu.com/welcome?fm=menu',
						],
					],
				],
			],
		];

		$accessToken = $this->fetchGlobalAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $accessToken;

		$result = $this->postJson($url, json_encode($menu, JSON_UNESCAPED_UNICODE));
		//print_r($result);
		return $result;
	}

	public function createQrcode($hospitalId, $doctorId, $qrcodeFrom) {
		$sceneId = QrcodeWeixinModel::getInstance()->getSceneId($hospitalId, $doctorId);
		if (!$sceneId) {
			$maxSceneId = QrcodeWeixinModel::getInstance()->getMaxSceneId();
			$sceneId = max(200, $maxSceneId + 1);
			$arr = $this->getQrcode($sceneId);

			$data = [
				'scene_id' => $sceneId,
				'hospital_id' => intval($hospitalId),
				'doctor_id' => intval($doctorId),
				'qrcode_from' => intval($qrcodeFrom),
				'ticket' => $arr['ticket'],
				'url' => $arr['url'],
			];
			QrcodeWeixinModel::getInstance()->add($data);
		} else {
			$data = QrcodeWeixinModel::getInstance()->getRowBySceneId($sceneId);
		}
		return $data;
	}

	private function getQrcode($sceneId) {
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $this->fetchGlobalAccessToken();
		$json = [
			'action_name' => 'QR_LIMIT_SCENE',
			'action_info' => [
				'scene' => ['scene_id' => $sceneId],
			],
		];

		list($code, $content) = $this->postJson($url, json_encode($json));
		$data = [];
		if ($content && strpos($content, 'url') !== false) {
			$data = json_decode($content, true);
		}
		return $data;
	}

	private function postJson($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: ' . strlen($data))
		);
		ob_start();
		curl_exec($ch);
		$content = ob_get_contents();
		ob_end_clean();

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return array($code, $content);
	}

	private function queryUrl($url) {
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

}
