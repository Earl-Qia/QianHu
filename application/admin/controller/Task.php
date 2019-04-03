<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;
use app\admin\model\Task as Tk;
class Task extends Common{
    protected  $model;
    public function _initialize(){
        parent::_initialize();
        $this->model = model('task');
    }
    //微任务列表
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('ltask')->alias('lt')
                ->join(config('database.prefix').'task t','t.taskid = lt.taskid','left')
                // ->join(config('database.prefix').'invite it','t.taskid = it.taskid','left')
                ->join(config('database.prefix').'users u','t.userid = u.id','left')
                ->field('t.*,lt.*,u.username,u.mobile')
                ->order('t.createtime desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }else{
            return $this->fetch();
        }
    }
    //悬赏招聘列表
    public function invite(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('invite')->alias('it')
                //->join(config('database.prefix').'ltask lt','t.taskid = lt.taskid','left')
                ->join(config('database.prefix').'task t','it.taskid = t.taskid','left')
                ->join(config('database.prefix').'users u','t.userid = u.id','left')
                ->field('t.*,it.*,u.username,u.mobile')
                ->order('t.createtime desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }else{
            return $this->fetch();
        }
    }
    //User<->Task
    public function taskUser(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('user_task')->alias('ut')
                //->join(config('database.prefix').'ltask lt','t.taskid = lt.taskid','left')
                ->join(config('database.prefix').'task t','ut.taskid = t.taskid','left')
                ->join(config('database.prefix').'users u','ut.userid = u.id','left')
                ->field('t.*,ut.*,u.username,u.mobile')
                ->order('t.createtime desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $db = ($v['style']==0)?'ltask':'invite';
                $list['data'][$k]['taskName'] = db($db)->where('taskid',$v['taskid'])->value('taskname');
                $list['data'][$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }else{
            return $this->fetch('taskUser');
        }
    }
    //User<->Task---End

    //Boss<->Task
    //雇主发布的任务
    public function taskBoss(){
        if(request()->isPost()){
            $key=input('post.key');
            $id = Session::pull('id');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('task')->alias('t')
                ->where('t.userid',$id)
                ->field('t.*')
                ->order('t.createtime desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $db = ($v['style']==0)?'ltask':'invite';
                $list['data'][$k]['taskName'] = db($db)->where('taskid',$v['taskid'])->value('taskname');
                $list['data'][$k]['describe'] = db($db)->where('taskid',$v['taskid'])->value('describe');
                $list['data'][$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
                $list['data'][$k]['auditortime'] = date('Y-m-d H:i:s',$v['auditortime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }else{
            Session::set('id',input('get.id'));
            return $this->fetch('taskBoss');
        }
    }
    //Boss<->Task---End
   
     //设置审核状态
    public function taskState(){
        $taskid=input('post.taskid');
        $state=input('post.state');
        $auditortime = time();
        if(Tk::where('taskid='.$taskid)->update(['state'=>$state,'auditortime'=>$auditortime])!==false){
            $result['code'] = 1;
            $result['msg'] = '设置成功!';
        }else{
            $result['code'] = 0;
            $result['msg'] = '设置失败!';
        }
        return $result;
    }
    public function listDel(){
        $id = input('post.id');
        $model = $this->model;
        $model->where(array('id'=>$id))->delete();//转入回收站
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delAll(){
        // $map[] =array('id','in',input('post.ids/a'));
        $map = input('post.ids/a');
        foreach ($map as $key => $value) {
            Pro::destroy($value);
            $map = false;
        }
        if(!$map){
            $result['code'] = 1;
            $result['msg'] = '删除成功！';
            $result['url'] = url('index',array('catid'=>input('post.catid')));
        }else{
            $result['code'] = 0;
            $result['msg'] = '删除失败！';
            $result['url'] = url('index',array('catid'=>input('post.catid')));
        }
        return $result;
    }
    public function listorder(){
        $model = $this->model;
        $catid = input('catid');
        $data = input('post.');
        $model->update($data);
        $result = ['msg' => '排序成功！','url'=>url('index',array('catid'=>$catid)), 'code' => 1];
        return $result;
    }
    public function delImg(){
        if(!input('post.url')){
            return ['code'=>0,'请指定要删除的图片资源'];
        }
        $file = ROOT_PATH.__PUBLIC__.input('post.url');
        if(file_exists($file) && trim(input('post.url'))!=''){
            is_dir($file) ? dir_delete($file) : unlink($file);
        }
        if(input('post.id')){
            $picurl = input('post.picurl');
            $picurlArr = explode(':',$picurl);
            $pics = substr(implode(":::",$picurlArr),0,-3);
            $model = $this->model;
            $map['id'] =input('post.id');
            $model->where($map)->update(array('pics'=>$pics));
        }
        $result['msg'] = '删除成功!';
        $result['code'] = 1;
        return $result;
    }
    public function getRegion(){
        $Region=db("region");
        $map['pid'] = input("pid");
        $list=$Region->where($map)->select();
        return $list;
    }

}