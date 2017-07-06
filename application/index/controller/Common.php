<?php
namespace app\index\controller;
use think\Controller;
class Common extends Controller{
	public static $login_user = '';
    public function _initialize(){
    	self::$login_user	=	session('taoke_user');
    	$this->assign('login_user', self::$login_user);
    }
}