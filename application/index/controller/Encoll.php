<?php
namespace app\index\controller;
use app\index\model\Users as UsersModel;
use app\index\model\Task as Tk;
use think\Db;
use think\Request;
use think\View;

class Encoll extends Common{
    public function _initialize()
    {

    }
    /*public function index(){

        return $this->fetch();
    }*/
    //注册用户
    public function add(){
        if(request()->isPost()){
            $user = new UsersModel();
            $list =
                [
                    'id'=>'',
                    'username' => '',
                    'password' => '6666666',
                    'district' => '',
                    'province' => '',
                    'mobile' => '18813095444',
                    'level' => '',
                    'email' => '',
                    'city' => '',
                    'qq' => '',
                    'sex' => '',
                    'reg_time' => time()
                ];
            $res = db('users')->insertGetId($list);
            $wa = db('wallet')->insertGetId(['userid'=>$res]);
            if ($res&&$wa) {
                $result['msg'] = '注册成功!';
                //  $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = '注册失败!';
                $result['code'] = 0;
            }
            return json_encode($result);
        }
    }
    //验证码
    public function authCode($length=6)
    {
        $chars = "1234567890";
        $str = "";
        $size = strlen($chars);
        for($i=0;$i<$length;$i++){
            $str .= $chars[mt_rand(0,$size-1)];
        }
        return $str;
    }
    //测试
    public function ceshi(){
    }
    //我参与的任务
    public function taskOfMe()
    {
        //if(Request::instance()->isAjax()) {
        $list=db('ltask')->alias('lt')
            ->join(config('database.prefix').'task t','t.taskid = lt.taskid','left')
            // ->join(config('database.prefix').'invite it','t.taskid = it.taskid','left')
            ->join(config('database.prefix').'users u','t.userid = u.id','left')
            ->field('t.*,lt.*,u.username,u.mobile')
            ->where('t.userid', '=', "6") //更改当前userid
            ->order('t.createtime desc')
            ->select();
        $result['code'] = 1;
        $result['msg'] = '获取成功!';
        $result['url'] = url('index');
        return json_encode($result);
        //}
        //  return $this->fetch();
    }
    //我的钱包
    public function walletOfMe(){
        $list=db('users')->alias('u')
            ->join(config('database.prefix').'wallet wa','u.id = wa.userid','left')
            ->field('u.*,wa.*')
            ->where('wa.userid','=','3')//更改当前userid
            ->select();
        $count = $list['0']['systemMoney']+$list['0']['makeMoney']+$list['0']['upMoney'];
        $result['code'] = 1;
        $result['msg'] = '获取成功!';
        $result['url'] = url('index');
        return json_encode($result);
    }
}


