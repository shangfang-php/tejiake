<?php
namespace app\admin\Controller;
use app\admin\controller\Common;
use think\Controller;
use think\Paginator;
use think\Db;
class User extends Common{
    /*
     * 用户列表 get传递
     * @param type 淘客类型 1普通淘客 2招商淘客 可选 默认为全部淘客
     * @param status 用户状态 1正常 2封禁 可选 默认正常
     * @param keyword 搜索关键词 可选
     * */
    public function index(){
        $where['u.is_delete'] = 0;
        //获取用户类型
        $type = input('type');
        if(!empty($type)){
            $where['u.type'] = $type;
        }
        //获取封禁用户列表
        $status = input('status');
        if(!empty($status)){
            $where['u.status'] = $status;
        }else{
            $where['u.status'] = 1;
        }
        //关键词
        $pagesize = 10;
        $keyword = input('keyword');

        if(!empty($keyword)){
            $where['m.qq|u.phone'] = array('like',"%$keyword%");
        }
        //print_r($where);exit;
        $list = Db::name('user')
            ->alias('u')
            ->field('u.*,m.nickname,m.qq')
            ->where($where)
            ->join('merchant_apply_record m','u.id=m.uid','left')
            ->order('u.id desc')
            ->paginate($pagesize,false,['query'=>request()->param()]);
        $show = $list->render();
        $this->assign('data',$list);
        $this->assign('show',$show);
        return $this->fetch();
    }
    /*
     * 修改用户分数 post传递
     * @param uid 用户ID
     * @param type 积分操作类型 3为添加积分，4为删减积分
     * @param score 分数
     * */
    public function editScore(){
        if(request()->post()){
            $uid = input('post.uid');
            $info = Db::name('user')->find($uid);
            if(empty($info)||$info['status'] == 2){
                $this->error('用户数据有误');
            }

            $score = intval(input('post.score'));
            $type = input('post.type');
            $remark = input('post.remark');
            $diff = $info['score'];
            if($type == 3){
                //添加
                $diff += $score;
            }elseif($type == 4){
                //删减
                if($info['score'] == 0){
                    $this->error('用户积分不够执行此操作');
                }
                $diff -= $score;
                if($diff<0)$diff =0;
            }
            $data = [
                'uid'     =>$uid,
                'score'   =>$score,
                'type'    =>$type,
                'remark'  =>$remark,
                'time'    =>time(),
                'operator'=>$this->admininfo['id']
            ];
            $res = Db::name('user_score_record')->insert($data);
            if($res){
                Db::name('user')->where(['id'=>$uid])->update(['score'=>$diff]);
                $this->redirect(url('Finance/scoreDetail'));
            }else{
                $this->error('失败');
            }
        }else{
            $uid = input('uid');
            //用户详情
            $info = Db::name('user')->find($uid);
            //获取当前用户的积分变更记录
            $this->assign('data',$info);
           // echo '<pre>';
            // print_r($list);exit;
            return $this->fetch();
        }
    }

    /*
     * 修改用户状态 get传递
     * @param uid 要修改的用户ID
     * */
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

    /*
     * 删除用户的积分记录
     * @param rid 要删除的积分记录ID
     * */
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

    /*
     * 删除用户 get传递
     * @param uid 用户ID
     * */
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

