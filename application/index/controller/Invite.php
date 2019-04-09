<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;

class Invite extends Common{
    
    public function index(){
        if(Request::instance()->isAjax()) {
            $data = input('post.');
            $taskid = db('task')->insertGetId([
                'style' => '0',
                'userid' => Session::get('userid'),
                'createtime' => time()
            ]);
            if(!$taskid)
                return ['code'=>201,'msg'=>'任务添加失败!'];
            $array = [
                'style' =>  $data['style'],
                'welfare' => $data['welfare'],
                'taskname'  =>  $data['name'],
                'taskid'    =>  $taskid,
                'sumOne'  =>  $data['sumOne'],
                'price'  =>  $data['price'],
                'taskTime'  =>  $data['taskTime'],
                'describe'  =>  $data['describe'],
                'logo'  =>  $data['logo'],
                'step'  =>  $data['step'],
                'wages' =>  $data['wages'],
                'bounty' =>  $data['bounty'],
                'site'  =>  $data['site']
            ];
            $itask = db('itask')->insert($array);
            if(!$itask)
                return ['code'=>201,'msg'=>'任务添加失败!'];
            return ['code'=>200,'msg'=>'添加成功!']
        }
        return $this->fetch();
    }
}