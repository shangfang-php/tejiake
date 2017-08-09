<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

/**
 * 产品发布
 */
class Publish extends UserCommon{
	private static $team_info = '';
	public function _initialize(){
		parent::_initialize();
		
		/** 获取招商信息 没有则跳到申请页面*/
		self::$team_info	=	Db::table('merchant_apply_record')->where(array('uid'=>self::$login_user['id']))->order('id', 'desc')->find();
		if(!self::$team_info){ ##未申请招商淘客
			$this->redirect('user/apply');
		}

		if(self::$team_info['status'] == 1){ ##审核中
			$this->redirect('user/show_checking');
		}
        
        if(self::$team_info['status'] == 3){ ##审核失败
			$this->redirect('user/apply_fail');
		}
	}

	
	/**
	 * 发布中心首页
	 * @return [type] [description]
	 */
	public function index(){
		$mianshen 	=	self::$team_info['free_trial'] ? json_decode(self::$team_info['free_trial']) : array(); ##免审项目
		$data 		=	array(
							'mianshen'	=>	$mianshen,
							'web_title'	=>	'发布宝贝',
						);
		$this->assign($data);
		return $this->fetch('publish_index');
	}

	/**
	 * 发布产品页面
	 * @return [type] [description]
	 */
	function publish(){
		$common_type 	=	intval(trim(input('get.common_type')));
		$goods_type		=	intval(trim(input('get.goods_type')));
		if(!in_array($common_type, array(1,2))){
			$this->error('请重新选择类型', '/index/publish/index');
		}

		if(!in_array($goods_type, array(1,2,3,4,5))){
			$this->error('请重新选择类型', '/index/publish/index');
		}

		$data 	=	array(
						'common_type' 	=>	$common_type,
						'goods_type'	=>	$goods_type,
						'web_title'		=>	'发布宝贝_第二步'
					);
		if($goods_type == 4){ ##直播单
			$data['extends']	=	array(0=>[],1=>[],2=>[],3=>[],4=>[]);
		}
		$this->assign($data);
		return $this->fetch('publish_goods');
	}

	/**
	 * 通过接口获取商品信息
	 * @return [type] [description]
	 */
	function get_goods_info(){
		$goods_id 	=	trim(input('post.id'));
		if(!$goods_id){
			return returnAjaxMsg(301, '请输入商品ID');
		}

		$res 	=	Db::table('goods')->field('id')->where('taobao_goodsId', $goods_id)->find();
		if($res){
			return returnAjaxMsg(303,'该商品已存在!');
		}

		$goods_info 	=	getGoodsInfo($goods_id);
		if(empty($goods_info)){
			$code 	=	302;
			$msg 	=	'未查到商品信息!';
		}else{
			$code 	=	200;
			$msg 	=	$goods_info;
		}
		return returnAjaxMsg($code, $msg);
	}

	/**
	 * 获取优惠券信息
	 * @return [type] [description]
	 */
	function get_coupon_info(){
		$activityId	=	trim(input('post.activityId'));
		$goods_id 	=	trim(input('post.goods_id'));
		$goods_type	=	intval(trim(input('post.goods_type')));
		if(!$activityId){
			return returnAjaxMsg(401,'优惠券链接不正确!');
		}

		if(!$goods_id){
			return returnAjaxMsg(402,'商品链接不正确!');
		}

		$coupon_info 	=	getCouponInfo($activityId,$goods_id);
		if(!$coupon_info){
			return returnAjaxMsg(403,'未获取到优惠券信息!');
		}

		if($coupon_info['result']['retStatus'] != '0'){
			return returnAjaxMsg(404,'优惠券已失效!');
		}
		$coupon_info 	=	$coupon_info['result'];
		if(time() >= strtotime($coupon_info['effectiveEndTime'])){
			return returnAjaxMsg(405,'优惠券已过期!');
		}
		if($goods_type == 3){ ##过夜单
			$next_day_start	=	strtotime(date('Y-m-d', strtotime('+1 day')));
			$diff_time 		=	strtotime($coupon_info['effectiveStartTime']) - $next_day_start;
			if($diff_time <0 || $diff_time > 86399){
				return returnAjaxMsg(406,'过夜单只能提交第二天开始的优惠券商品!');
			}
		}
		$coupon_info['coupon_link'] = '';
		return returnAjaxMsg(200, $coupon_info);
	}

