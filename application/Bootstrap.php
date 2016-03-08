<?php
/**
 * @name Bootstrap
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Yaf_Bootstrap_Abstract {
	//把配置保存起来
	public function _initConfig() {
		$configs = Yaf_Application::app()->getConfig()->toArray();

		$this->loadConfigEnv($_SERVER['env']);

		if ($_SERVER['OSS_ID']) {
			$configs['application']['oss']['accessid'] = $_SERVER['OSS_ID'];
		}

		if ($_SERVER['OSS_KEY']) {
			$configs['application']['oss']['accesskey'] = $_SERVER['OSS_KEY'];
		}

		if ($_SERVER['DB_HOST']) {
			$configs['db'][0]['host'] = $_SERVER['DB_HOST'];
		}

		if ($_SERVER['DB_USER']) {
			$configs['db'][0]['user'] = $_SERVER['DB_USER'];
		}

		if ($_SERVER['DB_PWD']) {
			$configs['db'][0]['pwd'] = $_SERVER['DB_PWD'];
		}

		foreach ($configs['redis'] as $key => $item) {
			foreach ($item as $index => $config) {
				if ($_SERVER['REDIS_HOST']) {
					$config['host'] = $_SERVER['REDIS_HOST'];
				}

				if ($_SERVER['REDIS_AUTH']) {
					$config['auth'] = $_SERVER['REDIS_AUTH'];
				}

				if ($_SERVER['REDIS_PORT']) {
					$config['port'] = $_SERVER['REDIS_PORT'];
				}

				$configs['redis'][$key][$index] = $config;
			}
		}

		Yaf_Registry::set('config', $configs);
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		// //注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}

	public function _initView(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的view控制器，例如smarty,firekylin
	}

	/**
	 * @param Dispatcher $dispatcher
	 */
	public function _initComposerAutoload(Yaf_Dispatcher $dispatcher) {
		$autoload = APPLICATION_PATH . '/application/thirdparty/vendor/autoload.php';
		if (file_exists($autoload)) {
			Yaf_Loader::import($autoload);
		}
	}

	private function loadConfigEnv($env) {
		if (file_exists(APPLICATION_PATH . "/conf/{$env}/config.env")) {
			$ini = parse_ini_file(APPLICATION_PATH . "/conf/{$env}/config.env");
			foreach ($ini as $key => $value) {
				$_SERVER[$key] = $value;
			}
		}
	}
}
