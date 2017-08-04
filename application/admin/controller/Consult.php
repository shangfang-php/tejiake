<?php
namespace app\admin\controller;
use app\admin\controller\Common;
use think\Controller;
use think\Db;
class Consult extends Common{
    public function index(){
        $list = Db::name('consult')->order('id desc')->select();
        $this->assign('data',$list);
        return $this->fetch();
    }

    public function addConsult(){
        if(request()->post()){
            $data = [
                'title'=>input('post.title'),
                'content'=>input('post.area'),
                'is_show'=>1,
                'time'=>time()
            ];
            $res = Db::name('consult')->insert($data);
            if($res){
                $this->redirect(url('Consult/index'));
            }else{
                $this->error('失败');
            }
        }else{
            return $this->fetch();
        }
    }

    public function info(){
        $cid = input('cid');
        $info = Db::name('consult')->where(['id'=>$cid])->find();
        $this->assign('data',$info);
        return $this->fetch();
    }

    public function editConsult(){
        if(request()->post()){
            $cid = input('post.cid');
            $data = [
                'title'=>input('post.title'),
                'content'=>input('post.area'),
            ];
            //print_r($cid);exit;
            $res = Db::name('consult')->where(['id'=>$cid])->update($data);
            if($res){
                $this->redirect(url('Consult/index'));
            }else{
                $this->error('失败');
            }
        }else{
            $cid = input('cid');
            $info = Db::name('consult')->where(['id'=>$cid])->find();
            $this->assign('data',$info);
            return $this->fetch();
        }

    }

    public function delConsult(){
        $cid = input('cid');
        $res = Db::name('Consult')->delete($cid);
        if($res){
            //return json_encode(array('status'=>1,'msg'=>'成功'));
            return returnAjaxMsg(1,'成功');
        }else{
            //return json_encode(array('status'=>0,'msg'=>'失败'));
            return returnAjaxMsg(0,'失败');
        }
    }

}