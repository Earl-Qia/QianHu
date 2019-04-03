<?php
namespace app\admin\controller;
use think\Db;
use think\request;
class Cad extends Common
{
    public function initialize(){
        parent::initialize();
    }
    //广告列表
    public function index(){
        if(Request::instance()->isAjax()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table(config('database.prefix') . 'cad')->alias('a')
                ->where('a.mtitle', 'like', "%" . $key . "%")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();                                          
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function add(){
        if(Request::instance()->isAjax()) {
            //构建数组
            $data = Request::instance()->except('file');
            $data['addtime'] = time();
            db('cad')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '广告添加成功!';
            cache('adList', NULL);
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('ad_type')->order('sort')->select();
            $this->assign('adtypeList',json_encode($adtypeList,true));

            $this->assign('title',lang('add').lang('ad'));
            $this->assign('info','null');
            $this->assign('selected','null');
            return $this->fetch('form');
        }
    }
    public function edit(){
        if(Request::instance()->isAjax()) {
            $data = Request::instance()->except('file');
            db('cad')->update($data);
            $result['code'] = 1;
            $result['msg'] = '广告修改成功!';
            cache('adList', NULL);
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('ad_type')->order('sort')->select();
            $cadid=input('cadid');
            $adInfo=db('cad')->where(array('cadid'=>$cadid))->find();
            $this->assign('adtypeList',json_encode($adtypeList,true));
            $this->assign('info',json_encode($adInfo,true));
            $this->assign('title',lang('edit').lang('ad'));
            return $this->fetch('form');
        }
    }
    //设置广告状态
    public function editState(){
        $id=input('post.id');
        $state=input('post.state');
        if(db('cad')->where('cadid='.$id)->update(['state'=>$state])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function adOrder(){
        $ad=db('ad');
        $data = input('post.');
        if($ad->update($data)!==false){
            cache('adList', NULL);
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function del(){
        db('cad')->where(array('cadid'=>input('cadid')))->delete();
        cache('adList', NULL);
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delall(){
        $map['cadid']=array('in',input('param.ids/a'));
        db('cad')->where($map)->delete();
        cache('adList', NULL);
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /***************************位置*****************************/
    //位置
    public function type(){
        if(Request::instance()->isAjax()) {
            $key = input('key');
            $this->assign('testkey', $key);
            $list = db('ad_type')->where('name', 'like', "%" . $key . "%")->order('sort')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }
    public function typeOrder(){
        $ad_type=db('ad_type');
        $data = input('post.');
        if($ad_type->update($data)!==false){
            return $result = ['msg' => '操作成功！','url'=>url('type'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function addType(){
        if(Request::instance()->isAjax()) {
            db('ad_type')->insert(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位保存成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $this->assign('title',lang('add').lang('ad').'位');
            $this->assign('info','null');
            return $this->fetch('typeForm');
        }
    }
    public function editType(){
        if(Request::instance()->isAjax()) {
            db('ad_type')->update(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位修改成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $type_id=input('param.type_id');
            $info=db('ad_type')->where('type_id',$type_id)->find();
            $this->assign('title',lang('edit').lang('ad').'位');
            $this->assign('info',json_encode($info,true));
            return $this->fetch('typeForm');
        }
    }
    public function delType(){
        $map['type_id'] = input('param.type_id');
        db('ad_type')->where($map)->delete();//删除广告位
        db('ad')->where($map)->delete();//删除该广告位所有广告
        return ['code'=>1,'msg'=>'删除成功！'];
    }
}