<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\View;
use think\Session;

class Topup extends Common{
    
    public function index(){
        $user['id']='5';
        $user['walletid']='5';
       $this->writeMoney(11,$user);
    }
    //写入金额
    public function writeMoney($money,$user){
        $wallet = db('wallet')->where('userid',$user['id'])->setInc('upMoney',$money);
        $upincom = db('incom')->insert([
            'walletid' => $user['walletid'],
            'source'   => '2',
            'money'    => $money,
            'createtime' => time()
        ]);
        if(empty($wallet)||empty($upincom)){
            return ['code'=>0,'meg'=>'账单更新失败！'];
        }
    }
    //今日收入
    public function todayUp(){
        $tm = time();
        $d = date('Y-m-d',$tm);
        //***********************************
        Session::set('userid','4');
        //***********************************
        $userid = Session::get('userid');
        $dstart = strtoTime($d);//今天零点时间戳
        $list=db('wallet')->alias('w')
                ->join(config('database.prefix').'incom ic','ic.walletid = w.walletid','left')
                ->field('ic.*')
                ->where(['ic.createtime'=>['gt',$dstart],'w.userid'=>$userid])
                ->order('ic.createtime desc')
                ->select();
        foreach ($list as $k=>$v){
                $list[$k]['createtime'] = date('H:i:s',$v['createtime']);
            }
        dump($list);
        $this->assign('list',$list);
        return $this->fetch();
    }
    //历史收入
    public function historyUp(){
        //*****************************
        Session::set('userid','4');
        //*****************************
        $userid = Session::get('userid');
        $list=db('wallet')->alias('w')
                ->join(config('database.prefix').'incom ic','ic.walletid = w.walletid','left')
                ->field('ic.*')
                ->where(['w.userid'=>$userid])
                ->order('ic.createtime desc')
                ->select();
        foreach ($list as $k=>$v){
                $list[$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            }
        dump($list);
        $this->assign('list',$list);
        return $this->fetch();
    }
}