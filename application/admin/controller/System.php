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
            if(strlen($newpwd)<6){
                exit(json_encode(array('status'=>0,'msg'=>'密码长度必须大于OR等于6位')));
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

    public function webset(){
        $info = Db::name('setting')->where(['id'=>1])->find();//print_r($info);exit;
        if(request()->post()){
            $data = [
                'title'=>input('post.title'),
                'keyword'=>input('post.keyword'),
                'describe'=>input('post.describe'),
                'code'=>htmlspecialchars(input('post.code')),
            ];
            if(!empty($info)){
                //print_r($data);exit;
                $data['updatetime'] = time();
                Db::name('setting')->where(['id'=>1])->update($data);
            }else{
                $data['createtime'] = time();
                Db::name('setting')->insert($data);
            }
            $info = Db::name('setting')->where(['id'=>1])->find();
            //$this->assign('data',$info);
        }
        $this->assign('data',$info);
        return $this->fetch();
    }
}