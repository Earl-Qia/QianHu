<?php
namespace app\admin\controller;
use think\Db;
use ensh\Tree;
use think\request;
class Procategory extends Common
{
    protected $model, $categorys , $module,$groupId;
    function _initialize()
    {
        parent::_initialize();
        $this->model = model('procategory');
    }
    public function index()
    {
        $list=$this->model->alias('c')
                ->join('module m','m.id = c.moduleid','LEFT')
                ->field('c.*,m.title as modulename')
                ->where('')
                ->order('sort')
                ->select()
                ->toArray();
        if ($list) {
            foreach ($list as $r) {
                    if ($r['module'] == 'page') {
                        $r['str_manage'] = '<a class="orange" href="' . url('page/edit', array('id' => $r['id'])) . '" title="修改内容"><i class="icon icon-file-text2"></i></a>  ';
                    } else {
                        $r['str_manage'] = '';
                    }
                    $r['str_manage'] .= '<a class="blue" title="添加子栏目" href="' . url('Procategory/add', array('parentid' => $r['id'])) . '"> <i class="icon icon-plus"></i></a>  <a class="green" href="' . url('Procategory/edit', array('id' => $r['id'])) . '" title="修改"><i class="icon icon-pencil2"></i></a>  <a class="red" href="javascript:del(\'' . $r['id'] . '\')" title="删除"><i class="icon icon-bin"></i></a> ';

                    $r['modulename'] = $r['modulename'];

                    $r['dis'] = $r['ismenu'] == 1 ? '<font color="green">显示</font>' : '<font color="red">不显示</font>';
                    $array[] = $r;
            }
            $str = "<tr><td class='visible-lg visible-md'>\$id</td>";
            $str .= "<td class='text-left'>\$spacer<a href='' class='green' title='查看内容'>\$catname </a>&nbsp;</td>";

            $str .= "<td class='visible-lg visible-md'>\$modulename</td><td class='visible-lg visible-md'>\$dis</td>";
            $str .= "<td><input type='text' size='10' data-id='\$id' value='\$sort' class='layui-input list_order'></td><td>\$str_manage</td></tr>";
            $tree = new Tree ($array);
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│  ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $categorys = $tree->get_tree(0, $str);
            $this->assign('categorys', $categorys);
        }
        $this->assign('title','分类列表');
        return $this->fetch();
    }

    public function add(){
        $parentid =	input('parentid/d');
        //模型列表
        $module = db('module')->where('status',1)->field('id,title,name')->select();
        $this->assign('modulelist',$module);
        //父级模型ID
        if($parentid){
            $row=$this->model->where(['id'=>$parentid])->field('moduleid')->find();
            $this->assign('moduleid', $row['moduleid']);
        }else{
            $this->assign('moduleid',0);
        }
        //栏目选择列表
        $array=$this->model->column('catname,id,parentid,child','id');
        $str  = "<option value='\$id' \$selected>\$spacer \$catname</option>";
        $tree = new Tree ($array);
        $categorys = $tree->get_tree(0,$str,$parentid);
        $this->assign('categorys', $categorys);
        $this->assign('title','添加分类');
        return $this->fetch();
    }
    public function insert(){
        $data = $data = Request::instance()->except('file');
        //$data['module'] = $this->module[$data['moduleid']]['name'];
        $data['child'] = isset($data['child'])?1:0;
        $id = $this->model->insert($data);
        if($id) {
            return $this->resultmsg('栏目添加成功!',1,url('index'));
        }else{
            return  $this->resultmsg('栏目添加失败!',0);
        }
    }

    public function edit(){
        $array=$this->model->column('*','id');
        $id = input('id');
        $module = db('module')->field('id,title,name')->select();
        $this->assign('modulelist',$module);
        $row = $array[$id];
        $row['imgUrl'] = imgUrl($row['image']);
        $parentid =	intval($row['parentid']);
        $result = $this->categorys;
        $str  = "<option value='\$id' \$selected>\$spacer \$catname</option>";
        $tree = new Tree ($array);
        $categorys = $tree->get_tree(0, $str,$parentid);
        $this->assign('row',$row);
        $this->assign('categorys', $categorys);
        $this->assign('title','编辑分类');
        return $this->fetch();
    }
    public function Update(){
        $data = $data = Request::instance()->except('file');
        $data['module'] = db('module')->where(array('id'=>$data['moduleid']))->value('name');
        $data['arrparentid'] = $this->get_arrparentid($data['id']);
        $data['child'] = isset($data['child']) ? '1' : '0';
        if (false !==$this->model->update($data)) {
            // $this->repair();
            // $this->repair();
            // savecache('Category');
            return $this->resultmsg('栏目修改成功!',1,url('index'));
        }else{
            return  $this->resultmsg('栏目修改失败!',0);
        }
    }


