<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Model;
use think\Loader;
use think\Db;

/**
 * AdminController
 * Admin后台总控制器
 * @package TP流量
 * @author Gary
 * @copyright 2017
 * @version $Id$
 * @access public
 */
class AdminController extends Controller{
    public static $loginUser   =   '';
    
    public function __construct(){
        parent::__construct();
        
        self::$loginUser    =   Session::get('admin_user');
        if(empty(self::$loginUser)){
            $this->redirect('login/index');
        }
        
        $menuListModel  =   Loader::model('menuList');
        $menuList       =   $menuListModel->getUserMenu(self::$loginUser['id']);
        
        $this->assign('parentMenu', $menuList['parentMenu']);
        $this->assign('childMenu', $menuList['childMenu']);
        //$this->assign('loginUser', self::$loginUser);
    }
}
