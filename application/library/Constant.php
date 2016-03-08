<?php

class Constant {
	const PROJECT_NAME = 'shiyuehehu';

	const MODULE_NAME_ANDROID = 'An'; //android 模块名
	const MODULE_NAME_IOS = 'Ios'; //Ios 模块名

	const DEVICE_ANDROID = 'android'; //android 设备
	const DEVICE_IOS = 'ios'; //ios 设备

	const ENV_TEST = 'test'; //测试环境
	const ENV_PRODUCT = 'product'; //线上环境

	const SMS_SIGN_LUOSIMAO = '【十月呵护】'; //短信签名

	const DEPART_ERKE = 1; //儿科
	const DEPART_FUCHANKE = 2; //妇产科

	const SMS_TYPE_WEIXIN = 'weixin';

	const BOOKING_TTL = 7200; //支付有效时间

	const REDIS_INSTANCE_DEFAULT = 'default'; //redis实例

	const ORDER_STATUS_UNPAY = 0; //下单待支付
	const ORDER_STATUS_UNPAY_FINISH = 5; //下单未支付 10分钟后将未支付订单过期
	const ORDER_STATUS_PAY = 10; //下单已支付,待医生确认
	const ORDER_STATUS_DOCTOR_CONFIRM = 20; //医生确认约见
	const ORDER_STATUS_UNCOMMENT = 30; //已见面待评价
	const ORDER_STATUS_COMMENT = 40; //已完成已评价
	const ORDER_STATUS_DOCTOR_NOCONFIRM = 50; //医生确认不见面
	const ORDER_STATUS_USER_ABSENT = 60; //用户未出席
	const ORDER_STATUS_DOCTOR_ABSENT = 70; //医生未出席已退款
	const ORDER_STATUS_KEFU_REFUND = 80; //客服取消已订单退款
	const ORDER_STATUS_KEFU_NOREFUND = 90; //客服取消订单不给用户退款
	const ORDER_STATUS_DOCTOR_NOCONFIRM_REFUND = 100; //医生无法见面已退款
	const ORDER_STATUS_DOCTOR_ABSENT_REFUND = 110; //医生无法出席已退款
	const ORDER_STATUS_KEFU_REFUNDING = 120; //客服取消订单退款

	const ORDER_CATE_PENDING_CONFIRM = 1; //待确认
	const ORDER_CATE_IN_PROGRESS = 2; //进行中
	const ORDER_CATE_FINISH = 3; //已完成

	const PLAN_STATE_BOOKING = 2; //已被预约
	const PLAN_STATE_COMMON = 1; //正常
	const PLAN_STATE_CANCEL = 0; //已取消
}
