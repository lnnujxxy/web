<?php
/**
 * RpcServer 控制器
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
use JsonRPC\Client;

class RpcClientController extends Yaf_Controller_Abstract {
	public function callbackAction() {

		$client = new Client('http://wenzhen.dev.shiyuehehu.com/rpcServer/callback');
		$result = $client->execute('addition', [3, 5]);

		var_dump($result);
		return false;
	}

	public function testYarApiAction() {
		$client = new YarClient(
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'hello',
			),
			array('yaf'),
			'http://wenzhen.dev.shiyuehehu.com/'
		);
		$data = $client->api();

		return false;
	}
}
