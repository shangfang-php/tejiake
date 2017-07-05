<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
class Login extends Controller{
    //登录
    public function index(){
        if(request()->post()){

        }else{
            return $this->fetch();
        }
    }

    //注册
    public function register(){
        if(request()->post()){
            $phone = input('post.phone');
            $code = input('post.code');
            $sess_code = Session::get('phone_code');
            if($code != $sess_code){
                exit(json_encode(array('status'=>0,'msg'=>'验证码不正确')));
            }
            $psw = input('post.psw');
            $confirmpsw = input('post.confirmpsw');
            if($confirmpsw != $psw){
                exit(json_encode(array('status'=>0,'msg'=>'2次密码填写不一致')));
            }
            $param = array(
                'phone'=>$phone,
                'password'=>$psw,
                'create_time'=>time()
            );
            $res = Db::name('user')->insert($param);
            if($res){
                exit(json_encode(array('status'=>1,'msg'=>'注册成功')));
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'注册失败')));
            }
        }else{
            return $this->fetch();
        }
    }

    //发送手机验证码
    public function sendVerify(){
        $rand = rand(1000,9999);
        $phone = input('get.phone');
        $info = Db::name('user')->where(array('phone'=>$phone))->find();
        if(!empty($info)){
            exit(json_encode(array('status'=>0,'msg'=>'当前手机已注册，请更换手机号码')));
        }
        Session::set('phone_code',$rand);
        $url = 'http://api.sms.cn/sms/?ac=send&uid=yuyuane2142&pwd=aee44f843c2590e977d717f642571ee5&mobile='.$phone
.'&content=手机注册验证码为'.$rand.'，请尽快填写以完成会员注册【Jane小憩Place】';
        request_get($url);

    }
}
