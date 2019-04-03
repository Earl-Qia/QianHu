<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\View;

class Superior extends Common{
    
    private $self;

    public function index(){
        //分佣比例(n%)
        $first = db('config')->where('id',1)->value('value')/100;
        $second = db('config')->where('id',2)->value('value')/100;
        echo 'F:'.$first."//S:".$second."<br>";
        //自己Id
        // $self = input('post.id');
        $self = "6";
        // 分成佣金
        // $sup = input('post.sup');
        $sup = '200';
        $father = $this->father($self);
        $grd_ft = $this->father($father);
        $result['first'] = $first;
        $result['second'] = $second;
        $result['father'] = $father;
        $result['grd_ft'] = $grd_ft;
        $this->writeMoney($result,$sup);
    }
    //获取上级
    public function father($self){
        $father = db('users')->where('id',$self)->value('superior');
        return ($father)?$father:'';
    }
    //写入金额
    public function writeMoney($ratio,$sup){
        //获取总佣金
        dump($sup);
        //更新流水记录表
        //更新钱包
        if($ratio['father'])
        db('wallet')->where('userid',$ratio['father'])->setInc('makeMoney',$sup*$ratio['first']);
        if($ratio['grd_ft'])
        db('wallet')->where('userid',$ratio['grd_ft'])->setInc('makeMoney',$sup*$ratio['second']);
    }
}