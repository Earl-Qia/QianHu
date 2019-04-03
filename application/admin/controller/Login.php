<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use think\captcha\Captcha;
use think\request;
class Login extends Controller
{
    private $cache_model,$system;
    public function _initialize(){
        if (session('aid')) {
            $this->redirect('admin/index/index');
        }
        // $this->cache_model=array('Module','AuthRule','Category','Posid','Field','System');
        // $this->system = cache('sys_config');
        $this->assign('system','open');
        // if(empty($this->system)){
        //     foreach($this->cache_model as $r){
        //         savecache($r);
        //     }
        // }
    }
    public function index(){
        if(request()->isPost()) {
            $data = input('post.');
//            dump($data);die;
            $admin = new Admin();
            $return = $admin->login($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            return $this->fetch();
        }
    }
    public function verify(){
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    25,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    false,
            'bg'          =>    [255,255,255],
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}