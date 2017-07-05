<?php
namespace app\index\controller;
use think\Controller;
class Login extends Controller{
    public function index(){

       return $this->fetch();
    }

    public function register(){

       return $this->fetch();
    }

    //发送手机验证码
    public function sendVerify(){
        $rand = rand(1000,9999);
        $url = 'http://api.sms.cn/sms/?ac=send&uid=yuyuane2142&pwd=aee44f843c2590e977d717f642571ee5&mobile=18702039648&content=手机注册验证码为'.$rand.'，请尽快填写以完成会员注册【Jane小憩Place】';
        request_get($url);
        //$r = json_decode($res,true);
    }
}