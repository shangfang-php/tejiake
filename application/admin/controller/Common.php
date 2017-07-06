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
        //获取Menu
        $menu = Db::name('menu')->where(array('parentId'=>0,'is_close'=>0,'is_delete'=>0))->order('id asc')->select();
        $nmenu = array();
        if(!empty($menu)){
            foreach ($menu as $k=>$v) {
                $pid = $v['id'];
                $nmenu[$k] = $v;
                $cmenu = Db::name('menu')->where(array('parentId'=>$pid,'is_close'=>0,'is_delete'=>0))->select();
                if(!empty($cmenu)){
                    $nmenu[$k]['cmenu'] = $cmenu;
                }else{
                    $nmenu[$k]['cmenu'] = array();
                }
            }
        }
        //$time = getRandTime();
        //$this->assign('time',$time);
        $this->assign('menulist',$nmenu);
    }
}