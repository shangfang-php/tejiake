<?php
namespace app\admin\controller;
use think\Controller;
use think\Cookie;
use think\Loader;
use think\Session;
use think\Db;

class Login extends Controller
{   
    public function index()
    {
        if(Session::get('admin_user')){
            $this->redirect('index/index');
        }else{
            if(request()->isPost()){
                $username = input('post.username');
                $psw = input('post.password');
                if(empty($username)||empty($psw)){
                    exit(json_encode(array('status'=>0,'msg'=>'用户名或密码不可为空')));
                }
                $password = pswCrypt($psw);
                $rempsw = input('post.rempsw');
                $info = Db::name('admin')->where(array('username'=>$username))->find();
                if(empty($info)){
                    exit(json_encode(array('status'=>0,'msg'=>'当前用户不存在')));
                }
                if($password != $info['password']){
                    exit(json_encode(array('status'=>0,'msg'=>'密码有误')));
                }
                Session::set('admin_user',$username);
                if($rempsw == 1){
                    //记住密码 存储于cookie
                    Cookie::set('admin_user',$username);
                    Cookie::set('admin_pass',$psw);
                }
                exit(json_encode(array('status'=>1,'msg'=>'登录成功'))) ;
            }else{
                return view('index');
            }
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
        $cookie_admin = Cookie::get('admin_user');
        $cookie_pass = Cookie::get('admin_pass');
        if(!empty($cookie_admin) || !empty($cookie_pass)){
            Cookie::delete('admin_user');
            Cookie::delete('admin_pass');
            //Cookie::clear();
        }
        $this->redirect('login/index');
    }
}
