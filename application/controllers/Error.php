<?php
/**
 * 错误处理
 *
 * @author lnnujxxy@gmail.com
 * @version  1.
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction($exception) {
		header('Content-type:application/json; charset=utf-8');
		$res = [
			'code' => -999,
			'errno' => intval($exception->getCode()),
			'type' => get_class($exception),
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
		];

		echo json_encode($res);
		//error_log(json_encode($res));
		$this->exceptionHandler($exception);

		return false;
	}

	private function exceptionHandler($exception) {
		// these are our templates
		$traceline = "#%s %s(%s): %s(%s)";
		$msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

		// alter your trace as you please, here
		$trace = $exception->getTrace();
		foreach ($trace as $key => $stackPoint) {
			// I'm converting arguments to their type
			// (prevents passwords from ever getting logged as anything other than 'string')
			$trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
		}

		// build your tracelines
		$result = array();
		foreach ($trace as $key => $stackPoint) {
			$result[] = sprintf(
				$traceline,
				$key,
				$stackPoint['file'],
				$stackPoint['line'],
				$stackPoint['function'],
				implode(', ', $stackPoint['args'])
			);
		}
		// trace always ends with {main}
		$result[] = '#' . ++$key . ' {main}';

		// write tracelines into main template
		$msg = sprintf(
			$msg,
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			implode("\n", $result),
			$exception->getFile(),
			$exception->getLine()
		);

		// log or echo as you please
		error_log($msg);
	}
}