    public function repair() {
        @set_time_limit(500);
        $this->categorys = $categorys = array();
        $categorys =  $this->model->where("parentid=0")->order('sort ASC,id ASC')->select();
        $this->set_categorys($categorys);
        if(is_array($this->categorys)) {
            foreach($this->categorys as $id => $cat) {
                if($id == 0 || $cat['type']==1) continue;
                $this->categorys[$id]['arrparentid'] = $arrparentid = $this->get_arrparentid($id);
                $this->categorys[$id]['arrchildid'] = $arrchildid = $this->get_arrchildid($id);
                $this->categorys[$id]['parentdir'] = $parentdir = $this->get_parentdir($id);
                $this->model->update(array('parentdir'=>$parentdir,'arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'id'=>$id));
            }
        }

    }
    public function set_categorys($categorys = array()) {
        if (is_array($categorys) && !empty($categorys)) {
            foreach ($categorys as $id => $c) {
                $this->categorys[$c['id']] = $c;
                $r = $this->model->where(array("parentid"=>$c['id']))->Order('sort ASC,id ASC')->select();
                $this->set_categorys($r);
            }
        }
        return true;
    }
    public function get_arrparentid($id, $arrparentid = '') {
        if(!is_array($this->categorys) || !isset($this->categorys[$id])) return false;
        $parentid = $this->categorys[$id]['parentid'];
        $arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
        if($parentid) {
            $arrparentid = $this->get_arrparentid($parentid, $arrparentid);
        } else {
            $this->categorys[$id]['arrparentid'] = $arrparentid;
        }
        return $arrparentid;
    }
    public function get_arrchildid($id) {
        $arrchildid = $id;
        if(is_array($this->categorys)) {
            foreach($this->categorys as $catid => $cat) {
                if($cat['parentid'] && $id != $catid) {
                    $arrparentids = explode(',', $cat['arrparentid']);
                    if(in_array($id, $arrparentids)){
                        $arrchildid .= ','.$catid;
                    }
                }
            }
        }
        return $arrchildid;
    }
    public function get_parentdir($id) {
        if($this->categorys[$id]['parentid']==0){
            return '';
        }
        $arrparentid = $this->categorys[$id]['arrparentid'];
        unset($r);
        if ($arrparentid) {
            $arrparentid = explode(',', $arrparentid);
            $arrcatdir = array();
            foreach($arrparentid as $pid) {
                if($pid==0) continue;
                $arrcatdir[] = $this->categorys[$pid]['catdir'];
            }
            return implode('/', $arrcatdir).'/';
        }
    }


    public function del() {
        $catid = input('param.id');
        $modules = $this->categorys[$catid]['module'];

        $modulesId = $this->categorys[$catid]['moduleid'];

        $scount = $this->model->where(array('parentid'=>$catid))->count();
//        dump($scount);die;
        if($scount){
            $result['info'] = '请先删除其子栏目!';
            $result['status'] = 0;
            return $result;
        }

        $module  = db($modules);

        $arrchildid = $this->categorys[$catid]['arrchildid'];

        if($modules != 'page'){
            $fields = cache($modulesId.'_Field');
            $fieldse=array();
            foreach ($fields as $k=>$v){
                $fieldse[] = $k;
            }
//
            if(in_array('catid',$fieldse)){
                $count = $module->where('catid','in',$arrchildid)->count();
            }else{
                $count = $module->count();
            }
            if($count){
                $result['info'] = '请先删除该栏目下所有数据!';
                $result['status'] = 0;
                return $result;
            }
        }
        $pid = $this->categorys[$catid]['parentid'];
//        dump($pid);die;
        $scat = $this->model->where(array('parentid'=>$pid))->count();
        if($scat==1){
            $this->model->where(array('id'=>$pid))->update(array('child'=>0));
        }
        $this->model->where('id','in',$arrchildid)->delete();
//        dump($this);die;
        $arr=explode(',',$arrchildid);
        foreach((array)$arr as $r){
            if($this->categorys[$r]['module']=='page'){
                $module=db('page');
                $module->delete($r);
            }
        }
        $this->repair();
        savecache('Category');
        $result['info'] = '栏目删除成功!';
        cache('cate', NULL);
        $result['url'] = url('index');
        $result['status'] = 1;
        return $result;
    }

    public function cOrder(){
        $data = input('post.');
        $this->model->update($data);
        $result = ['msg' => '排序成功！', 'code' => 1,'url'=>url('index')];
        savecache('Category');
        cache('cate', NULL);
        return $result;
    }
}