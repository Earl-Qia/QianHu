<?php
/**
 +----------------------------------------------------------------------
 * 模型配置
 +---------------------------------------------------------------------- 
 * Copyright (c) 2018 www.yunkehui.com All rights reserved.
 * This software is the proprietary information of www.yunkehui.com
 * Author: 全福 <yeencms@163.com> QQ:29055128
 * 2018-08-12 08:18:18
 */
return [
	'water'	=> [
		0		=> ['status'=>0,'statusname'=>'不开启','color'=>'blue'],
		1		=> ['status'=>1,'statusname'=>'左上角','color'=>'green'],
		2		=> ['status'=>2,'statusname'=>'上居中','color'=>'green'],
		3		=> ['status'=>3,'statusname'=>'右上角','color'=>'green'],
		4		=> ['status'=>4,'statusname'=>'左居中','color'=>'green'],
		5		=> ['status'=>5,'statusname'=>'正居中','color'=>'green'],
		6		=> ['status'=>6,'statusname'=>'右居中','color'=>'green'],
		7		=> ['status'=>7,'statusname'=>'左下角','color'=>'green'],
		8		=> ['status'=>8,'statusname'=>'下居中','color'=>'green'],
		9		=> ['status'=>9,'statusname'=>'右下角','color'=>'green'],
		10		=> ['status'=>10,'statusname'=>'随机位置','color'=>'green'],
	],
	'thumb'	=> [
		0		=> ['status'=>0,'statusname'=>'关闭','color'=>'red'],
		1		=> ['status'=>1,'statusname'=>'等比例缩放','color'=>'green'],
		2		=> ['status'=>2,'statusname'=>'缩放后填充','color'=>'green'],
		3		=> ['status'=>3,'statusname'=>'居中裁剪','color'=>'green'],
		4		=> ['status'=>4,'statusname'=>'左上角裁剪','color'=>'green'],
		5		=> ['status'=>5,'statusname'=>'右下角裁剪','color'=>'green'],
		6		=> ['status'=>6,'statusname'=>'固定尺寸缩放','color'=>'green'],
	],
	'allowimage'	=> ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
	'allowfiles'	=> ['png', 'jpg', 'jpeg', 'gif', 'bmp', 
					'flv', 'swf', 'mkv', 'avi', 'rm', 'rmvb', 'mpeg', 'mpg', 
					'ogg', 'ogv', 'mov', 'wmv', 'mp4', 'webm', 'mp3', 'wav', 'mid', 'm4a',
					'rar', 'zip', 'tar', 'gz', '7z', 'bz2', 'cab', 'iso', 
					'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'md', 'xml', 'qsv'
					],
	
];



