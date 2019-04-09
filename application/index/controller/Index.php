<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;

class Index extends Common{
    
    public function index(){
       return $this->fetch();
    }
    //徒子徒孙
    public function posterity(){
        Session::set('userid','5');
        //徒弟
        $son = db('users')->where('superior',Session::get('userid'))->column('username','id');
        foreach ($son as $key => $value) {
            $grandson[] = db('users')->where('superior',$key)->column('username','id');
        }
        // dump($son);
        // dump($grandson);
        $this->assign('son',$son);
        $this->assign('grandson',$grandson);
        $this->fetch('posterity');
    }

}