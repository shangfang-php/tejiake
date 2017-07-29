<?php
namespace app\index\controller;
use think\Controller;
use think\paginator;
use think\Db;
//会员收藏列表 OR 推广列表
class Collect extends UserCommon{
    public function index(){
        $uid = self::$login_user['id'];
        $condition['gc.uid'] = $uid;
        $action = isset($_GET['action'])?input('get.action'):'collect';
        //获取type
        $type = isset($_GET['type'])?input('get.type'):'1';
        if($type == '1'){
            $type_arr = array('1');
        }else{
            $type_arr = explode(',',$type);
        }
        $condition['g.type'] = array('in',"$type");
        if($action == 'spread'){
            $condition['gc.is_spread'] = array('in','1,2,3');
        }else{
            $condition['gc.is_spread'] = 0;
        }
        $list = Db::name('goods_collect')
            ->alias('gc')
            ->field('gc.*,g.type,g.short_title,g.coupon_money,g.real_money,g.price,g.coupon_total_num,g.coupon_apply_num,g.end_time,g.taoke_money_percent,g.start_time,g.taoke_money,g.plan_type,g.create_time')
            ->where($condition)
            ->join('goods g','gc.gid=g.id','left')
            ->order('gc.id desc')
            ->paginate(10,false,['query'=>request()->param()]);
        //exit;
        $collect_count = Db::name('goods_collect')->where(['uid'=>$uid,'is_spread'=>0])->count();
        $spread_count = Db::name('goods_collect')->where(['uid'=>$uid,'is_spread'=>array('in','1,2,3')])->count();
        $show = $list->render();
        $data = array(
            'action'       =>$action,
            'list'         =>$list,
            'collect_count'=>$collect_count,
            'spread_count' =>$spread_count,
            'show'         =>$show,
            'type_arr'     =>$type_arr,
        );
        $this->assign($data);
        return $this->fetch('user/user_collect');
    }

    /*
     * 清空 / 删除
     * @param gcid OR gcids_arr 收藏ID OR 收藏ID数组群
     * */
    public function clear(){
        //$uid = self::$login_user['id'];
        if(request()->isPost()){
            $gcid = input("post.gcids_arr/a");
        }else{
            $gcid = input("get.gcid");
        }
        /*if(count($gids_arr)>1){
            $gids = implode(',',$gids_arr);
        }else{
            $gids = $gids_arr[0];
        }*/
        $res = Db::name('goods_collect')->delete($gcid);
        if($res){
            exit(json_encode(array('code'=>1,'msg'=>'成功')));
            //return returnAjaxMsg(1,'成功');
        }else{
            exit(json_encode(array('code'=>0,'msg'=>'失败')));
            //return returnAjaxMsg(0,'失败');
        }
    }
    /*
     * 加入推广
     * @param gcid收藏ID spread_time推广时间
     * */
    public function addSpread(){
        $gcid = $_POST['gcid'];
        $spread_time = input('post.spread_time');
        $data = ['is_spread'=>3,'spread_time'=>$spread_time];
        if(is_numeric($gcid)){
            $info = Db::name('goods_collect')->where(['id'=>$gcid])->find();
            if(empty($info)){
                return returnAjaxMsg(0,'数据不存在');
            }
            if($info['is_spread'] != 0){
                return returnAjaxMsg(0,'数据有误');
            }
            $res = Db::name('goods_collect')->where(['id'=>$gcid])->update($data);
        }else{
            $gcid = input('post.gcid/a');
            $gcid = implode(',',$gcid);
            $res = Db::name('goods_collect')->where(array('id'=>array('in',$gcid)))->update($data);
        }
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }
    }

}