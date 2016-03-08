<?php
/**
 * 用户控制器
 *
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class BaseUserController extends BaseController {

	/**
	 * 用户登录接口
	 */
	public function loginAction() {
		$params = $this->getParams();
		$this->upgradeApi($params);

		$db = Mysql::getInstance(false);
		$userModel = new UserModel($db);
		$content = $userModel->login('11', '222');

		return $this->output(0, 'ok', $content);
	}

	public function login2Action() {
		return $this->output(0, 'ok', 'api 2');
	}
}
