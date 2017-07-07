<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
class Admin extends Common{
    public function index(){
        //获取全部用户
        $list = Db::name('admin')->where(array('id'=>array('neq','1')))->select();
        //print_r($list);exit;
        //获取每个用户的菜单权限
        if(!empty($list)&&is_array($list)){
            foreach($list as $k=>$v){
                $aid = $v['id'];
                $menus = Db::name('adminauth')->alias('a')->field('name')->where(array('a.adminid'=>$aid))->join('menu m','m.id = a.menuid','left')->select();
                if(!empty($menus)){
                    $new = array();
                    foreach($menus as $kk=>$vv){
                        $new[$kk] = $vv['name'];
                    }
                    $list[$k]['menu'] = implode('，',$new);
                }else{
                    $list[$k]['menu']  = '无';
                }
            }
        }
        $this->assign('data',$list);
        return $this->fetch();
    }
    public function addAdmin(){
        if(request()->post()){
            //print_r($_POST);exit;
            $username = input('post.username');
            $password = pswCrypt(input('post.password'));
            $menus = input('post.menu/a');
            //$menus = request()->post('menu/a');
            $status = input('post.status');
            $info = Db::name('admin')->where(array('username'=>$username))->find();
            //exit(json_encode(array('status'=>0,'msg'=>count($menus))));
            if(!empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'当前用户名已存在')));
            }
            if(empty($menus)||count($menus)==0){
                exit(json_encode(array('status'=>0,'msg'=>'菜单不可为空')));
            }
            $res = Db::name('admin')->insert(array('username'=>$username,'password'=>$password,'status'=>$status));
            if(!$res){
                exit(json_encode(array('status'=>0,'msg'=>'用户添加失败')));
            }
            $userId = Db::name('admin')->getLastInsID();
            //添加权限表
            $param = array();
            if(is_array($menus)){
                foreach($menus as $k=>$v){
                    $menuinfo = Db::name('menu')->where(array('id'=>$v))->find();
                    if($menuinfo['parentId'] == 0){
                        $level = 1;
                    }else{
                        $level = 2;
                    }
                    $param[$k]=array(
                        'adminid'=>$userId,
                        'menuid'=>$v,
                        'level'=>$level,
                    );
                }
                //添加
                $result = Db::name('adminauth')->insertAll($param);
                if($result){
                    exit(json_encode(array('status'=>1,'msg'=>'成功')));
                }else{
                    exit(json_encode(array('status'=>0,'msg'=>'失败')));
                }
            }
        }else{
            return $this->fetch();
        }
    }

    public function editAdmin(){
        if(request()->post()){
            $id = input('post.id');
            $username = input('post.username');

            $menus = input('post.menu/a');
            //$menus = request()->post('menu/a');
            $status = input('post.status');
            $info = Db::name('admin')->where(array('id'=>$id,'username'=>$username))->find();
            //exit(json_encode(array('status'=>0,'msg'=>count($menus))));
            if(empty($info)){
                exit(json_encode(array('status'=>0,'msg'=>'程序错误')));
            }
            if(empty($menus)||count($menus)==0){
                exit(json_encode(array('status'=>0,'msg'=>'菜单不可为空')));
            }
            $editparam['status'] = $status;
            //修改密码
            if(!empty(input('post.password'))){
                $password = pswCrypt(input('post.password'));
                $editparam['password']  = $password;
            }
            $res = Db::name('admin')->where(array('id'=>$id))->update($editparam);
            if(!$res){
                exit(json_encode(array('status'=>0,'msg'=>'修改错误')));
            }

            //添加权限表
            $param = array();
            if(is_array($menus)){
                foreach($menus as $k=>$v){
                    $menuinfo = Db::name('menu')->where(array('id'=>$v))->find();
                    if($menuinfo['parentId'] == 0){
                        $level = 1;
                    }else{
                        $level = 2;
                    }
                    $param[$k]=array(
                        'adminid'=>$id,
                        'menuid'=>$v,
                        'level'=>$level,
                    );
                }
                //修改菜单权限 即 先删除原有权限菜单再重新添加
                $delete = Db::name('adminauth')->where(array('adminid'=>$id))->delete();
                if($delete){
                    $result = Db::name('adminauth')->insertAll($param);
                    if($result){
                        exit(json_encode(array('status'=>1,'msg'=>'成功')));
                    }else{
                        exit(json_encode(array('status'=>0,'msg'=>'失败')));
                    }
                }
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'失败')));
            }
        }else{
            $id = input('adminid');
            //echo $id;exit;
            $info = Db::name('admin')->where(array('id'=>$id))->find();
            $menus = Db::name('adminauth')->where(array('adminid'=>$id))->select();
            $menu = array();
            if(!Empty($menus)&&is_array($menus)){
                foreach($menus as $kk=>$vv){
                    $menu[$kk] = $vv['menuid'];
                }
            }
            //echo '<pre>';
            //print_r($menu);exit;
            $this->assign('menu',$menu);
            $this->assign('data',$info);
            return $this->fetch();
        }

    }
}