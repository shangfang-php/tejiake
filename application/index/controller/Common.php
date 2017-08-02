<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Common extends Controller{
	public static $login_user = '';
	public static $goods_type = 'hot'; ##商品类型（用于判断头部导航栏样式 默认爆款
	public static $code       = 0;
	public static $msg        = '';
    public function _initialize(){
    	self::$login_user	=	session('taoke_user');
    	if(self::$login_user){
    		self::$login_user 	=	Db::table('user')->find(self::$login_user['id']);
            //获取当前收藏数目
            $collect_count = Db::name('goods_collect')->where(['uid'=>self::$login_user['id'],'is_spread'=>0])->count();
            $this->assign('collect_count', $collect_count);
    	}
    	$this->assign('login_user', self::$login_user);
    }
}