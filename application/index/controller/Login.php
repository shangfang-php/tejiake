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

    //注册
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
        if(!checkPhone($phone)){
            return returnAjaxMsg(403, '手机号格式不正确!');
        }

        /** 检测发送时间间隔 */
        $send_time  =   session('send_time');
        $now_time   =   time();
        if($now_time - $send_time < 60){
            return returnAjaxMsg(402, '请不要频繁点击发送!');
        }

        $phoneInfo  =   Db::table('user')->field('id')->where(['phone'=>$phone])->find();
        if($phoneInfo){
            return returnAjaxMsg(403, '该手机号已注册!');
        }

        $rand = rand(100000,999999);
        Session::set('send_time', $now_time); ##保存发送时间
        Session::set('send_code', $rand); ##保存发送内容

        include_once("SendTemplateSMS.php");
        $datas = array($rand,'10分钟');
        $tempId ="76747";
        sendTemplateSMS($phone,$datas,$tempId);
        return returnAjaxMsg(200, '发送成功!');
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

    /**
     * 登录提交信息
     * @return [type] [description]
     */
    function login_info(){
        $phone  =   trim(input('post.phone'));
        $password=  trim(input('post.password'));
        $isSaveUser=trim(input('post.isSaveUser'));

        if(!$phone){
            return returnAjaxMsg(501, '请输入手机号');
        }

        if(!checkPhone($phone)){
            return returnAjaxMsg(502, '手机号格式不正确');   
        }

        if(!$password){
            return returnAjaxMsg(503, '请输入密码');
        }

        $phoneInfo  =   Db::table('user')->where(array('phone'=>$phone))->find();
        if(!$phoneInfo){
            return returnAjaxMsg(504,'用户不存在！');
        }

        if($phoneInfo['password'] != md5($password)){
            return returnAjaxMsg(505,'密码错误');
        }

        if($phoneInfo['status'] == 2){
            return returnAjaxMsg(507,'该帐号已封禁');   
        }

        $data   =   array(
                        'login_time'    =>  time(),
                        'login_ip'      =>  $_SERVER['REMOTE_ADDR'],
                    );

        $info   =   Db::table('user')->where(array('id'=>$phoneInfo['id']))->update($data);
        if($info !== FALSE){
            Session::set('taoke_user', $phoneInfo);
            $code = 200;
            $msg  = '登录成功!';
        }else{
            $code = 506;
            $msg  = '登录失败!';
        }
        return returnAjaxMsg($code, $msg);
    }

    function logout(){
        Session::set('taoke_user', NULL);
        $this->redirect('/');
    }
}
