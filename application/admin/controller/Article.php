<?php
namespace app\admin\controller;
use think\Db;
use think\request;
use app\admin\model\Tags as Tags;

class Article extends Common{
    protected  $model;
    public function _initialize(){
        parent::_initialize();
        $this->model=model('article');
    }
    public function index(){
        if(Request::instance()->isAjax()){
            $keyword=input('key');
            $catid=input('catid');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $map=[];
            $catemodel=model('category');
            if ($catid) {
                $catids = $catemodel->where(array('parentid'=>$catid))->column('id');
                if($catids){
                    $catid = $catid.','.implode(',',$catids);
                }else{
                    $catid = $catid;
                }
                $map['catid']=array('in',$catid);
                dump($map);
                $cinfo= $catemodel->where(array('id'=>$catid))->field('catdir,is_show')->find();
                dump($cinfo);die();
            }
            
            if(!empty($keyword) ){
                $map['title']=array('like','%'.$keyword.'%');
            }
            $list = $this->model->alias('a')
                ->join('category c','c.id = a.catid','LEFT')
                ->field('a.*,c.catname')
                ->where($map)
                ->order("sort asc,id desc")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->each(function($row,$key){
					$row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
				})->toArray();
            $rsult['code'] = 0;
            $rsult['msg'] = "获取成功";
            $lists = $list['data'];
            $rsult['data'] = $lists;
            $rsult['count'] = $list['total'];
            $rsult['rel'] = 1;
            return $rsult;
        }else{
            return $this->fetch();
        }
    }
    /*
       编辑文章
     */
    public function edit(){
        $id = input('id');
        $info = $this->model->where('id',$id)->find();
        $info['title_color'] = substr(explode(';',$info['title_style'])[0],6);
        $info['title_bold'] = substr(explode(';',$info['title_style'])[1],12);
        $catemodel=model('category');
        $cat_tree=$catemodel->get_category_tree('moduleid=2',1,$info['catid']);
        $this->assign('cat_tree',$cat_tree);
        $this->assign ('info', $info );
        $this->assign ( 'title', '编辑内容' );
        return $this->fetch();
    }
    /*
      编辑文章---提交数据
     */
    function update(){
        $data=input();
        $data['createtime'] =strtotime($data['createtime']);
        $data['updatetime'] = time();
        $title_style ='';
        if (isset($data['style_color'])) {
            $title_style .= 'color:' . $data['style_color'].';';
            unset($data['style_color']);
        }else{
            $title_style .= 'color:#222;';
        }
        if (isset($data['style_bold'])) {
            $title_style .= 'font-weight:' . $data['style_bold'].';';
            unset($data['style_bold']);
        }else{
            $title_style .= 'font-weight:normal;';
        }
        $data['title_style'] = $title_style;
        unset($data['style_color']);
        unset($data['style_bold']);
        unset($data['aid']);
        unset($data['pics_name']);
        unset($data['file']);
        $list=$this->model->update($data);
        if (false !== $list) {
            $result['url'] = url("admin/article/index");
            //标签
            if(isset($data['tags'])){
                $tags = array_filter(explode(',', $data['tags']));
                if ($tags) {
                    $tagsId = Db::name('article_tags')->where('article_id',$data['id'])->column('tag_id');
                    if($tagsId){
                        //如果存在，则全部删除后，重新添加
                        //统计减1
                        Tags::where('id', 'in', $tagsId)->setDec('nums');
                        //删除全部
                        Db::name('article_tags')->where('article_id',$data['id'])->delete();
                        //重新添加
                        foreach ($tags as $k => $v) {
                            $info = Tags::where('name', $v)->find();
//                            dump($info);die;
                            if($info){
                                Tags::where('name', $v)->setInc('nums');
                                $data3['tag_id'] = $info['id'];
                            }else{
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                            }
                            $data3['article_id'] = $data['id'];
                            Db::name('article_tags')->insert($data3);
                        }
                    }else{
                        //如果不存在
                        $tagslist = Tags::where('name', 'in', $tags)->select();
                        if(count($tagslist)>0){
                            foreach ($tagslist as $k => $v) {
                                $data3['tag_id'] = $v['id'];
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                                $v->nums++;
                                $v->save();
                                $tags = array_diff($tags, [$v['name']]);
                            }
                            foreach ($tags as $k => $v) {
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                            }
                        }else{
                            foreach ($tags as $k => $v) {
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                            }
                        }
                    }
                }
            }

            $result['msg'] = '修改成功!';
            $result['code'] = 1;
            return $result;
        } else {
            $result['msg'] = '修改失败!';
            $result['code'] = 0;
            return $result;
        }
    }
    public function set_categorys($categorys = array()) {
        if (is_array($categorys) && !empty($categorys)) {
            foreach ($categorys as $id => $c) {
                $this->categorys[$c['id']] = $c;
                $r = db('category')->where("parentid = $c[id]")->order('listorder ASC,id ASC')->select();
                $this->set_categorys($r);
            }
        }
        return true;
    }
    function checkfield($fields,$post){
        foreach ( $post as $key => $val ) {
            if(isset($fields[$key])){
                $setup=$fields[$key]['setup'];
                if(!empty($fields[$key]['required']) && empty($post[$key])){
                    $result['msg'] = $fields[$key]['errormsg']?$fields[$key]['errormsg']:'缺少必要参数！';
                    $result['code'] = 0;
                    return $result;
                }
                if(isset($setup['multiple'])){
                    if(is_array($post[$key])){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if(isset($setup['inputtype'])){
                    if($setup['inputtype']=='checkbox'){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if(isset($setup['fieldtype'])){
                    if($fields[$key]['type']=='checkbox'){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if($fields[$key]['type']=='datetime'){
                    $post[$key] =strtotime($post[$key]);
                }elseif($fields[$key]['type']=='textarea'){
                    $post[$key]=addslashes($post[$key]);
                }elseif($fields[$key]['type']=='linkage'){
                    if($post[$key][0]){
                        $post[$key] = implode(',',$post[$key]);
                    }else{
                        unset($post[$key]);
                    }
                }elseif($fields[$key]['type']=='editor'){
                    if(isset($post['add_description']) && $post['description'] == '' && isset($post['content'])) {
                        $content = stripslashes($post['content']);
                        $description_length = intval($post['description_length']);
                        $post['description'] = str_cut(str_replace(array("\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;'), '', strip_tags($content)),$description_length);
                        $post['description'] = addslashes($post['description']);
                    }
                    if(isset($post['auto_thumb']) && $post['thumb'] == '' && isset($post['content'])) {
                        $content = $content ? $content : stripslashes($post['content']);
                        $auto_thumb_no = intval($post['auto_thumb_no']) * 3;
                        if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                            $post['thumb'] = $matches[$auto_thumb_no][0];
                        }
                    }
                }
            }
        }
        return $post;
    }
    /*
       添加文章
     */
    public function add(){
        $catemodel=model('category');
        $cat_tree=$catemodel->get_category_tree('moduleid=2');
        $this->assign('cat_tree',$cat_tree);
        return $this->fetch();
    }
    /*
       添加文章----插入数据
     */
    public function insert(){
        $data = input('post.');
//        dump($data);die;
        $data['createtime'] =$data['updatetime'] = time();
        $data['userid'] = session('aid');
        $data['username'] = session('username');
        $title_style ='';
        if (isset($data['style_color'])) {
            $title_style .= 'color:' . $data['style_color'].';';
            unset($data['style_color']);
        }else{
            $title_style .= 'color:#222;';
        }
        if (isset($data['style_bold'])) {
            $title_style .= 'font-weight:' . $data['style_bold'].';';
            unset($data['style_bold']);
        }else{
            $title_style .= 'font-weight:normal;';
        }
        $data['title_style'] = $title_style;
        unset($data['style_color']);
        unset($data['style_bold']);
        unset($data['aid']);
        unset($data['pics_name']);
        unset($data['file']);
        $id= $this->model->insert($data);
        if ($id !==false) {
            $result['url'] = url("admin/article/index");
            //标签
            if(isset($data['tags'])){
                $tags = array_filter(explode(',', $data['tags']));
                if ($tags) {
                    $tagslist = Tags::where('name', 'in', $tags)->select();
                    if(count($tagslist)>0){
                        foreach ($tagslist as $k => $v) {
                            $data3['tag_id'] = $v['id'];
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                            $v->nums++;
                            $v->save();
                            $tags = array_diff($tags, [$v['name']]);
                        }
                        foreach ($tags as $k => $v) {
                            $data2 = ['name' => $v, 'nums' => 1];
                            $data3['tag_id'] = model('tags')->insertGetId($data2);
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                        }
                    }else{
                        foreach ($tags as $k => $v) {
                            $data2 = ['name' => $v, 'nums' => 1];
                            $data3['tag_id'] = model('tags')->insertGetId($data2);
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                        }
                    }
                }
            }
            $result['msg'] = '添加成功!';
            $result['code'] = 1;
            return $result;
        } else {
            $result['msg'] = '添加失败!';
            $result['code'] = 0;
            return $result;
        }

    }
    public function listDel(){
        $id = input('post.id');
        $model = $this->model;
        $model->where(array('id'=>$id))->delete();//转入回收站
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delAll(){
        $map[] =array('id','in',input('post.ids/a'));
        $model = $this->model;
        $model->where($map)->delete();
        $result['code'] = 1;
        $result['msg'] = '删除成功！';
        $result['url'] = url('index',array('catid'=>input('post.catid')));
        return $result;
    }
    public function listorder(){
        $model = $this->model;
        $catid = input('catid');
        $data = input('post.');
        $model->update($data);
        $result = ['msg' => '排序成功！','url'=>url('index',array('catid'=>$catid)), 'code' => 1];
        return $result;
    }
    public function delImg(){
        if(!input('post.url')){
            return ['code'=>0,'请指定要删除的图片资源'];
        }
        $file = ROOT_PATH.__PUBLIC__.input('post.url');
        if(file_exists($file) && trim(input('post.url'))!=''){
            is_dir($file) ? dir_delete($file) : unlink($file);
        }
        if(input('post.id')){
            $picurl = input('post.picurl');
            $picurlArr = explode(':',$picurl);
            $pics = substr(implode(":::",$picurlArr),0,-3);
            $model = $this->model;
            $map['id'] =input('post.id');
            $model->where($map)->update(array('pics'=>$pics));
        }
        $result['msg'] = '删除成功!';
        $result['code'] = 1;
        return $result;
    }
    public function getRegion(){
        $Region=db("region");
        $map['pid'] = input("pid");
        $list=$Region->where($map)->select();
        return $list;
    }

}