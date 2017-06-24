<?php
namespace app\admin\controller;

/**
 * Menu
 * 菜单管理
 * @package TP流量
 * @author Gary
 * @copyright 2017
 * @version $Id$
 * @access public
 */
class Menu extends AdminController
{   
    public $validate = '';
    public function __construct(){
        parent::__construct();
        $this->validate = \think\Loader::validate('Menu');
    }
    
    public function index(){
        return $this->fetch('menuIndex');
    }
    
    /**
     * Menu::addParentMenu()
     * 添加一级菜单
     * @auth Gary
     * @return void
     */
    public function addParentMenu(){
        if(request()->isPost()){
            $data   =   array('name'=>input('name'), 'url'=>input('url'));
            $check  =   $this->validate->scene('parentAdd')->check($data);
            if($check){
                $data   =   array_filter($data);
                $info   =   \think\Db::name('menuList')->insert($data);
                if(!$info){
                    $this->error('添加失败');
                }else{
                    $this->success('添加成功!');
                }
            }else{
                return $this->error($this->validate->getError());
            }
        }
        return $this->fetch('addParentMenu');
    }
    
    /**
     * Menu::addChildMenu()
     * 添加二级菜单
     * @auth Gary
     * @return void
     */
    public function addChildMenu(){
        if(request()->isPost()){
            $data   =   array('name'=>input('name'), 'url'=>input('url'), 'parentId'=>input('parentId'));
            $check  =   $this->validate->check($data);
            if($check){
                $data   =   array_filter($data);
                $info   =   \think\Db::name('menuList')->insert($data);
                if(!$info){
                    $this->error('添加失败');
                }else{
                    $this->success('添加成功!');
                }
            }else{
                return $this->error($this->validate->getError());
            }
        }
        return $this->fetch('addChildMenu');
    }
    
    /**
     * Menu::editUserMenu()
     * 权限分配主页
     * @return void
     */
    public function editUserMenu(){
        $uid        =   input('id');
        $this->assign('uid', $uid);
        $authList   =   \think\Db::name('menu_user_relation')->where('uid', $uid)->find();
        $authList   =   empty($authList) ? array() : json_decode($authList['auth'], TRUE);
        $this->assign('authList', $authList);
        return $this->fetch('editUserMenu');
    }
}
