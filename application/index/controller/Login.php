<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Db;
/**
 * 登录类
 */
class Login extends Controller{
    public $validate = '';

    public function _initialize(){
        $this->validate = \think\Loader::validate('User');
    }

    public function index(){
       return $this->fetch();
    }

    public function register(){
        Session::set('isRegister', 1); ##防止脚本提交验证
        return $this->fetch();
    }

    /**
     * 发送手机验证码
     * @return [type] [description]
     */
    public function send_code(){
        $phone      =   trim(input('post.phone'));
        if(!$phone || !preg_match("/^1[3587]\d{9}$/", $phone)){
            return false;
        }

        $send_time  =   session('send_time');
        $now_time   =   time();
        if($now_time - $send_time < 60){
            return false;
        }

        $rand = rand(100000,999999);
        Session::set('send_time', $now_time); ##保存发送时间
        Session::set('send_code', $rand); ##保存发送内容
        echo $rand;exit;
        /*$url = 'http://api.sms.cn/sms/?ac=send&uid=yuyuane2142&pwd=aee44f843c2590e977d717f642571ee5&mobile='.$phone.'&content=手机注册验证码为'.$rand.'，请尽快填写以完成会员注册【特价客】';
        request_get($url);*/
    }

    /**保存滑动验证通过信息
     * @author Gary
     * @return [type] [description]
     */
    function check_drag(){
        if(session('isRegister')){
            Session::set('check_drag', 1); ##滑动验证码通过
        }
    }

    /**
     * 注册提交验证
     * @return [type] [description]
     */
    function register_info(){
        $isRegister =   session('isRegister');
        $check_drag =   session('check_drag');
        if(!$isRegister || !$check_drag){
            return returnAjaxMsg(300,'非法操作');
        }

        $session_code   =   session('send_code');
        $code           =   trim(input('post.code'));
        if(!$code || $code != $session_code){
            return returnAjaxMsg(301, '手机验证码不正确');
        }

        $data   =   array(
                        'phone'     =>  trim(input('post.phone')),
                        'password'  =>  trim(input('post.password')),
                        'password1' =>  trim(input('post.password1')),
                    );

        $checkValidata  =   $this->validate->check($data); ##通过验证器验证信息
        if(!$checkValidata){
            return returnAjaxMsg(302, $this->validate->getError() );
        }

        unset($data['password1']);
        $data['password']   =   md5($data['password']);
        $data['login_time'] =   time();
        $data['create_time']=   time();
        $data['login_ip']   =   $_SERVER['REMOTE_ADDR'];

        $insert_id  =   Db::table('user')->insertGetId($data);
        if($info === FALSE){
            $code = 303;
            $msg  = '注册失败!';
        }else{
            $code = '200';
            $msg  = '注册成功!';
            $data['id'] =   $insert_id;

            /** 清除session */
            Session::delete('send_code');
            Session::delete('isRegister');
            Session::delete('check_drag');

            Session::set('taoke_user', data); ##保存登陆session
        }

        return returnAjaxMsg($code, $msg);
    }
}