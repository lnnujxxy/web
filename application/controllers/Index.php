<?php
/**
 * index 控制器
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
class IndexController extends Yaf_Controller_Abstract {

	public function indexAction($name = "yaf") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");
		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
		return true;
	}

	public function helloAction() {
		Yaf_Dispatcher::getInstance()->disableView();
		$response = json_encode("json");
		$this->getResponse()->setBody($response, 'content');
	}

	public function redisAction() {
		$var = $this->getRequest()->getQuery('var', 1);
		$redis = RedisClient::getInstance("default");

		$redis->set("redis", $var);
		echo $redis->get("redis") . "\r\n";

		return FALSE;
	}

	public function cliAction() {
		print_r($_SERVER['argv']);

		return FALSE;
	}

}
