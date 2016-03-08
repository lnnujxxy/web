<?php
/**
 * RpcServer 控制器
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
use JsonRPC\Server;

class RpcserverController extends Yaf_Controller_Abstract {
	public function callbackAction() {

		$server = new Server;

		$server->register('addition', function ($a, $b) {
			return $a + $b;
		});

		$server->register('random', function ($start, $end) {
			return mt_rand($start, $end);
		});

		echo $server->execute();

		return false;
	}
}
