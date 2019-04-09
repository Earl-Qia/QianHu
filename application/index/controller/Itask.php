<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;

class Itask extends Common{
    
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
                'taskname'  =>  $data['name'],
                'taskid'    =>  $taskid,
                'price'  =>  $data['price'],
                'sumOne'  =>  $data['sumOne'],
                'taskTime'  =>  $data['taskTime'],
                'describe'  =>  $data['describe'],
                'logo'  =>  $data['logo'],
                'step'  =>  $data['step'],
            ];
            $itask = db('itask')->insert($array);
            if(!$itask)
                return ['code'=>201,'msg'=>'任务添加失败!'];
            return ['code'=>200,'msg'=>'添加成功!']
        }
        return $this->fetch();
    }
}