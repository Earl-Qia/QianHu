<?php
namespace app\common\taglib;
use think\template\TagLib;
class Loop extends TagLib{
    
	//定义标签列表
	//标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
	protected $tags   =  [
		//变量名assign,条件status,排序order,查询数量limit
		'module'      => ['attr' => 'assign,status,categoryid,attribute,fields,order,limit,showpage'],
	];
	
	//模型数据循环
	public function tagModule($params, $content){
		//变量名
		$assign = empty($params['assign']) ? 'row' : $params['assign'];
		unset($params['assign']);
		$parse = '
			<?php
				$params =\''.json_encode($params).'\';
				$params = json_decode($params, true);
				//模型名
				if( isset($params["modulename"]) ){
					$modulename = $params["modulename"];
					unset($params["modulename"]);
				}else{
					$modulename = "module";
				}
				//实例化
				$module = model($modulename);
				//字段
				if( isset($params["fields"]) ){
					$module->field($params["fields"]);
					unset($params["fields"]);
				}
				//排序
				if( isset($params["order"]) ){
					$module->order($params["order"]);
					unset($params["order"]);
				}
				//查询条数
				$limit = 20;
				if( isset($params["limit"]) ){
					$limit = $params["limit"];
					$module->limit($params["limit"]);
					unset($params["limit"]);
				}
				//是否分页
				$showpage = false;
				if( isset($params["showpage"]) ){
					$showpage = $params["showpage"] ? true : false;
					unset($params["showpage"]);
				}
				//条件
				//$where = $params;
				if( isset($params["status"]) ){
				    $module->where($params["status"]);
				}
				//查询数据
				if($showpage){
					$data = $module->paginate($limit);
					$pagination = $data->render();
				}else{
					$data = $module->select();
				}
				
				$__LIST__ = $data;
			?>';
		$parse .= '{volist name="__LIST__" id="' . $assign . '"}';
		$parse .= $content;
		$parse .= '{/volist}';
		//$this->tpl->assign("abc", '{__LIST__}');
		return $parse;
	}
	
	
}