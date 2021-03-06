<?php
namespace app\index\controller;
use think\Controller;
use think\paginator;
use think\Db;
//宝贝管理
class Goods extends UserCommon{
    public function _initialize(){

        parent::_initialize();
        /** 获取招商信息 没有则跳到申请页面*/
        $team_info  =   Db::table('merchant_apply_record')->where(array('uid'=>self::$login_user['id']))->order('id', 'desc')->find();
        if(!$team_info){ ##未申请招商淘客
            $this->redirect('user/apply');
        }

        if($team_info['status'] == 1){ ##审核中
            $this->redirect('user/show_checking');
        }
        
        if($team_info['status'] == 3){ ##审核失败
            $this->redirect('user/apply_fail');
        }
    }
    /*
     * 宝贝列表
     * @param type 商品类型
     * @param status 商品状态
     * @param keyword 商品标题关键词
     * */
    public function index(){
        $uid = self::$login_user['id'];
        $condition['g.uid'] = $uid;
        $condition['g.is_delete'] = 0;
        $keyword = isset($_GET['keyword'])?input('get.keyword'):"";
        $type = isset($_GET['type'])?input('get.type'):0;
        $status = isset($_GET['status'])?input('get.status'):0;
        if($type > 0){
            $condition['g.type'] = $type;
        }
        if($status > 0){
            $condition['g.status'] = $status;
        }
        if(!empty($keyword)){
            $condition['g.title'] = array('like',"%$keyword%");
        }
        $goods = Db::name('goods')
            ->alias('g')
            ->field('g.*,u.score')
            ->where($condition)
            ->join('user u','g.uid=u.id','left')
            ->order('g.id desc')
            ->paginate(5,false,['query'=>request()->param()]);
        $show = $goods->render();
        $count_0 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0])->count();//全部 0
        $count_2 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0,'status'=>2])->count();//展示 2
        $count_1 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0,'status'=>1])->count();//待审核 1
        $count_4 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0,'status'=>4])->count();//拒绝 4
        $count_5 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0,'status'=>5])->count();//已下架 5
        $count_3 = Db::name('goods')->where(['uid'=>$uid,'is_delete'=>0,'status'=>3])->count();//已过期 3
        $total = [$count_0,$count_1,$count_2,$count_3,$count_4,$count_5];
        $param = ['type'=>$type, 'status'=>$status,'keyword'=>$keyword];
        $data = [
            'goods'=>$goods,
            'show' =>$show,
            'total'=>$total,
            'param'=>$param,
            'web_title'=>'宝贝管理'
        ];
        //echo '<pre>';
        //print_r($count_3);exit;
        $this->assign($data);
        return $this->fetch();
    }

    /*
     * 商品下架 get
     * $param gid 商品ID
     * */
    public function makeGoodsShelves(){
        $uid = self::$login_user['id'];
        $gid = input('get.gid');
        $where =    ['id'=>$gid, 'uid'=>$uid, 'is_delete'=>0];
        $goods_info = Db::table('goods')->where($where)->find();
        if(empty($goods_info)){
            return returnAjaxMsg(0, '找不到对应商品信息!');
        }

        if($goods_info['status'] != 2){
            return returnAjaxMsg(0, '只有展示中的订单才能下架!');
        }

        Db::startTrans();
        $info   =   endGoods($gid, 7, '商品手动下架', $goods_info, $uid);
        if(!$info){
            Db::rollback();
            return returnAjaxMsg(0, '下架订单失败!');
        }
        Db::commit();
        return returnAjaxMsg(1,'成功');
    }

    /*
     * 删除商品 get
     * $param gid 商品ID
     * */
    public function delGoods(){
        $uid = self::$login_user['id'];
        $gid = input('get.gid');
        $info = Db::name('goods')->find($gid);
        if(empty($info)||$info['is_delete'] == 1||$info['uid'] != $uid){
            //exit(json_encode(array('status'=>)));
            return returnAjaxMsg(0,'数据有误');
        }
        $res = Db::name('goods')->where(['id'=>$gid])->update(['is_delete'=>1]);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }
    }

    /*
     * 添加备注
     * @param gid商品ID
     * @param user_reamrk 用户备注
     * */
    public function addRemark(){
        $uid = self::$login_user['id'];
        $gid = input('post.gid');
        $user_remark = input('post.user_remark');
        $info = Db::name('goods')->where(['is_delete'=>0,'id'=>$gid,'uid'=>$uid])->find();
        if(empty($info)){
            return returnAjaxMsg(0,'数据有误');
        }
        $res = Db::name('goods')->where(['id'=>$gid])->update(['user_remark'=>$user_remark]);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }

    }
}