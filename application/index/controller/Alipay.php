<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

/**
 * 支付宝支付接口
 */
class Alipay extends Controller{
	private $alipay_config;
	public function _initialize(){
		parent::_initialize();

		$this->alipay_config = array(
									'service'	=>	'create_direct_pay_by_user',
						            'partner'	=>	'2088411530199942',
						            'key'		=>	'3w4aeizl5usxnnzalc7a3cygyu0jicfr',
						            'sign_type'	=>	'MD5',
						            'input_charset'=>'UTF-8',
						            'seller_email'=>'yeebai123@163.com',
						            'notify_url'=>	'http://ceshi.tejiake.com/index/alipay/receive_alipay_result',
						            'return_url'=>	'http://ceshi.tejiake.com/index/charge/index',
						            'payment_type'=> 1,
						            'transport'	=>	'http',
						            'cacert'	=>	getcwd().'/cacert.pem',
						        );
	}

	/**
	 * 提交支付宝充值信息
	 * @return [type] [description]
	 */
	function charge(){
		$uid		=	intval(input('get.uid'));
		$orderId	=	intval(input('get.id'));
		if(empty($orderId)){
			$this->error('非法操作!', '/index/charge/index');
		}
		$orderInfo	=	Db::table('user_charge_records')->where(array('id'=>$orderId))->find();
		if(!$orderInfo){
			$this->error('无充值信息!', '/index/charge/index');
		}

		if($orderInfo['uid'] != $uid){
			$this->error('非法操作!', '/index/charge/index');
		}

		//构造要请求的参数数组，无需改动
		$parameter = array(
			'service'		=> $this->alipay_config['service'], ##接口名
			'partner'       => $this->alipay_config['partner'],
			'key'			=> $this->alipay_config['key'],
			'seller_id'		=> $this->alipay_config['partner'],
			'input_charset'	=> $this->alipay_config['input_charset'],
			'payment_type'	=> $this->alipay_config['payment_type'],
			'notify_url'	=> $this->alipay_config['notify_url'],
			'return_url'	=> $this->alipay_config['return_url'],
			'sign_type'		=> $this->alipay_config['sign_type'],
			'out_trade_no'	=> $orderInfo['id'],
			'subject'		=> $orderInfo['description'],
			'total_fee'		=> $orderInfo['money'],
			'body'			=> $orderInfo['description'],
			'_input_charset'=> $this->alipay_config['input_charset'],
		);

		include_once EXTEND_PATH.'alipay/alipay_submit.class.php';
		//建立请求
		$alipaySubmit = new \AlipaySubmit($parameter);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
	}
	
	/**
	 * 接收支付宝充值结果
	 * @return [type] [description]
	 */
	function receive_alipay_result(){
		include_once EXTEND_PATH.'alipay/alipay_notify.class.php';
		$alipayNotify 	= new \AlipayNotify($this->alipay_config);
		$verify_result 	= $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
			$pay_money	  = $_POST['total_fee']; ##付款金额

			##付款成功
			if($trade_status == 'TRADE_SUCCESS'){
				$where		=	array('id'=>$out_trade_no);
				$orderInfo	=	Db::table('user_charge_records')->where($where)->find();
				if(!$orderInfo || $orderInfo['status'] != 1){
					echo 'fail';
					exit;
				}else{
					$update	=	array(
									'ali_trade_no'	=>	$trade_no,
									'pay_money'		=>	$pay_money,
									'end_time'		=>	time(),
								);
					Db::startTrans();
					if($pay_money == $orderInfo['money']){ ##付款金额一致
						$update['status']	=	2;
						$info 	=	Db::table('user_charge_records')->where($where)->update($update);
						if($info === FALSE){
							Db::rollback();
							echo 'fail';exit;
						}

						$score	=	$orderInfo['score'] + $orderInfo['give_score'];
						$info 	=	updateUserScore($orderInfo['uid'], $score, 1, $orderInfo['description'], $orderInfo['uid']);
						if(!$info){
							Db::rollback();
							echo 'fail';
							exit;
						}

					}else{
						$update['status']	=	3;
						$info 	=	Db::table('user_charge_records')->where($where)->update($update);
						if(!$info){
							Db::rollback();
							echo 'fail';exit;
						}
					}
					Db::commit();
				}
			}
		        
			echo "success"; ##接收成功
		}else {
		    //验证失败
		    echo "fail";
		}
	}
}