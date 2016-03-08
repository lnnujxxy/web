<?php
/**
 * 预约控制器
 *
 * @authaesr lnnujxxy@gmail.com
 * @version  1.0
 */
class TestController extends BaseTestController {

	public function IndexAction() {
		$content = ['test' => 'andriod'];
		return $this->output(Msg::SUCC_NO_COMMON, Msg::SUCC_MSG_COMMON, $content);
	}

}