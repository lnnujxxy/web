<?php
/**
 * 返回提示或文本
 *
 * @author lnnujxxy@gmail.com
 * @version  1.0
 */
class Msg {
	//文本
	const TEXT_VCODE = '您的手机验证码为：$vcode，请不要把验证码泄露给其他人。';
	const SMS_SEND_CONTENT_PAY = '孕妈$user，您已成功预约$month月$day日$hospital上门产检，将会在$prevMonth月$prevDay日跟您进行电话确认，请您保持电话畅通。';
	const SMS_SEND_CONTENT_NOT_PAY = '孕妈$user，请在30分钟内支付上门产检服务费以完成预约。关注公众号十月产检，在个人中心支付。预约信息：$month月$day日$hospital【十月呵护】';
	const SMS_SEND_SERVICE_START = '孕妈$user，$hospitalName上门产检车已经向您的地址出发，请您准备好相关病历，耐心等候。';
	const SMS_SEND_SERVICE_ARRIVE = '孕妈$user，$hospitalName上门产检车已经到达您的地址，请您到车上进行产检。';
	const SMS_SEND_SERVICE_REFUND = '孕妈$user，您的上门产检$money元退款已经退回您的账户，请注意查收。';

	const TEXT_PAYMENT_BODY = '约个医生';

	//返回提示
	const SUCC_NO_COMMON = 0;
	const SUCC_MSG_COMMON = '成功';
	const ERR_NO_HAVE_FINISH_PAY = 10000;
	const ERR_MSG_HAVE_FINISH_PAY = '该订单已经支付完成';
	const ERR_NO_PARAM = 1000;
	const ERR_MSG_PARAM = '参数错误';
	const ERR_NO_LOGIN_FAIL = 1001;
	const ERR_MSG_LOGIN_FAIL = '用户名或密码错误';
	const ERR_NO_LOGIN_EXPIRE = 1002;
	const ERR_MSG_LOGIN_EXPIRE = '登录已经失效，请重新登录';
	const ERR_NO_ADD_ORDER = 1003;
	const ERR_MSG_ADD_ORDER = '订单增加失败';
	const ERR_NO_PAYMENT_WEIXIN_FAIL = 1004;
	const ERR_MSG_PAYMENT_WEIXIN_FAIL = '微信支付失败';
	const ERR_NO_BIND_PAYMENT_FAIL = 1005;
	const ERR_MSG_BIND_PAYMENT_FAIL = '设置完成支付失败';
	const ERR_NO_WEIXIN_UNIFIEDORDER_FAIL = 1006;
	const ERR_MSG_WEIXIN_UNIFIEDORDER_FAIL = '微信下单失败';
	const ERR_NO_INSERT_PAYMENT_FAIL = 1007;
	const ERR_MSG_INSERT_PAYMENT_FAIL = '写入支付表失败';
	const ERR_NO_ADD_COMMENT_FAIL = 1008;
	const ERR_MSG_ADD_COMMENT_FAIL = '加入评论失败';
	const ERR_NO_HAVE_UNFINISH_ORDER = 1009;
	const ERR_MSG_HAVE_UNFINISH_ORDER = '您跟该医生有未完成预约，无需重复预约';
	const ERR_NO_WAIT_ORDER = 1010;
	const ERR_MSG_WAIT_ORDER = '您的订单未成功下单支付，请10分钟后再试。';
	const ERR_NO_MB_ERROR = 1036;
	const ERR_MSG_MB_ERROR = '手机号输入错误';
	const ERR_NO_VCODE_ERROR = 1037;
	const ERR_MSG_VCODE_ERROR = '请输入验证码';
	const ERR_NO_PLAN_USE = 1038;
	const ERR_MSG_PLAN_USE = '该时间已被占用';

	const ERR_NO_AK_INVALID = 40001;
	const ERR_MSG_AK_INVALID = "无效AK";
	const ERR_NO_AK_EXPIRE = 40002;
	const ERR_MSG_AK_EXPIRE = "AK已过期";
	const ERR_NO_RK_EXPIRE = 40003;
	const ERR_MSG_RK_EXPIRE = "RK 失效或过期";
	const ERR_NO_USER_NOEXIST = 40004;
	const ERR_MSG_USER_NOEXIST = "用户名不存在";
	const ERR_NO_USER_PW_NOMATH = 40000;
	const ERR_MSG_USER_PW_NOMATH = "用户名或密码错误";
	const ERR_NO_PW_MAX_LIMIT = 40005;
	const ERR_MSG_PW_MAX_LIMIT = "密码错误次数太多，请联系管理员";
	const ERR_NO_NOLOGIN = 40006;
	const ERR_MSG_NOLOGIN = "请登录";
	const ERR_NO_SEND_SMS_VCODE = 50000;
	const ERR_MSG_SEND_SMS_VCODE = '发送验证码失败';
	const ERR_NO_VCODE_INVALID = 50001;
	const ERR_MSG_VCODE_INVALID = '验证码无效';
	const ERR_NO_BIND_MB_FAIL = 50002;
	const ERR_MSG_BIND_MB_FAIL = '绑定手机号失败';
	const ERR_NO_MB_HAVE_BIND = 50003;
	const ERR_MSG_MB_HAVE_BIND = '该手机已经被使用过';
	const ERR_NO_ADD_ADDRESS_FAIL = 50004;
	const ERR_MSG_ADD_ADDRESS_FAIL = '增加该地址失败';
	const ERR_NO_GET_ACCESS_TOKEN_FAIL = 50005;
	const ERR_MSG_GET_ACCESS_TOKEN_FAIL = '获取微信access_token失败';
	const ERR_NO_IP_REQUEST_MUCH = 50006;
	const ERR_MSG_IP_REQUEST_MUCH = '该IP发送短信过多';
	const ERR_NO_MB_REQUEST_MUCH = 50007;
	const ERR_MSG_MB_REQUEST_MUCH = '该手机号发送短信过多';
	const ERR_NO_GET_WEIXIN_INFO_FAIL = 50012;
	const ERR_MSG_GET_WEIXIN_INFO_FAIL = '获取微信用户失败';
}
