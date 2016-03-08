<?php
/**
 * 测试控制器
 *
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class BaseTestController extends BaseController {

	/**
	 * 用户登录接口
	 */
	public function IndexAction() {
		$params = $this->getParams();
		$this->upgradeApi($params);
		$params = $this->checkAuth($params);

		$sampleModel = new Dao_TestModel();
		$content = $sampleModel->selectSample();
		return $this->output(Msg::SUCC_NO_COMMON, Msg::SUCC_MSG_COMMON, $content);
	}

	public function mysqlAction() {
		$params = $this->getParams();
		$this->upgradeApi($params);
		// $params = $this->checkAuth($params);

		$db = Mysql::getDB("SELECT 1");
		return $this->output(Msg::SUCC_NO_COMMON, Msg::SUCC_MSG_COMMON, $content);
	}
}
