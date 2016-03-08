<?php
/**
 * Payment支付
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */

Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/lib/WxPay.Api.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/example/WxPay.NativePay.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/lib/WxPay.Notify.php");

Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/aop/request/AlipayTradePrecreateRequest.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/aop/request/AlipayTradeQueryRequest.php");

Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/function.inc.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/AopSdk.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/HttpRequst.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/config.php");

Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/alipay_fuwuchuang/f2fpay/F2fpay.php");

class Payment {
	/**
	 * 微信支付
	 */
	public static function weixinPay($params) {
		$notify = new NativePay();

		$input = new WxPayUnifiedOrder();
		$input->SetBody($params['body']);
		// $input->SetAttach($params['attach']);
		$input->SetOut_trade_no($params['trade_no']);
		$input->SetTotal_fee($params['pay_price']);
		$input->SetTime_start(date("YmdHis"));
		$ttl = $params['ttl'] > 0 ? $params['ttl'] : Constant::BOOKING_TTL;
		$input->SetTime_expire(date("YmdHis", time() + $ttl));

		if ($params['openid']) {
			$input->SetOpenid($params['openid']);
		}

		$input->SetNotify_url($params['notify_url']);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($params['product_id']);
		$result = $notify->GetPayUrl($input);
		// var_dump($result);exit;
		$result['trade_no'] = $params['trade_no'];
		return $result;
	}

	public static function weixinJsPay($params) {
		$tools = new JsApiPay();
		// $openId = $tools->GetOpenid();

		$input = new WxPayUnifiedOrder();
		$input->SetBody($params['body']);
		// $input->SetAttach("test");
		$input->SetOut_trade_no($params['trade_no']);
		$input->SetTotal_fee($params['pay_price']);
		$input->SetTime_start(date("YmdHis"));
		$ttl = $params['ttl'] > 0 ? $params['ttl'] : Constant::BOOKING_TTL;
		$input->SetTime_expire(date("YmdHis", time() + $ttl));
		// $input->SetGoods_tag("test");
		$input->SetNotify_url($params['notify_url']);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($params['openid']);
		$order = WxPayApi::unifiedOrder($input);
		// var_dump($order);exit;
		$jsApiParameters = $tools->GetJsApiParameters($order);
		return $jsApiParameters;
	}

	/**
	 * 支付宝支付
	 */
	public static function aliPay($params) {
		$f2fpay = new F2fpay();
		$price = number_format($params['pay_price'] / 100, 2, '.', '');
		$response = $f2fpay->qrpayChanjian($params['trade_no'], $price, $params['subject'], $params['notify_url']);
		return $response;
	}

	/**
	 * 查询订单支付
	 */
	public static function queryOrder($tradeNo) {
		// $starttime = microtime(true);
		//先查询微信
		$queryOrderInput = new WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($tradeNo);
		$result = WxPayApi::orderQuery($queryOrderInput);
		if ($result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS"
			&& $result["trade_state"] == "SUCCESS") {
			return ['method' => Constant::PAY_METHOD_WEIXIN, 'weixin_id' => $result['transaction_id']];
		}
		// var_dump(microtime(true) - $starttime);
		//再查询支付宝
		$f2fpay = new F2fpay();
		$response = $f2fpay->query($tradeNo);
		if ($response->alipay_trade_query_response->code == 10000) {
			// var_dump($response->alipay_trade_query_response->trade_no);
			return ['method' => Constant::PAY_METHOD_ALIPAY, 'alipay_id' => $response->alipay_trade_query_response->trade_no];
		}
		// var_dump(microtime(true) - $starttime);
		return false;
	}
}
