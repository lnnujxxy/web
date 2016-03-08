<?php
/**
 * Yaf.app Framework
 *
 * @author xudianyang<120343758@qq.com>
 * @copyright Copyright (c) 2014 (http://www.phpboy.net)
 */
class YarClient {
	private $_client;
	private $_module;
	private $_controller;
	private $_action;
	private $_parameters;

	public function __construct($mvc = array(), $params = array(), $url = '', $opt = array()) {
		if (!$url) {
			$url = Yaf_Registry::get('config')['backend_url']['backend_url'];
		}

		$this->_client = new Yar_Client($url);

		if (is_array($opt) && !empty($opt)) {
			foreach ($opt as $option => $value) {
				$this->_client->setOpt($option, $value);
			}
		}

		if (is_array($mvc) && !empty($mvc)) {
			if (isset($mvc['module'])) {
				$this->_module = $mvc['module'];
			}
			if (isset($mvc['controller'])) {
				$this->_controller = $mvc['controller'];
			}
			if (isset($mvc['action'])) {
				$this->_action = $mvc['action'];
			}
		}

		if (is_array($params) && !empty($params)) {
			$this->_parameters = $params;
		}
	}

	public function __call($method, $parameters = array()) {
		$params = array_merge((array) $this->_parameters, (array) $parameters[0]);
		$result = call_user_func(array($this->_client, $method), $this->_module, $this->_controller, $this->_action, $params);

		if (!$result) {
			return null;
		}
	}

	public function setModule($module) {
		$this->_module = $module;
	}

	public function setController($controller) {
		$this->_controller = $controller;
	}

	public function setAction($action) {
		$this->_action = $action;
	}

	public function setParameters($parameters) {
		$this->_parameters = $parameters;
	}
}