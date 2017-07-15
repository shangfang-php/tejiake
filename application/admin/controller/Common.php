<?php
namespace app\admin\Controller;
use think\Controller;
use think\Session;
use think\Db;

class Common extends Controller{
    //public $admininfo;
    public  function _initialize(){
        //return Session::get('admin_user');
        $sess_admin = Session::get('admin_user');
        //return $sess_admin;
        if(empty($sess_admin)){
            $this->redirect('Login/index');
        }else{
            $this->admininfo = Db::name('admin')->where(array('username'=>$sess_admin))->find();
        }
        //获取当前用户的Menu权限
        if($this->admininfo['id'] == 1){
            //admin 有全部权限
            $menu = Db::name('menu')->where(array('parentId'=>0,'is_delete'=>0,'is_show'=>0))->order('sort asc')->select();
            $nmenu = array();
            if(!empty($menu)){
                foreach ($menu as $k=>$v) {
                    $pid = $v['id'];
                    $nmenu[$k] = $v;
                    $cmenu = Db::name('menu')->where(array('parentId'=>$pid,'is_close'=>0,'is_delete'=>0,'is_show'=>0))->select();
                    if(!empty($cmenu)){
                        $nmenu[$k]['cmenu'] = $cmenu;
                    }else{
                        $nmenu[$k]['cmenu'] = array();
                    }
                }
            }
        }else{
            $cid = array();
            //获取当前管理员是否有当前进去的方法的权限
            $url = getActionUrl();
            $auth = Db::name('adminauth')
                ->alias('a')
                ->where(array('a.adminid'=>$this->admininfo['id'],'m.url'=>$url))
                ->join('menu m','m.id=a.menuid','left')
                ->find();
            //print_r(request()->ip());exit;
            if($url != 'admin/Index/index'){
                if(empty($auth)){
                    $this->error('没有操作权限');
                }
            }
            $adminauth = Db::name('adminauth')->where(array('adminid'=>$this->admininfo['id']))->select();
            if(!empty($adminauth)&&is_array($adminauth)){
                foreach($adminauth as $k=>$v){
                    $info = array();
                    if($v['level'] == 1){
                        //$pid[] = $v['menuid'];
                        $info = Db::name('menu')->where(array('id'=>$v['menuid'],'is_close'=>0,'is_delete'=>0,'is_show'=>0))->find();
                        if(!empty($info)) {
                            $menu[] = $info;
                        }
                    }elseif($v['level'] == 2){
                        $cid[] = $v['menuid'];
                    }
                }
            }
           // echo '<pre>';
           // print_r($menu);exit;
            $nmenu = array();
            if(!empty($menu)){
                foreach ($menu as $k=>$v) {
                    $pid = $v['id'];
                    $nmenu[$k] = $v;
                    //获取当前一级菜单的全部下级菜单
                    $cmenu = Db::name('menu')->where(array('parentId'=>$pid,'is_close'=>0,'is_delete'=>0,'is_show'=>0))->select();
                    if(!empty($cmenu)){//判断属于当前用户的二级菜单
                        foreach($cmenu as $kk=>$vv){
                            if(in_array($vv['id'],$cid)){
                                $nmenu[$k]['cmenu'][] = $vv;
                            }
                        }
                    }else{
                        $nmenu[$k]['cmenu'] = array();
                    }
                }
            }
        }



        //echo '<pre>';
        //print_r($nmenu);exit;
        //$time = getRandTime();
        //$this->assign('time',$time);
        $this->assign('menulist',$nmenu);
    }
}