<?php
/**
 +----------------------------------------------------------------------
 * 订单
 +---------------------------------------------------------------------- 
 * Copyright (c) 2018 www.yunkehui.com All rights reserved.
 * This software is the proprietary information of www.yunkehui.com
 * Author: 全福 <yeencms@163.com> QQ:29055128
 * 2018-08-12 08:18:18
 */
return [
    'status'	=> [
		'0'		=> ['status'=>0,'statusname'=>'未支付','color'=>'red'],
		//'1'		=> ['status'=>1,'statusname'=>'已支付','color'=>'red'],
		//'5'		=> ['status'=>5,'statusname'=>'已取消','color'=>'blue'],
		'9'		=> ['status'=>9,'statusname'=>'已支付','color'=>'green'],
	],
	'sys_member_price'	=> 999,
	//支付方式
	'payment'	=> [
		'3'		=> ['status'=>3,'statusname'=>'微信支付','color'=>'green'],
		'4'		=> ['status'=>4,'statusname'=>'微信转账','color'=>'green'],
		'5'		=> ['status'=>5,'statusname'=>'支付宝转账','color'=>'green'],
		'6'		=> ['status'=>6,'statusname'=>'银行转账','color'=>'green'],
	],
	
];