	/**
	 * 计算发布商品消耗的积分
	 * @return [type] [description]
	 */
	function count_score(){
		$real_money		=	floatval(trim(input('post.real_money')));
		$coupon_total_num=	intval(trim(input('post.coupon_total_num')));
		$coupon_apply_num=	intval(trim(input('post.coupon_apply_num')));
		if(!$real_money || !$coupon_total_num){
			return returnAjaxMsg(501,'请求非法!');
		}

		$uid 	=	self::$login_user['id'];
		$need_scores=	countGoodsScore($real_money, $coupon_total_num - $coupon_apply_num); ##计算需要的总积分
		$user_info 	=	Db::table('user')->field('score')->where(array('id'=>$uid))->find();
		$user_score =	$user_info['score'];
		$data		=	compact('user_score');
		$data		=	array_merge($data, $need_scores);
		return returnAjaxMsg(200, $data);
	}

	/**
	 * 提交产品信息
	 * @return [type] [description]
	 */
	function submit_goods(){
		$data 	=	$_POST;
		$uid	=	self::$login_user['id'];
		$data['uid']	=	$uid;
		$goods_type		=	$data['goods_type'];
		unset($data['goods_type']);
		$goods_id  		=	isset($data['id']) ? $data['id'] : 0;

		$data['common_type'] == 2 && $data['plan_type'] = 4; ##营销计划时计划类型为4
		$data['type'] =	$goods_type;
		
		$data['show_time']		=	( isset($data['show_time']) && $data['show_time'] ) ? strtotime($data['show_time']) : 0;
		$data['start_time']		=	$data['start_time'] ? strtotime($data['start_time']) : 0;
		$data['end_time']		=	$data['end_time'] ? strtotime($data['end_time']) : 0;
		$data['coupon_start_time']		=	$data['coupon_start_time'] ? strtotime($data['coupon_start_time']) : 0;
		$data['coupon_end_time']		=	$data['coupon_end_time'] ? strtotime($data['coupon_end_time']) : 0;

		if($goods_type == 4){
			if(!isset($data['live_info']) || !$data['live_info']){
				return returnAjaxMsg('620', '请输入直播信息!');
			}
			$live_info 	=	$data['live_info'];
			unset($data['live_info']);
		}

		if($goods_type == 5){
			$video_url 	=	$data['video_url'];
			unset($data['video_url']);
		}
		
		$data 			=	init_goods_data($data); ##初始化商品数据
		if(!is_array($data)){
			return returnAjaxMsg(601, $data);
		}

		$mianshen 	=	check_goods_mianshen($uid, $goods_type); ##检测是否免审
		$data['data']['status']=	$mianshen ? 2 : 1; ##免审则直接发布 2发布1待审核
		
		$data['data']['create_time']	=	time();
		$long_img	=	$data['data']['long_img'];
		unset($data['data']['long_img']);

		/** 检测用户积分 */
		$userInfo 	=	Db::table('user')->field('score')->where(['id'=>$uid])->find();
		$need_scores=	countGoodsScore($data['data']['real_money'], $data['data']['coupon_total_num'] - $data['data']['coupon_apply_num']);
		$need_scores=	$need_scores['need_scores'];
		if($need_scores > $userInfo['score']){
			return returnAjaxMsg('666', '积分余额不足，请先充值！');
		}
		$data['data']['scores']	=	$need_scores;

		Db::startTrans();
		if(!$goods_id){ ##发布产品
			$insertId 	=	Db::table('goods')->insertGetId($data['data']);
			if(!$insertId){
				Db::rollback();
				return returnAjaxMsg('602', '保存商品基本信息失败!');
			}
		}else{ ##修改产品
			$info 		=	Db::table('goods')->update($data['data']);
			if($info === FALSE){
				Db::rollback();
				return returnAjaxMsg('625', '修改商品基本信息失败!');
			}
			$insertId 	=	$goods_id;
		}

		/**扣除用户积分**/
		$remark	=	($goods_id ? '修改' : '发布') .'产品冻结'.$need_scores.'积分';
		$info 	=	updateUserScore($uid, '-'.$need_scores, 2, $remark, $uid, $need_scores, $insertId);
		if(!$info){
			Db::rollback();
			return returnAjaxMsg('665', '扣除用户积分失败!');
		}

		if(!$goods_id){ ##只有新发布产品才再次保存图片信息
			$info 	=	$this->save_goods_img($insertId, $data['images_arr']);
			if(!$info){
				Db::rollback();
				return returnAjaxMsg('603', '保存商品图片失败!');
			}
		}

		if($long_img){
			if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $long_img, $result)){
				$img_data 	=	str_replace($result[1], '', $long_img);
				$image_path	=	$this->save_goods_long_img($uid, $insertId, $img_data, $result[2]);
				if(!$image_path){
					Db::rollback();
					return returnAjaxMsg(self::$code, self::$msg);
				}
	        }else{
	        	$image_path =	$long_img;
	        }
	        $info 	= 	Db::table('goods')->where(['id'=>$insertId])->update(['long_img'=>$image_path]);
	        if(!$info){
	        	Db::rollback();
	        	return returnAjaxMsg('604', '保存长图失败！');
	        }
		}

		if($goods_type == 4 && $live_info){ ##直播单
			$info 	=	saveGoodsLiveInfo($uid, $insertId, $live_info);
			if(!$info){
				Db::rollback();
				return returnAjaxMsg('605', '保存直播信息失败！');
			}
		}

		if($goods_type == 5){ ##保存视频单网址信息
			$info 	=	saveGoodsVideInfo($uid, $insertId, $video_url);
			if(!$info){
				Db::rollback();
				return returnAjaxMsg('606', '保存视频信息失败！');
			}
		}

		Db::commit();
		return returnAjaxMsg('200', '提交商品成功!');
	}

	/**
	 * 保存商品图片信息
	 * @param  [type] $goodsId    [description]
	 * @param  [type] $images_arr [description]
	 * @return [type]             [description]
	 */
	function save_goods_img($goodsId, $images_arr){
		$insertData 	=	array();
		foreach($images_arr as $url){
			$insertData[]	=	array('gid'=>$goodsId, 'image_url'=>$url);
		}
		$nums 	=	Db::table('goods_image')->insertAll($insertData);
		return $nums;
	}

	/**
	 * 保存base64编码之后的图片
	 * @param  [type] $uid        [description]
	 * @param  [type] $goods_id   [description]
	 * @param  [type] $image_data [description]
	 * @param  [type] $type       [description]
	 * @return [type]             [description]
	 */
	function save_goods_long_img($uid, $goods_id, $image_data, $type){
		$file_path	=	ROOT_PATH.'public/static/goods/'.$uid.'/';
		$file 		=	$goods_id.'.'.$type;

		$save  		=	saveGoodsBase64Img($file_path, $file, $image_data);
		if($save){
			return '/static/goods/'.$uid.'/'.$goods_id.'.'.$type;
		}else{
			self::$code 	=	902;
			self::$msg 		=	'保存商品长图失败!';
			return false;
		}
		
	}

	/**
	 * 修改商品信息
	 * @return [type] [description]
	 */
	public function edit(){
		$goods_id 	=	intval(trim(input('get.id')));
		if(!$goods_id){
			$this->error('请选择要修改的产品信息!', '/index/goods');
		}
		$uid 	=	self::$login_user['id'];

		$goods_info 	=	Db::table('goods')->where(['id'=>$goods_id])->find();
		if(empty($goods_info)){
			$this->error('找不到对应的产品!', '/index/goods');
		}

		if($goods_info['status'] != 4){
			$this->error('该产品不允许修改!', '/index/goods');
		}

		if($goods_info['uid'] != $uid){
			$this->error('该产品不属于你!', '/index/goods');
		}

		$images_arr 	=	Db::table('goods_image')->field('gid,image_url')->where('gid', $goods_id)->select();
		$extends_info 	=	getGoodsExtendsInfo($goods_id, $goods_info['type']); ##获取产品扩展信息
		$data 	=	array(
						'common_type'	=>	$goods_info['common_type'],
						'goods_type'	=>	$goods_info['type'],
						'goods_info'	=>	$goods_info,
						'images_arr'	=>	$images_arr,
						'extends'		=>	$extends_info,
						'web_title'		=>	'修改宝贝'
					);
		$this->assign($data);
		return $this->fetch('publish_edit');
	}

	/**
     * 根据营销计划链接获取商品及优惠券信息
     * @return [type] [description]
     */
    public function get_yingxiao_goods(){
        $link 	=   trim(input('post.link'));
        if(!$link){
        	return returnAjaxMsg('701', '请输入营销计划链接!');
        }
        if(!preg_match('/s.click.taobao.com\/[a-zA-Z0-9]{7,}\??/', $link)){
        	return returnAjaxMsg('702', '营销计划链接不正确!');
        }
		
        //$real_url 	=	@get_headers($link);
		$real_url = curlGetLocation($link);
        if(!$real_url){
        	return returnAjaxMsg('703', '获取商品信息失败!');
        }

        //$coupon_info 	=	$this->get_yingxiao_coupon_info($real_url['5']);
        $coupon_info 	=	$this->get_yingxiao_coupon_info($real_url);
        if(!$coupon_info){
        	return returnAjaxMsg(self::$code, self::$msg);
        }
        $itemId 		=	$coupon_info['item']['itemId'];
        $res 			=	Db::table('goods')->field('id')->where('taobao_goodsId', $itemId)->find();
        if($res){
        	return returnAjaxMsg('704', '该商品已存在!');
        }

        $goods_info 	=	getGoodsInfo($itemId);
        $data 			=	array('coupon_info'=>$coupon_info, 'goods_info'=>$goods_info);
        return returnAjaxMsg(200, '获取成功', $data);
    }

    /**
     * 获取并检测营销计划优惠券信息
     * @param  [type] $coupon_link [description]
     * @return [type]              [description]
     */
    public function get_yingxiao_coupon_info($coupon_link){
		preg_match('/me=(.+)&?/', $coupon_link, $matches);
		if(empty($matches)){
			self::$code 	=	799;
        	self::$msg 		=	'地址不正确!';
        	return false;
		}
        $param_me 	=	$matches[1];
        $coupon_info=	getYingxiaoCouponInfo($param_me);
        if(!$coupon_info){
        	self::$code 	=	801;
        	self::$msg 		=	'获取优惠券信息失败!';
        	return false;
        }
        $coupon_info 	=	$coupon_info['result'];
        if($coupon_info['retStatus'] != 0){
        	self::$code 	=	802;
        	self::$msg 		=	'优惠券已失效!';
        	return false;
        }

        if(strtotime($coupon_info['effectiveEndTime']) < time()){
        	self::$code 	=	802;
        	self::$msg 		=	'优惠券已过期!';
        	return false;
        }
        
        $coupon_link 	=	'https://uland.taobao.com/coupon/edetail?me='.$param_me;
        $coupon_info['coupon_link']	=	$coupon_link;
        
        return $coupon_info;
    }
}