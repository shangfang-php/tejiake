<?php
namespace app\index\controller;
use think\Controller;

class UserCommon extends Common{
    public function _initialize(){
    	parent::_initialize();
    	$this->assign('goods_type', self::$goods_type);
    	
    	/** 用户未登录则跳转到登录界面 */
    	if(!self::$login_user){
    		$this->redirect('/user_login');
    	}
    }
}