<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class UserCommon extends Common{
    public function _initialize(){
    	parent::_initialize();
		/** 用户未登录则跳转到登录界面 */
		if(!self::$login_user){
			$this->redirect('/user_login');
		}
    	$this->assign('goods_type', self::$goods_type);
		$uid = self::$login_user['id'];
		$collect_count = Db::name('goods_collect')->where(['uid'=>$uid,'is_spread'=>0])->count();
		$this->assign('collect_count', $collect_count);
    	
    	/* 用户未登录则跳转到登录界面 */
    	/*if(!self::$login_user){
    		$this->redirect('/user_login');
    	}*/
    }
}