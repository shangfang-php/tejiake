<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Spread extends Controller{
    //获取等待推广数据  准备推广到软件  phone
    public function index(){
        $list = Db::name('goods_collect')
            ->alias('gc')
            ->field('gc.*,g.title,g.link,g.common_type,g.type,g.plan_link,g.type,g.main_img,g.long_img,g.short_title,g.coupon_money,g.real_money,g.price,g.coupon_total_num,g.coupon_apply_num,g.end_time,g.taoke_money_percent,g.start_time,g.taoke_money,g.plan_type,g.create_time,g.sell_num,g.coupon_link,g.coupon_money,g.coupon_limit,g.real_money,g.guide_info,g.status,g.activity_type')
            ->where(['gc.is_spread'=>3])
            ->join('goods g','gc.gid=g.id','left')
            ->select();
        if(!empty($list)){
            return returnAjaxMsg(1,'成功',array('data'=>$list));
        }else{
            return returnAjaxMsg(0,'失败');
        }
    }

    /*
     * 修改推广状态
     * @param status 推广状态
     * @param remark 备注
     * */
    public function getStatus(){
        $gcid = input('gcid');
        $status = input('status');
        $remark = input('remark');
        if(!in_array($status,array(1,2))){
            return returnAjaxMsg(0,'数据有误');
        }
        $res = Db::name('goods_collect')->where(['id'=>$gcid])->update(['is_spread'=>$status,'remark'=>$remark]);
        if($res){
            return returnAjaxMsg(1,'成功');
        }else{
            return returnAjaxMsg(0,'失败');
        }
    }
}