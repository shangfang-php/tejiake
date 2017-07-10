<?php
namespace app\admin\controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
class System extends Common{

    public function editPwd(){
        $info = $this->admininfo;
        if(request()->post()){
            $oldpwd = pswCrypt(input('post.oldpwd'));
            $newpwd = input('post.newpwd');
            $confirmnewpwd = input('post.confirmnewpwd');
            if($oldpwd != $info['password']){
                return json_encode(array('status'=>0,'msg'=>'密码有误'));
            }
            if($newpwd != $confirmnewpwd){
                return json_encode(array('status'=>0,'msg'=>'2次输入的密码不一致'));
            }
            $res = Db::name('admin')->where(['id'=>$info['id']])->update(['password'=>pswCrypt($newpwd)]);
            if($res){
                return json_encode(array('status'=>1,'msg'=>'成功'));
            }else{
                return json_encode(array('status'=>0,'msg'=>'失败'));
            }

        }else{
            $this->assign('data',$info);
            return $this->fetch();
        }
    }
}