    /*
     * 添加用户 post传递
     * @param phone 手机号
     * @param password 密码
     * @param confirmpwd 确认密码
     * */
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
            'phone'      =>$param['phone'],
            'password'   =>Md5($param['password']),
            'login_ip'   =>request()->ip(),
            'login_time' =>time(),
            'create_time'=>time(),
        );
        $res = Db::name('user')->insert($data);
        if($res){
            exit(json_encode(array('status'=>1,'msg'=>'成功')));
        }else{
            exit(json_encode(array('status'=>0,'msg'=>'失败')));
        }
    }

    /*
     * 修改用户密码
     * @param editid post传递 要修改的用户ID
     * @param editpwd post传递 要修改的用户新密码
     * */
    public function editUserPwd(){
        $editid = input('post.editid');
        $editpwd = Md5(input('post.editpwd'));
        $res = Db::name('user')->where(['id'=>$editid])->update(['password'=>$editpwd]);
        if($res){
            return json_encode(array('status'=>1,'msg'=>'成功'));
        }else{
            return json_encode(array('status'=>0,'msg'=>'失败'));
        }
    }

    /*
     * 获取用户详情
     * @param uid get传递 必选
     * */
    public function info(){
        if(request()->post()){
            $uid = input('post.uid');
            //$type = input('post.type');
           /* $data_user = [
                'type'=>input('post.type'),
            ];*/
            $data_merchant = [
                'free_trial'  =>json_encode(input('post.free_trial/a')),
                'type'        =>input('post.mtype'),
                'nickname'    =>input('post.nickname'),
                'qq'          =>input('post.qq'),
                'wechat'      =>input('post.wechat'),
                'month_income'=>input('post.month_income'),
                'introduce'   =>input('post.introduce'),
            ];
            //echo '<pre>';
            //print_r(json_encode(input('post.free_trial/a')));exit;
            $res = Db::name('merchant_apply_record')->where(['uid'=>$uid])->update($data_merchant);
            if($res){
                echo '<script> window.location.href = history.go(-1);</script>';exit;
            }else{
                $this->error('修改失败');
            }
        }else{
            //获取用户信息
            $uid = input('uid');
            $info = Db::name('user')
                ->alias('u')
                ->field('u.*,m.type as mtype,m.nickname,m.qq,m.wechat,m.month_income,m.introduce,m.free_trial')
                ->where(['u.id'=>$uid])
                ->join('merchant_apply_record m','u.id=m.uid','left')
                ->find();
            //获取当前用户发布的商品(已审核通过)
            $goodsnum = db('goods')->where(['uid'=>$uid,'status'=>2,'is_delete'=>0])->count();
            $info['goodsnum'] = $goodsnum;
            $info['free_trial'] = json_decode($info['free_trial'],true);
            //$info = Db::name('merchant_apply_record')->alias('m')->field('m.*,u.phone')->where(['m.id'=>$aid])->join('user u','u.id=m.uid','left')->find();
            //echo '<pre>';
            //print_r($info);exit;
            $this->assign('data',$info);
            return view();
        }

    }

    /*
     * 申请招商淘客列表
     * @param keyword 可选
     * */
    public function applyMerchant(){
        $keyword = input('keyword');
        $where['m.status'] = 1;
        if(!empty($keyword)){
            $where['u.phone'] = $keyword;
        }
        $list = Db::name('merchant_apply_record')->alias('m')->field('m.*,u.phone')->where($where)->join('user u','u.id=m.uid','left')->paginate(10,false,['query'=>request()->param()]);
        $show = $list->render();
        $this->assign('show',$show);
        $this->assign('data',$list);
        return $this->fetch();
    }

    /*
     * 获取当前申请招商淘客的数据详情 get传递
     * @param aid 申请招商淘客ID 必选
     * */
    public function applyInfo(){
        if(request()->post()){
            $aid = input('post.aid');
            $data = [
                'introduce'   =>input('post.introduce'),
                'nickname'    =>input('post.nickname'),
                'qq'          =>input('post.qq'),
                'wechat'      =>input('post.wechat'),
                'month_income'=>input('post.month_income'),
            ];
            //print_r($data);exit;
            $res = Db::name('merchant_apply_record')->where(['id'=>$aid])->update($data);
            if($res){
                echo '<script> window.location.href = history.go(-1);</script>';exit;
            }else{
                $this->error('修改失败');
            }
        }else{
            $aid = input('aid');
            $info = Db::name('merchant_apply_record')->alias('m')->field('m.*,u.phone')->where(['m.id'=>$aid])->join('user u','u.id=m.uid','left')->find();
            // print_r($info);exit;
            $this->assign('data',$info);
            return view();
        }

    }

    /*
     * 申请招商淘客的用户通过审核 get传递
     * @param aid 申请ID
     * */
    public function passApply(){
        $aid = input('aid');
        $info = Db::name('merchant_apply_record')->where(['id'=>$aid,'status'=>1])->find();
        if(empty($info)){
            return json_encode(array('status'=>0,'msg'=>'数据有误'));
        }
        //通过申请的话，一并修改user表中的淘客类型type=2
        $data = [
            'check_time'=>time(),
            'status'=>2
        ];
        $res = Db::name('merchant_apply_record')->where(['id'=>$aid])->update($data);
        if($res){
            Db::name('user')->where(['id'=>$info['uid']])->update(['type'=>2]);
            return json_encode(array('status'=>1,'msg'=>'成功'));
        }else{
            return json_encode(array('status'=>0,'msg'=>'失败'));
        }
    }

    /*
     * 拒绝招商申请 get 传递
     * @param aid 申请ID
     * @param remark 拒绝原因OR其他失败备注
     * */
    public function rejectMerchantApply(){
        $aid = input('post.aid');
        $remark = input('post.remark');
        $info = Db::name('merchant_apply_record')->where(['id'=>$aid,'status'=>1])->find();
        if(empty($info)||empty($remark)){
            return json_encode(array('status'=>0,'msg'=>'数据有误'));
        }
        $data = [
            'status'    =>3,
            'check_time'=>time(),
            'remark'    =>$remark
        ];
        $res = Db::name('merchant_apply_record')->where(['id'=>$aid])->update($data);
        if($res){
            return json_encode(array('status'=>1,'msg'=>'成功'));
        }else{
            return json_encode(array('status'=>0,'msg'=>'失败'));
        }

    }

    /*
     * 拒绝招商申请列表
     * @param
     * */
}