<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Db;
/**
 * 登录类
 */
class Login extends Controller{
    public $validate = '';
    private $code   =   0;
    private $msg    =   '';

    public function _initialize(){
        $this->validate = \think\Loader::validate('User');
        $web_info       =   Db::table('setting')->find(1);
        $this->assign('web_info',$web_info);
    }

    public function index(){
        $this->assign(['web_title'=>'登录']);
        return $this->fetch();
    }

    //注册
    public function register(){
        $this->assign(['web_title'=>'注册']);
        Session::set('isRegister', 1); ##防止脚本提交验证
        return $this->fetch();
    }

    /**
     * 发送手机验证码
     * @return [type] [description]
     */
    public function send_code(){
        $phone      =   trim(input('post.phone'));
        $type       =   trim(input('post.type'));
        $type       =   $type == 2 ? 2 : 1;
        if(!checkPhone($phone)){
            return returnAjaxMsg(401, '手机号格式不正确!');
        }

        $phoneInfo  =   Db::table('user')->field('id')->where(['phone'=>$phone])->find();
        if($type == 1 && $phoneInfo){
            return returnAjaxMsg(402, '该手机号已注册!');
        }

        if($type == 2 && !$phoneInfo){
            return returnAjaxMsg(403, '该手机号尚未注册!');
        }

        $phone_code_table = 'phone_code_records';
        $where      =   array('phone'=>$phone,'status'=>1);
        $record     =   Db::table($phone_code_table)->field('id,create_time')->where($where)->order('id','desc')->limit(1)->find();
        if(!empty($record)){ ##有发送记录
            $create_time    =   $record['create_time'];
            $diff_time      =   time() - $create_time;
            if( $diff_time <= 60){ ##60s之内
                return returnAjaxMsg(404,'请不要频繁点击发送！');
            }else{
                if($diff_time > 60 * 10){ ##已超出10分钟
                    $info = Db::table($phone_code_table)->where('id', $record['id'])->update(array('status'=>2));
                    if($info === false){
                        return returnAjaxMsg(405, '请再尝试点击发送验证码!');
                    }
                    $rand   =   rand(100000,999999);
                    $info   =   Db::table($phone_code_table)->insert(['phone'=>$phone,'code'=>$rand,'type'=>$type,'create_time'=>time()]);
                }else{
                    $rand   =   $record['code'];
                }
            }
        }else{
            $rand   =   rand(100000,999999);
            $info   =   Db::table($phone_code_table)->insert(['phone'=>$phone,'code'=>$rand,'type'=>$type,'create_time'=>time()]);
        }

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
        $phone  =   trim(input('post.phone'));
        $type   =   trim(input('post.type')); ##1为注册2为重置密码
        $type   =   $type == 2 ? 2 : 1; ##默认是注册

        $checkPhone =checkPhone($phone);
        if(!$checkPhone){
            return returnAjaxMsg('301', '手机号格式不正确!');
        }

        $res    =   Db::table('user')->field('id')->where('phone', $phone)->find();
        if($type == 1 && $res){
            return returnAjaxMsg('302','手机号已注册!');
        }
        if($type == 2 && !$res){
            return returnAjaxMsg('303','手机号尚未注册!');
        }
        if(session('isRegister') || $type == 2){
            Session::set('check_drag', 1); ##滑动验证码通过
            return returnAjaxMsg('200','验证通过');
        }else{
            return returnAjaxMsg('304','验证不通过');
        }
    }

    /**
     * 注册提交验证
     * @return [type] [description]
     */
    function register_info(){
        $isRegister =   session('isRegister');
        if(!$isRegister){
            return returnAjaxMsg(300,'非法操作');
        }

        $code           =   trim(input('post.code'));
        if(!$code){
            return returnAjaxMsg(301, '请输入验证码');
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

        $check_code     =   $this->check_code($data['phone'], $code, 1);
        if(!$check_code){
            return returnAjaxMsg($this->code, $this->msg);
        }

        unset($data['password1']);
        $data['password']   =   md5($data['password']);
        $data['login_time'] =   time();
        $data['create_time']=   time();
        $data['login_ip']   =   $_SERVER['REMOTE_ADDR'];
        $data['secret_key'] =   makeSecretKey(); ##生成密钥

        $insert_id  =   Db::table('user')->insertGetId($data);
        if($insert_id === FALSE){
            $code = 303;
            $msg  = '注册失败!';
        }else{
            Db::table('phone_code_records')->where('phone', $data['phone'])->update(['status'=>2]);
            $code = '200';
            $msg  = '注册成功!';
            $data['id']     =   $insert_id;
            $data['score']  =   0;

            /** 清除session */
            Session::delete('send_code');
            Session::delete('isRegister');

            Session::set('taoke_user', $data); ##保存登陆session
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
            $cookie_time    =   $isSaveUser ? 86400 * 365 : 86400;
            $sign           =   cookie_encode($phoneInfo['password']); ##密码加密
            Cookie::set('phone', $phone, $cookie_time);
            Cookie::set('sign', $sign, $cookie_time);
            $code = 200;
            $msg  = '登录成功!';
        }else{
            $code = 506;
            $msg  = '登录失败!';
        }
        return returnAjaxMsg($code, $msg);
    }

    /**
     * 退出登录
     * @return [type] [description]
     */
    function logout(){
        //Session::set('taoke_user', NULL);
        Cookie::delete('phone');
        Cookie::delete('sign');
        $this->redirect('/');
    }

    /**
     * 忘记密码
     * @return [type] [description]
     */
    function repwd(){
        $this->assign(['web_title'=>'找回密码']);
        return $this->fetch();
    }

    /**
     * 重置密码
     * @return [type] [description]
     */
    function reset_password(){
        $phone  =   trim(input('post.phone'));
        $code   =   trim(input('post.code'));
        $pwd    =   trim(input('post.pwd'));
        if(!$phone || !$code || !$pwd){
            return returnAjaxMsg('601', '请填入相关信息!');
        }

        $user   =   Db::table('user')->field('id')->where('phone',$phone)->find();
        if(!$user){
            return returnAjaxMsg('602', $phone.':该手机号尚未注册！');
        }

        $check_code     =   $this->check_code($phone,$code,2);
        if(!$check_code){
            return returnAjax($this->code, $this->msg);
        }

        $pwd    =   md5($pwd);
        $info   =   Db::table('user')->where('id', $user['id'])->update(['password'=>$pwd]);
        if($info === false){
            $this->code     =   603;
            $this->msg      =   '重置密码失败!';
        }else{
            Db::table('phone_code_records')->where('phone', $phone)->update(['status'=>2]);
            $this->code     =   200;
            $this->msg      =   '重置密码成功!';
        }
        return returnAjaxMsg($this->code, $this->msg);
    }

    /**
     * 检测手机号验证码是否正确
     * @param  [type] $phone [description]
     * @param  [type] $code  [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
    function check_code($phone, $code, $type = 1){
        $where      =   array('phone'=>$phone, 'status'=>1,'type'=>$type);
        $result     =   Db::table('phone_code_records')->field('id,code,create_time')->where($where)->order('id','desc')->limit(1)->find();
        if(!$result){
            $this->code     =   701;
            $this->msg      =   '没有相关验证码!';
            return false;
        }

        if($result['code'] != $code){
            $this->code     =   702;
            $this->msg      =   '验证码错误!';
            return false;
        }

        if(time() - $result['create_time'] > 10*60){
            $this->code     =   702;
            $this->msg      =   '验证码已过期!';
            Db::table('phone_code_records')->where('id', $result['id'])->update(['status'=>2]);
            return false;
        }
        return TRUE;
    }
}
