<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;

class Login extends Common{
    
    private $self;
    //普通登录
    public function index(){
        $sMd5 = $this->sMd5;
        if(Request::instance()->isAjax()) {
            $name = input('post.name');
            $pwd = input('post.pwd');
            if(empty($name)||empty($pwd))
            return  ['code'=>0,'msg'=>'用户名或密码不能为空!'];
            //获取相应用户的正确信息
            $qArray = db("users")->where("username",$name)->find();
            $qPwd = $qArray['password'];
            if(empty($qArray))
            return  ['code'=>0,'msg'=>'用户不存在!'];
            if(md5($pwd.$sMd5)==$qPwd){
                //生成用户session
                Session::set('username',$name);
                Session::set('userid',$qArray['id']);
                return  ['code'=>1,'msg'=>'登录成功!'];
            }else{
                return  ['code'=>0,'msg'=>'用户名或密码不正确!'];
                //json_encode
            }
        }
        return $this->fetch();
    }
    //手机验证码登录
    public function phone(){
        if(Request::instance()->isAjax()) {
            $phone = input('post.phone');
            $code = input('post.code');
            if(empty($phone)||empty($code))
            return ['code'=>0,'msg'=>'手机或验证码不能为空!'];
            //获取正确的验证码
            $qCode = Session::pull("authCode");
            //验证手机号,获取手机号对应的用户名
            $qArray = db("users")->where('mobile',$phone)->find();
            $uName = $qArray['username'];
            if(empty($uName))
            return  ['code'=>0,'msg'=>'该手机用户未注册!'];
            if($code==$qCode){
                //生成用户session
                Session::set('username',$uName);
                Session::set('userid',$qArray['id']);
                return  ['code'=>1,'msg'=>'登录成功!'];
            }else{
                return  ['code'=>0,'msg'=>'请输入正确的验证码!'];
            }
        }
        return $this->fetch();
    }
    //发送验证码
    public function authCode()
    {
        
    }

}