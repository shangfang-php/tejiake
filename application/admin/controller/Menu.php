<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
class Menu extends Common{
    public function index(){
        //获取Menu
        $menu = Db::name('menu')->where(array('parentId'=>0))->order('id asc')->select();
        $nmenu = array();
        if(!empty($menu)){
            foreach ($menu as $k=>$v) {
                $pid = $v['id'];
                $nmenu[$k] = $v;
                $cmenu = Db::name('menu')->where(array('parentId'=>$pid))->select();
                if(!empty($cmenu)){
                    $nmenu[$k]['cmenu'] = $cmenu;
                }else{
                    $nmenu[$k]['cmenu'] = array();
                }
            }
        }
        $this->assign('list',$nmenu);
        return $this->fetch();
    }

    public function addMenu(){
        if(request()->isPost()){
            $param = array(
                'name'    => input('post.name'),
                'parentId'=> input('post.pid'),
                'url'     => input('post.url'),
                'icon'    => input('post.icon'),
                'is_close'=> input('post.is_close'),
                'is_show' => input('post.is_show'),
            );

           // echo '<pre>';
           // print_r($param);exit;
            $info1 = Db::name('menu')->where(array('name'=>input('post.name')))->find();
            if(!empty($info1)){
                exit(json_encode(array('status'=>0,'msg'=>'菜单名称已存在')));
            }
            /*$info2 = Db::name('menu')->where(array('url'=>input('post.url')))->find();
            if(!empty($info2)){
                exit(json_encode(array('status'=>0,'msg'=>'URL已存在')));
            }*/
            $res = Db::name('menu')->insert($param);
            if($res){
                exit(json_encode(array('status'=>1,'msg'=>'添加成功','url'=>url('index'))));
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'添加失败')));
            }
        }else{
            return $this->fetch();
        }
    }

    public function editMenu(){
        if(request()->isPost()){
            $menuid = input('post.menuid');
            $param = array(
                'name'    => input('post.name'),
                'parentId'=> input('post.pid'),
                'url'     => input('post.url'),
                'icon'    => input('post.icon'),
                'is_close'=> input('post.is_close'),
                'is_show'=> input('post.is_show'),
            );
            $info1 = Db::name('menu')->where(array('id'=>$menuid))->find();
            if(empty($info1)){
                exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
            }
            $res = Db::name('menu')->where(array('id'=>$menuid))->update($param);
            if($res){
                exit(json_encode(array('status'=>1,'msg'=>'修改成功','url'=>url('index'))));
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'修改失败')));
            }
        }else{
            $menuid = input('menuid');
            $info = Db::name('menu')->where(array('id'=>$menuid))->find();
            //print_r($info);exit;
            $this->assign('data',$info);
            return $this->fetch();
        }
    }

    public function hideMenu(){
        $menuid = input('get.menuid');
        $info1 = Db::name('menu')->where(array('id'=>$menuid))->find();
        if(empty($info1)){
            exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
        }
        if($info1['is_close'] == 1){
            $save = array('is_close'=>0);
        }else{
            $save = array('is_close'=>1);
        }
        $res = Db::name('menu')->where(array('id'=>$menuid))->update($save);
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'操作成功',)));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
        }
    }
    public function delMenu(){
        $menuid = input('get.menuid');
        $info1 = Db::name('menu')->where(array('id'=>$menuid))->find();
        if(empty($info1)||$info1['is_delete'] == 1){
            exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
        }
        $res = Db::name('menu')->where(array('id'=>$menuid))->update(array('is_delete'=>1));
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'删除成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'删除失败')));
        }
    }
}