<?php
/**
 * Rpc 控制器
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
class RpcClientController extends Yaf_Controller_Abstract {
	public function testYarApiAction() {
		$client = new YarClient(
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'hello',
			),
			array('yaf')
		);
		$data = $client->api();
		return false;
	}
}
