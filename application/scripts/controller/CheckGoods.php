<?php
namespace app\scripts\controller;
use think\Controller;
use think\Db;

class CheckGoods extends Controller{
	function _initialize(){
		parent::_initialize();
	}

	/**
	 * 检测商品是否过期
	 * @Author   Gary
	 * @DateTime 2017-08-03T15:15:14+0800
	 * @return   [type]                   [description]
	 */
	public function index(){
		$where 	=	['status'=>2, 'coupon_end_time|end_time'=>['<=', time()]];
		$res 	=	Db::table('goods')->field('id,coupon_end_time,end_time,uid,scores')->where($where)->select();
		if(!empty($res)){
			foreach($res as $val){
				$goods_id 	=	$val['id'];
				$uid 		=	$val['id'];
				$coupon_end_time=$val['coupon_end_time'];
				$end_time 	=	$val['end_time'];
				$now_time 	=	time();
				if($end_time <= $now_time){
					$remark	=	'活动结束时间到期完结商品';
				}
				if($coupon_end_time <= $now_time){
					$remark	=	'优惠券结束时间到期完结商品';
				}

				Db::startTrans();
				$info 	=	endGoods($goods_id, 5, $remark, $val);
				if(!$info){
					Db::rollback();
					continue;
				}
				Db::commit();
			}
		}
	}
}
?>