<?php
/**
 +----------------------------------------------------------------------
 * 公共配置
 +---------------------------------------------------------------------- 
 * Copyright (c) 2018 www.yunkehui.com All rights reserved.
 * This software is the proprietary information of www.yunkehui.com
 * Author: 全福 <yeencms@163.com> QQ:29055128
 * 2018-08-12 08:18:18
 */
return [
    'status'	=> [
		'0'		=> ['status'=>0,'statusname'=>'未启用','color'=>'red'],
		'9'		=> ['status'=>9,'statusname'=>'已启用','color'=>'green'],
	],
	'logic'	=> [
		'0'		=> ['status'=>0,'statusname'=>'否','color'=>'gray'],
		'1'		=> ['status'=>1,'statusname'=>'是','color'=>'green'],
	],
	'gender'  =>  [
        '0'  =>  ['status'=>0,'statusname'=>'女','color'=>'#FF5722'],
        '1'  =>  ['status'=>1,'statusname'=>'男','color'=>'#009688'],
    ],
];