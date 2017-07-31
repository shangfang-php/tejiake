<?php
namespace app\common\model;
use think\Model;

/**
 * 用户模型MODEL
 */
class User extends Model{
	public function _initialize(){
		parent::_initialize();
	}

	public function check_sign($uid, $sign, $range_string){
		$secret_key 	=	$this->get_secret_key($uid);
		if(empty($secret_key)){
			return false;
		}

		$correct_sign 	=	$this->make_sign($secret_key, $range_string);
		return $correct_sign == $sign;
	}

	/**
	 * 获取用户的密钥
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	function get_secret_key($uid){
		$userInfo 	=	$this->field('secret_key')->where('id', $uid)->find()->toArray();
		return empty($userInfo) ? '' : $userInfo['secret_key'];
	}

	/**
	 * 生成密钥
	 * @param  [type] $secret_key   [description]
	 * @param  [type] $range_string [description]
	 * @return [type]               [description]
	 */
	function make_sign($secret_key, $range_string){
		return strtolower($secret_key.md5($range_string));
	}
}

?>