<?php
namespace app\admin\controller;

use app\admin\model\Users as UsersModel;
use think\request;
use think\Session;

class Users extends Common{
    //会员列表
    public function index(){
        Session::set('fun','index');
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join(config('database.prefix').'user_level ul','u.level = ul.level_id','left')
                ->join(config('database.prefix').'wallet wa','u.id = wa.userid','left')
                ->field('u.*,ul.level_name,wa.*')
                ->where('u.email|u.mobile|u.username','like',"%".$key."%")
                ->where('u.level','neq','11')
                ->where('u.level','neq','12')
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            //dump($list);
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        echo Session::has('fun');
        return $this->fetch();
    }
    //雇主列表
    public function boss(){
        Session::set('fun','boss');
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join(config('database.prefix').'user_level ul','u.level = ul.level_id','left')
                //->join(config('database.prefix').'task t','u.id = t.userid','left')
                ->field('u.*,ul.level_name')
                ->where('u.email|u.mobile|u.username','like',"%".$key."%")
                ->where('level','11')
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    
    //业务员列表
    public function sales(){
        Session::set('fun',"sales");
        if(request()->isPost()){
            $key=input('post.key');
            $req = Request::instance();
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join(config('database.prefix').'user_level ul','u.level = ul.level_id','left')
                //->join(config('database.prefix').'task t','u.id = t.userid','left')
                ->field('u.*,ul.level_name')
                ->where('u.email|u.mobile|u.username','like',"%".$key."%")
                ->where('level','12')
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            //统计时间段
            $tm = time();
            $d = date('Y-m-d',$tm);
            $m = date('Y-m',$tm);
            $dstart = strtoTime($d);
            $mstart = strtoTime($m);
            //时间段end
            foreach ($list['data'] as $k=>$v){
                //今日推广人数
                $list['data'][$k]['td_peo'] = db('user_demo')->where(['salesid'=> $v['id'],'createtime'=>['gt',$dstart],])->count();
                //今日试玩人次（IP量）
                $list['data'][$k]['td_IP'] = db('user_demo')->where(['salesid'=> $v['id'],'createtime'=>['gt',$dstart],])->count('userIP');
                //月推广人数
                 $list['data'][$k]['m_peo'] = db('user_demo')->where(['salesid'=> $v['id'],'createtime'=>['gt',$mstart],])->count();
                //月推广人次（IP量）
                 $list['data'][$k]['m_IP'] = db('user_demo')->where(['salesid'=> $v['id'],'createtime'=>['gt',$mstart],])->count();
                $list['data'][$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    //设置会员状态
    public function usersState(){
        $id=input('post.id');
        $is_lock=input('post.is_lock');
        if(db('users')->where('id='.$id)->update(['is_lock'=>$is_lock])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    //添加会员
    public function add($id='')
    {
        if(request()->isPost())
        {
            $url = Session::pull('fun');
            // $data = input('post.');
            // dump($data);
            $user = new UsersModel();
            //用户组处理
            $level =explode(':',input('level'));
            $level = $level[1];
            $list =
                [
                    'username' => input('username'),
                    'password' => input('password'),
                    'district' => input('district'),
                    'province' => input('province'),
                    'mobile' => input('mobile'),
                    'level' => $level,
                    'email' => input('email'),
                    'city' => input('city'),
                    'qq' => input('qq'),
                    'sex' => input('sex'),
                    'reg_time' => time()
                ];
            $res = db('users')->insertGetId($list);
            $wa = db('wallet')->insertGetId(['userid'=>$res]);
            if ($res&&$wa) {
                $result['msg'] = '添加成功!';
                $result['url'] = url($url);
                $result['code'] = 1;
            } else {
                $result['msg'] = '添加失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $province = db('Region')->where ( array('pid'=>1) )->select ();
            $user_level=db('user_level')->order('sort')->select();
            $info = UsersModel::get($id);
            $this->assign('info',json_encode($info,true));
            $this->assign('title',lang('edit').lang('user'));
            $this->assign('province',json_encode($province,true));
            $this->assign('user_level',json_encode($user_level,true));
            $city = db('Region')->where ( array('pid'=>$info['province']) )->select ();
            $this->assign('city',json_encode($city,true));
            $district = db('Region')->where ( array('pid'=>$info['city']) )->select ();
            $this->assign('district',json_encode($district,true));
            return $this->fetch();
        }
//        return $this->fetch('add');
    }
    //编辑会员
    public function edit($id=''){
        if(request()->isPost()){
            $user = db('users');
            $data = input('post.');
            $level =explode(':',$data['level']);
            $data['level'] = $level[1];
            $province =explode(':',$data['province']);
            $data['province'] = isset( $province[1])?$province[1]:'';
            $city =explode(':',$data['city']);
            $data['city'] = isset( $city[1])?$city[1]:'';
            $district =explode(':',$data['district']);
            $data['district'] = isset( $district[1])?$district[1]:'';
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = md5($data['password']);
            }
            if ($user->update($data)!==false) {
                $result['msg'] = '会员修改成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = '会员修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $province = db('Region')->where ( array('pid'=>1) )->select ();
            $user_level=db('user_level')->order('sort')->select();
            $info = UsersModel::get($id);
            $this->assign('info',json_encode($info,true));
            $this->assign('title',lang('edit').lang('user'));
            $this->assign('province',json_encode($province,true));
            $this->assign('user_level',json_encode($user_level,true));
            $city = db('Region')->where ( array('pid'=>$info['province']) )->select ();
            $this->assign('city',json_encode($city,true));
            $district = db('Region')->where ( array('pid'=>$info['city']) )->select ();
            $this->assign('district',json_encode($district,true));
            return $this->fetch();
        }
    }

    public function getRegion(){
        $Region=db("region");
        $pid = input("pid");
        $arr = explode(':',$pid);
        $map['pid']=$arr[1];
        $list=$Region->where($map)->select();
        return $list;
    }

    public function usersDel(){
        db('users')->delete(['id'=>input('id')]);
        db('oauth')->delete(['uid'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map[] =array('id','IN',input('param.ids/a'));
        db('users')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /***********************************会员组***********************************/
    public function userGroup(){
        if(request()->isPost()){
            $userLevel=db('user_level');
            $list=$userLevel->order('sort')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }
    public function groupAdd(){
        if(request()->isPost()){
            $data = input('post.');
            db('user_level')->insert($data);
            $result['msg'] = '会员组添加成功!';
            $result['url'] = url('userGroup');
            $result['code'] = 1;
            return $result;
        }else{
            $this->assign('title',lang('add')."会员组");
            $this->assign('info','null');
            return $this->fetch('groupForm');
        }
    }
    public function groupEdit(){
        if(request()->isPost()) {
            $data = input('post.');
            db('user_level')->update($data);
            $result['msg'] = '会员组修改成功!';
            $result['url'] = url('userGroup');
            $result['code'] = 1;
            return $result;
        }else{
            $map['level_id'] = input('param.level_id');
            $info = db('user_level')->where($map)->find();
            $this->assign('title',lang('edit')."会员组");
            $this->assign('info',json_encode($info,true));
            return $this->fetch('groupForm');
        }
    }
    public function groupDel(){
        $level_id=input('level_id');
        if (empty($level_id)){
            return ['code'=>0,'msg'=>'会员组ID不存在！'];
        }
        db('user_level')->where(array('level_id'=>$level_id))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    //修改金额
    public function moneyOrder(){
        $userLevel=db('wallet');
        $data = input('post.');
        $userLevel->update($data);
        $result['msg'] = '金额更新成功!';
        $result['url'] = url('index');
        $result['code'] = 1;
        return $result;
    }
}