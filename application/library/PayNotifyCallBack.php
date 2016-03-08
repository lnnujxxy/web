<?php
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/lib/WxPay.Api.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/example/WxPay.NativePay.php");
Yaf_Loader::import(APPLICATION_PATH . "/application/thirdparty/WxpayAPI_php_v3/lib/WxPay.Notify.php");

class PayNotifyCallBack extends WxPayNotify {
	//查询订单
	public function Queryorder($transaction_id) {
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		if (array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS") {

			Utility::log('pay', '微信返回支付成功');
			return true;
		}

		Utility::log('pay', '微信返回支付失败');
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg) {
		Utility::log('pay', 'data1 = ' . print_r($data, true));
		$notfiyOutput = array();

		if (!array_key_exists("transaction_id", $data)) {
			Utility::log('pay', "$msg = 输入参数不正确");
			return false;
		}
		//查询订单，判断订单真实性
		if (!$this->Queryorder($data["transaction_id"])) {
			Utility::log('pay', "$msg = 订单查询失败");
			return false;
		}
		Utility::log('pay', '11111');
		$paymentModel = new Dao_PaymentModel();
		Utility::log('pay', '333333');
		$row = $paymentModel->getPaymentByTradeNo($data['out_trade_no']);
		Utility::log('pay', 'row = ' . print_r($row, true));
		//判断是否已经更新成功
		if ($row['ispay']) {
			Utility::log('pay', '已经回调过了');
			return true;
		}

		$orderModel = new Dao_OrderModel();
		$args = [
			'order_status' => 10,
		];
		$where = " order_id = " . $row['order_id'];
		if (!$orderModel->updateFields($where, $args)) {
			Utility::log('pay', '更新order数据失败');
			return false;
		}

		Utility::log('pay', '2222');
		$fields = [
			'weixin_id' => $data["transaction_id"],
			'ispay' => 1,
			'paytime' => date('Y-m-d H:i:s'),
		];
		Utility::log('pay', 'fields = ' . print_r($fields, true));
		$where = " trade_no = " . $data['out_trade_no'];
		Utility::log('pay', $where);
		if (!$paymentModel->updateFields($where, $fields)) {
			Utility::log('pay', '更新payment数据失败');
			return false;
		}

		Utility::log('pay', '通知成功');
		return true;
	}
}
