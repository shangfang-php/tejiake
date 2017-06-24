<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use think\Session;

class Login extends Controller
{   
    public function index()
    {var_dump(111);exit;
        if(Session::get('admin_user')){
            $this->redirect('index/index');
        }else{
            return view('login');
        }
    }
    
    /**
     * Login::login()
     * 管理员后台登陆验证
     * @return
     */
    public function login(){
        $username   =   input('username');
        $password   =   input('password');
        if(!$username){
            //$this->error('用户名不能为空!');
            return '用户名不能为空';
        }
        
        if(!$password){
            return '密码不能为空!';
        }
        
        $password   =   Md5($password);
        
        $userModel  =   Loader::model('Admin');
        $userInfo   =   $userModel->get(['username'=>$username]);
        if(empty($userInfo)){
            return $username.':没有该用户!';
        }
        $userInfo   =   $userInfo->toArray();
        
        if($userInfo['password'] != $password){
            return '密码错误!';
        }
        
        Session::set('admin_user', $userInfo);
        return 'OK';
        //$this->redirect('index/index');
    }
    
    /**
     * Login::loginout()
     * 安全退出
     * @author Gary
     * @return void
     */
    public function loginout(){
        Session::delete('admin_user');
        $this->redirect('login/index');
    }
}
