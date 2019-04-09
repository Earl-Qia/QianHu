<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\View;

class Superior extends Common{
    
    private $self;

    public function index(){
        //分佣比例(n%)
        $terrace = db('config')->where('id',3)->value('value')/100;
        $first = db('config')->where('id',1)->value('value')/100;
        $second = db('config')->where('id',2)->value('value')/100;
        echo 'F:'.$first."//S:".$second."<br>";
        //自己Id
        // $self = input('post.id');
        $self = "7";
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
    //获取上级ID
    public function father($self){
        $father = db('users')->where('id',$self)->value('superior');
        return ($father)?$father:'';
    }
    //写入金额
    /*
    Array：$ratio 返佣账户与比例
    Int：  $sup 总佣金
    */
    public function writeMoney($ratio,$sup){
        //获取总佣金
        dump($sup);
        dump($ratio);
        //更新钱包、流水记录表
        if($ratio['father']){
            echo db('wallet')->where('userid',$ratio['father'])->setInc('makeMoney',$sup*$ratio['first']);
            $fw = db('wallet')->where('userid',$ratio['father'])->value('walletid');
            $data[]=[
                'walletid'  =>  $fw,
                'source'    =>  '4',
                'money'     =>  $sup*$ratio['first'],
                'createtime'=>  time(),
            ];
        }
        if($ratio['grd_ft']){
            echo db('wallet')->where('userid',$ratio['grd_ft'])->setInc('makeMoney',$sup*$ratio['second']);
            $fw = db('wallet')->where('userid',$ratio['grd_ft'])->value('walletid');
            $data[]=[
                'walletid'  =>  $fw,
                'source'    =>  '4',
                'money'     =>  $sup*$ratio['second'],
                'createtime'=>  time(),
            ];
        }
        if(isset($data))
        echo db('incom')->insertAll($data);
    }
}