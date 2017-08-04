<?php
namespace app\api\controller;
use think\Controller;
use think\Db;

/**
 * 商品查询接口
 */
class Goods extends Controller{
	private static $code 	=	0;
	private static $msg 	=	'';

	public function _initialize(){
		parent::_initialize();
	}

	/**
	 * 添加显示抢购产品
	 */
	public function index(){
		$userId 	=	intval(trim(input('post.user_id'))); ##注册是生成的用户ID
		$timestamp 	=	trim(input('post.timestamp')); ##时间戳
		$sign 		=	trim(input('post.sign')); ##加密签名
		$type 		=	intval(trim(input('post.type'))); ##商品类型 默认1 爆款单
		$type 		=	$type ? $type : 1;
		$page 		=	intval(trim(input('post.page'))); ##分页数 每页100
		$page 		=	(!$page || $page < 0) ? 1 : $page;
		//var_dump($page);exit;
		if(!$timestamp){
			return returnAjaxMsg('301', '时间戳不能为空!');
		}

		if($timestamp - time() > 10 * 60){
			return returnAjaxMsg('302', '时间戳与服务器时间相隔不能大于十分钟!');
		}

		if(!$sign){
			return returnAjaxMsg('303', '签名不能为空!');
		}

		if(!$userId){
			return returnAjaxMsg('304', '用户ID不能为空!');
		}

		if(!in_array($type, [1,2,3,4,5])){
			return returnAjaxMsg('305', '商品类型不正确!');
		}

		$user_model 	=	\think\Loader::model('User');
		$check_sign 	=	$user_model->check_sign($userId, $sign, $timestamp);
		if(!$check_sign){
			return returnAjaxMsg('306', '签名不正确!');
		}

		$data 	=	$this->search_data($type, $page);
		$total 	=	count($data);
		$return =	array('total'=>$total, 'result'=>$data);
		return returnAjaxMsg('200', '获取成功', $return);
	}

	/**
	 * 搜索商品
	 * @Author   Gary
	 * @DateTime 2017-08-03T20:00:30+0800
	 * @param    [type]                   $type [商品类型]
	 * @param    [type]                   $page [分页]
	 * @return   [type]                         [description]
	 */
	function search_data($type, $page){
		$data 		=	array();
		$page_num 	=	1;
		$limit 		=	($page - 1) * $page_num;

		$where		=	['type'=>$type, 'status'=>2];
		if($type == 3){ ##过夜单
			$where['show_time'] = ['>=', time()];
		}
		$first_gid 	=	Db::table('goods')->field('id')->where($where)->limit($limit,1)->select(); ##获取符合条件的第一条数据的商品ID
		if(empty($first_gid)){
			return $data;
		}
		
		$where['id']=	['>=',$first_gid['0']['id']];
		$field 		=	['id','taobao_goodsId'=>'itemId', 'title', 'short_title', 'link', 'plan_type', 'type', 'plan_link', 'main_img', 'price', 'real_money', 'sell_num','coupon_link', 'coupon_total_num', 'coupon_apply_num', 'coupon_money', 'coupon_start_time', 'coupon_end_time', 'taoke_money_percent', 'taoke_money', 'guide_info','is_tmall'];
		if($type == 2){ ##限时抢购
			$field[]	=	'activity_type';
		}

		$data 		=	Db::table('goods')->field($field)->where($where)->limit($page_num)->select();
		if(!empty($data)){
			$gids 	=	array_column($data, 'id');
			if(in_array($type , [4,5])){ ##直播单和视频单需要拉取扩展信息
				$type == 4 && $name = 'live_info';
				$type == 5 && $name = 'video_url';
				$extends 	=	$this->get_goods_extends($gids, $type);
				foreach($data as &$val){
					$gid 	=	$val['id'];
					unset($val['id']);
					$extend =	isset($extends[$gid]) ? ($type == 5 ? $extends[$gid][0] : $extends[$gid]) : '';

					$val[$name]	= $extend;
				}
			}
		}
		return $data;
	}

	/**
	 * 获取商品的扩展信息并返回处理之后的结果
	 * @Author   Gary
	 * @DateTime 2017-08-03T20:29:09+0800
	 * @param    [type]                   $gids [description]
	 * @param    [type]                   $type [description]
	 * @return   [type]                         [description]
	 */
	function get_goods_extends($gids, $type){
		$return 		=	array();
		$where['gid'] 	=	['in', $gids];
		if($type ==4){ ##直播单
			$table 	=	'goods_live_extends';
			$field 	=	'url,content,gid,type';
		}
		if($type == 5){ ##视频单
			$table 	=	'goods_video_extends';
			$field 	=	'video_url,gid';
		}
		$data 	=	Db::table($table)->field($field)->where($where)->select();
		if(!empty($data)){
			foreach($data as $val){
				$gid 	=	$val['gid'];
				unset($val['gid']);
				if($type == 4 && $val['type'] == 1){
					$val['url']	=	'http://www.tejiake.com'.$val['url'];
					unset($val['type']);
				}

				if($type == 5){
					$val 	=	$val['video_url'];
				}
				$return[$gid][] 	=	$val;
			}
		}
		return $return;
	}

}

?>