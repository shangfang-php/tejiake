<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

/**
 * 淘客充值中心
 */
class Charge extends UserCommon{
	protected $charge_price;

	public function _initialize(){
		parent::_initialize();
		$this->charge_price = getCommonConfig('charge_price');
		$this->assign('charge_price', $this->charge_price);
	}
	/**
	 * 充值中心首页
	 * @return [type] [description]
	 */
	public function index(){

		return $this->fetch('charge_index');
	}

	/**
	 * 充值订单提交
	 * @return [type] [description]		
	 */
	function submit_order(){
		$uid			=	self::$login_user['id'];
		$charge_money 	=	trim(input('post.price')); ##充值金额
		if(!isset($this->charge_price[$charge_money])){ ##不存在该价格配置
			return returnAjaxMsg(301, '非法提交！');
		}
		$charge_info	=	$this->charge_price[$charge_money];
		
		$order 	=	array(
						'money'	=>	$charge_money,
						'score'	=>	$charge_info['score'],
						'give_score'=>$charge_info['give'],
						'create_time'=>	time(),
						'status'	=>	1,
						'uid'		=>	$uid,
						'description'=> '充值'.$charge_info['score'].'积分'.( $charge_info['give'] ? ',赠送'.$charge_info['give'].'积分' : ''),
					);
		
		$insertId 	=	Db::table('user_charge_records')->insertGetId($order);
		if(!$insertId){
			return returnAjaxMsg(302, '保存充值信息失败');
		}else{
			return returnAjaxMsg(200, '/index/alipay/charge?id='.$insertId.'&uid='.$uid);
		}
	}

}