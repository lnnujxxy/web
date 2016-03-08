<?php
/**
 * 入口文件
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
const APPLICATION_PATH = __DIR__;

if (isset($_SERVER['HTTP_USER_AGENT']) && substr($_SERVER['HTTP_USER_AGENT'], 0, 11) === 'PHP Yar Rpc') {
	/**
	 * Yar_Server导出的API类
	 *
	 * 当请求通过Yar_Client进行远程调用时生效
	 *
	 * @package Global
	 */
	class Service {
		/**
		 * 导出API的api方法
		 *
		 * @access public
		 * @param string $module 应用模块名
		 * @param string $controller 对应模块内的控制器
		 * @param string $action 对应控制器中的动作名
		 * @param mixed $parameters 请求传递的参数
		 * @return string API调用的响应正文
		 */
		public function api($module, $controller, $action, $parameters) {
			$application = new Yaf_Application(ini(), 'common');
			$request = new Yaf_Request_Simple('API', $module, $controller, $action, $parameters);
			$response = $application->bootstrap()->getDispatcher()->dispatch($request);
			return $response->getBody();
		}
	}

	$server = new Yar_Server(new Service());
	// var_dump($server);exit;
	$server->handle();
} else {
	$application = new Yaf_Application(ini(), 'common');
	$application->bootstrap()->run();
}

function ini() {
	if ($_SERVER['env'] === 'dev') {
		$env = 'dev';
	} elseif ($_SERVER['env'] === 'test') {
		$env = 'test';
	} elseif ($_SERVER['env'] === 'stage') {
		$env = 'stage';
	} else {
		$env = 'product';
	}
	return APPLICATION_PATH . "/conf/{$env}/application.ini";
}
