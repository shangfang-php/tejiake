<?php
namespace app\api\controller;
use think\Controller;
use think\Db;

class FlashSale extends Controller{
	private static $code 	=	0;
	private static $msg 	=	'';

	public function _initialize(){
		parent::_initialize();
	}

	/**
	 * 添加显示抢购产品
	 */
	public function add_goods(){
		$sign 			=	trim(input('sign'));
		$range_string	=	trim(input('range_string'));

		if(!$sign){
			return returnAjaxMsg('301', '未传递签名信息！');
		}

		if(!$range_string){
			return returnAjaxMsg('302', '未传递随机字符！');
		}
		
		$uid			=	1;
		$goods_data 	=	urldecode(input('post.goods_data'));
		$check_sign 	=	$this->check_sign($range_string, $goods_data, $sign);
		if(!$check_sign){
			return returnAjaxMsg('303', '签名错误!');
		}

		$data 		=	$this->init_data();

		$goods_data =	$data['data'];
		$taobao_goodsId = $goods_data['taobao_goodsId'];

		$goods 		=	Db::table('goods')->field('id')->where('taobao_goodsId', $taobao_goodsId)->find();
		if($goods){
			return returnAjaxMsg('304', '已有该产品!');
		}	
		$images_arr =	$data['images_arr'];

		Db::startTrans();
		$insertId 	=	Db::table('goods')->insertGetId($goods_data);
		if(!$insertId){
			Db::rollback();
			return returnAjaxMsg('保存产品信息失败!');
		}

		$insertData 		=	array();
		foreach($images_arr as $url){
			$insertData[]	=	array('gid'=>$insertId, 'image_url'=>$url);
		}
		$nums 	=	Db::table('goods_image')->insertAll($insertData);
		if(!$nums){
			Db::rollback();
			return returnAjaxMsg('305', '保存图片失败!');
		}
		Db::commit();
		return returnAjaxMsg('200', '保存成功!');
	}

	/**
	 * 格式化传递过来的商品和优惠券信息
	 */
	function init_data(){
		$data 	=	array();
		$goods_info 	=	input('post.goods_data');
		$goods_info 	=	urldecode($goods_info);
		$goods_info 	=	json_decode($goods_info, TRUE)['results']['n_tbk_item'];
		$coupon_info 	=	input('post.coupon_data');
		$coupon_info 	=	urldecode($coupon_info);
		$coupon_info 	=	json_decode($coupon_info, TRUE)['result'];
		$short_title 	=	trim(input('post.short_title'));
		$type 			=	intval(trim(input('post.type')));
		$type 			=	$type ? $type : 2; ##默认是2 限时抢购 1爆款单
		
		$data['taobao_goodsId']=	$goods_info['num_iid'];
		$data['link']	=	$goods_info['item_url'];
		$data['title']	=	$goods_info['title'];
		$images_arr	 	=	$goods_info['small_images']['string'];
		$data['price']	=	$goods_info['zk_final_price'];
		$data['main_img']=	$images_arr['1'];
		$data['short_title']= $short_title ? $short_title : $data['title'];
		$data['sell_num']	=$goods_info['volume'];
		$data['coupon_money']=$coupon_info['amount'];
		$data['coupon_limit']=floatval($coupon_info['startFee']);
		$data['coupon_start_time']	=	strtotime($coupon_info['effectiveStartTime']);
		$data['coupon_end_time']	=	strtotime($coupon_info['effectiveEndTime']);
		$data['coupon_link']		=	urldecode(trim(input('post.coupon_link'))); ##优惠券地址
		$data['start_time']			=	strtotime(input('post.start_time')); ##活动开始时间
		$data['end_time']			=	strtotime(input('post.end_time')); ##活动结束时间
		$data['show_time']			=	$data['start_time']; ##展示时间
		$data['real_money']			=	$data['price'] - $data['coupon_money']; ##券后价
		$data['taoke_money_percent']=	trim(input('post.taoke_money_percent')); ##佣金比例
		$data['taoke_money']		=	trim(input('post.taoke_money')); ##佣金金额
		$data['activity_type']		=	intval(trim(input('post.activity_type'))); ##聚划算还是淘抢购 1聚划算2淘抢购
		$data['is_tmall']			=	intval(trim(input('post.is_tmall'))); ##是否是天猫 1天猫 0淘宝
		$data['coupon_total_num']	=	input('post.coupon_total_num') ? input('post.coupon_total_num') : 999; ##优惠券总数
		$data['coupon_apply_num']	=	input('post.coupon_apply_num') ? input('post.coupon_apply_num') : 1; ##优惠券已领取数
		$data['guide_info']			=	urldecode(trim(input('post.guide_info'))); ##导购文案
		$data['status']				=	2; ##直接是展示状态
		$data['uid']				=	1;
		$data['common_type']		=	1; ##普通计划
		$data['plan_type']			=	2; ##通用计划
		$data['create_time']		=	time();
		$data['type']				=	$type;
		$data['is_collection']		=	1; ##标识采集字段

		return array('data'=>$data, 'images_arr'=>$images_arr);
	}

	/**
	 * 生成签名并检测
	 * @param  [type] $rang_string [description]
	 * @param  [type] $key         [description]
	 * @return [type]              [description]
	 */
	function check_sign($rang_string, $key, $sign){
		$correct 	=	md5($key.md5($rang_string));
		return $sign == $correct;
	}

}

?>