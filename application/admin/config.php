<?php
return [
    'pathinfo_depr'          => '/',
    //开启模板布局
	'template'  =>  [
		// 'layout_on'     =>  true,
		// 'layout_name'   =>  'layout',
		
		// // 模板引擎类型 支持 php think 支持扩展
  //       'type'         => 'Think',
  //       // 模板路径
  //       'view_path'    => '',
  //       // 模板后缀
  //       'view_suffix'  => 'html',
  //       // 模板文件名分隔符
  //       'view_depr'    => DS,
  //       // 模板引擎普通标签开始标记
  //       'tpl_begin'    => '{',
  //       // 模板引擎普通标签结束标记
  //       'tpl_end'      => '}',
		// 预先加载的标签库
		'taglib_pre_load'     =>    'app\common\taglib\Ensh',
	],
];