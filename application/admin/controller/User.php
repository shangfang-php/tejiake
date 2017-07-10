<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Paginator;
use think\Db;
class User extends Common{
    //用户列表
    public function index(){
        $where['is_delete'] = 0;
        //获取用户类型
        $type = input('type');
        if(!empty($type)){
            $where['type'] = $type;
        }
        //获取封禁用户列表
        $status = input('status');
        if(!empty($status)){
            $where['status'] = $status;
        }else{
            $where['status'] = 1;
        }
        //关键词
        $pagesize = 10;
        $keyword = input('keyword');

        if(!empty($keyword)){
            $where['qq|wechat|phone'] = array('like',"%$keyword%");
        }
        //print_r($where);exit;
        $list = Db::name('user')->where($where)->order('id desc')->paginate($pagesize,false,['query'=>request()->param()]);
        $show = $list->render();
        $this->assign('data',$list);
        $this->assign('show',$show);
        return $this->fetch();
    }
    //修改用户分数
    public function editScore(){
        if(request()->post()){
            $uid = input('post.uid');
            $info = Db::name('user')->find($uid);
            if(empty($info)||$info['status'] == 1){
                exit(json_encode(['status'=>0,'msg'=>'用户数据有误']));
            }
            $score = intval(input('post.score'));
            $type = input('post.type');
            $remark = input('post.remark');
            if($type == 3){
                //添加
                $diff = $info['score']+$score;
            }elseif($type == 4){
                //删减
                $diff = $info['score']-$score;
                if($diff<0)$diff =0;
            }
            $data = [
                'uid'=>$uid,
                'score'=>$score,
                'type'=>$type,
                'remark'=>$remark,
                'time'=>time(),
                'operator'=>$this->admininfo['id']
            ];
            $res = Db::name('user_score_record')->insert($data);
            if($res){
                Db::name('user')->where(['id'=>$uid])->update(['score'=>$diff]);
                exit(json_encode(['status'=>1,'msg'=>'成功']));
            }else{
                exit(json_encode(['status'=>0,'msg'=>'失败']));
            }
        }else{
            $uid = input('uid');
            //用户详情
            $info = Db::name('user')->find($uid);
            //获取当前用户的积分变更记录
            $list = Db::name('user_score_record')->where(array('uid'=>$uid,'is_delete'=>0))->select();
            $scoreinfo = Db::name('common_config')->where(['name'=>'score_type'])->find();
            $score_type = json_decode($scoreinfo['content'],true);
            $this->assign('type',$score_type);
            $this->assign('data',$info);
            $this->assign('list',$list);
           // echo '<pre>';
            // print_r($list);exit;
            return $this->fetch();
        }
        //$scoreinfo = Db::name('common_config')->where(['name'=>'score_type'])->find();
        //$score_type = json_decode($scoreinfo['content'],true);
        //echo '<pre>';
       // print_r($score_type);
    }
    //修改用户状态
    public  function editStatus(){
        $uid = input('get.uid');
        $info = Db::name('user')->where(array('id'=>$uid))->find();
        if(empty($info)){
            exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
        }
        if($info['status'] == 1){
            $save = array('status'=>2);
        }else{
            $save = array('status'=>1);
        }
        $res = Db::name('user')->where(array('id'=>$uid))->update($save);
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'操作成功',)));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
        }
    }

    //删除用户的积分记录
    public function delScore(){
        $rid = input('get.rid');
        $info = Db::name('user_score_record')->where(array('id'=>$rid))->find();
        if(empty($info)||$info['is_delete'] == 1){
            exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
        }
        $res = Db::name('user_score_record')->where(array('id'=>$rid))->update(array('is_delete'=>1));
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'删除成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'删除失败')));
        }
    }

    //删除用户
    public function delUser(){
        $uid = input('get.uid');
        $info = Db::name('user')->where(array('id'=>$uid))->find();
        if(empty($info)||$info['is_delete'] == 1){
            exit(json_encode(array('status'=>0,'msg'=>'参数有误')));
        }
        $res = Db::name('user')->where(array('id'=>$uid))->update(array('is_delete'=>1));
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'删除成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'删除失败')));
        }
    }

    //添加用户
    public function addUser(){
        $param = request()->post();
        if(!checkPhone($param['phone'])){
           exit(json_encode(array('status'=>0,'msg'=>'输入正确手机号')));
        }
        if(empty($param['password']) || $param['password'] != $param['confirmpwd']){
            exit(json_encode(array('status'=>0,'msg'=>'数据有误')));
        }
        $info = Db::name('user')->where(['phone'=>$param['phone']])->find();
        if(!empty($info)){
            exit(json_encode(array('status'=>0,'msg'=>'当前手机号已使用')));
        }
        $data = array(
            'phone'   =>$param['phone'],
            'password'=>$param['password'],
            'login_ip'=>request()->ip(),
            'login_time'=>time(),
            'create_time'=>time(),
            'month_income'=>0
        );
        $res = Db::name('user')->insert($data);
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    //修改用户密码
    public function editUserPwd(){
        $editid = input('post.editid');
        $editpwd = pswCrypt(input('post.editpwd'));
        $res = Db::name('user')->where(['id'=>$editid])->update(['password'=>$editpwd]);
        if($res){
            return json_encode(array('status'=>1,'msg'=>'成功'));
        }else{
            return json_encode(array('status'=>0,'msg'=>'失败'));
        }
    }

    //获取用户详情
    public function info(){
        return view();
    }
}