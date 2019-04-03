<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use ensh\Tree;
class Category extends Model{
    public function  get_category_tree($where='',$disabled=1,$select_id=0){
    	
    	$list=$this->where($where)->column('catname,id,parentid,child','id');
    	if ($disabled) {
    		foreach ($list as $k => $v) {
    			$list[$k]['disabled']=$v['child'] ?'disabled':'';
	    	}
    	}
        $tree = new Tree($list);
		$str = "<option value='\$id' \$disabled \$selected>\$spacer\$catname</option>"; //生成的形式
		return $tree->get_tree(0,$str, $select_id);
    }
}