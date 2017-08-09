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
        $this->check_cookie_username();
    	if(self::$login_user){
            //获取当前收藏数目
            $collect_count = Db::name('goods_collect')->where(['uid'=>self::$login_user['id'],'is_spread'=>0])->count();
            $this->assign('collect_count', $collect_count);
    	}

        $web_info   =   Db::table('setting')->find(1);
        $data       =   array(
                            'login_user'    =>  self::$login_user, ##登录帐号信息
                            'keywords'      =>  '', ##头部搜索关键词
                            'web_info'      =>  $web_info, ##网站标题关键字相关信息、
                            'web_title'     =>  '首页',
                        );
    	
        $this->assign($data);
    }

    /**
     * 检测cookie里的手机号和密码是否正确
     * @Author   Gary
     * @DateTime 2017-08-09T15:14:35+0800
     * @return   [type]                   [description]
     */
    function check_cookie_username(){
        $cookie_phone       =   cookie('phone');
        $cookie_sign        =   cookie('sign');
        if($cookie_phone && $cookie_sign){
            $userInfo   =   Db::table('user')->where('phone', $cookie_phone)->find(); ##获取用户信息
            if($userInfo){
                $password   =   $userInfo['password']; 
                $current_sign=  cookie_encode($password); ##获取加密之后的密码
                if($current_sign == $cookie_sign){ ##检测是否与cookie中的一致
                    self::$login_user   =   $userInfo;
                }
            }
        }
    }
}