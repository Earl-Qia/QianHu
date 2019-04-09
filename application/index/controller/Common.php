<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
class Common extends Controller
{
    protected $sMd5 = "lingshang";
    //protected $mod,$role,$system,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$adminRules,$HrefId;
    public function _initialize()
    {
        
    }
    //空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }

    public function resultmsg($msg='操作成功',$code=1,$url=null){
        $result['msg'] = $msg;
        $result['url'] = $url;
        $result['code'] = $code;
        return $result;    
    }
    public function verify(){
        //判断用户是否登录
        if (!session('aid')) {
            $this->redirect('index/login/index');
        }    
    }
}
