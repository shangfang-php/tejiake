<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

/**
 * 淘客个人中心
 */
class User extends UserCommon{

	public function _initialize(){
		parent::_initialize();
	}
	/**
	 * 淘客个人中心首页
	 * @return [type] [description]
	 */
	public function index(){

		$days	=	ceil( (time() - self::$login_user['create_time'] )/86400);
		return $this->fetch('user_index', compact('days'));
	}

	/**
	 * 基本资料展示视图
	 * @return [type] [description]
	 */
	public function basic(){
		$uid 	=	self::$login_user['id'];
		$teamInfo 	=	Db::table('merchant_apply_record')->where(array('id'=>$uid,'status'=>2))->find();
		$userInfo	=	Db::table('user')->where(array('id'=>$uid))->find();
		
		$data 	=	array(
						'login_user' 	=>	$userInfo,
						'teamInfo'		=>	$teamInfo,
					);
		return $this->fetch('user_basic', $data);
	}

	/**
	 * 上传头像
	 * @return [type] [description]
	 */
	function upload_img(){
		$uid 	=	self::$login_user['id'];
		$file = request()->file('image');
		if(empty($file)){
			return returnAjaxMsg(301,'没有文件上传');
		}

		// 移动到框架应用根目录/public/user_temp/临时目录下
    	$info = $file->validate(['size'=>10240,'ext'=>'jpg,png'])->move(ROOT_PATH . 'public/static' . DS . 'user_temp', '');
    	if($info){
	        return returnAjaxMsg(200, $info->getSaveName());
	    }else{
	        // 上传失败获取错误信息
	        return returnAjaxMsg(302, $file->getError());
	    }
	    exit;
	}

	/**
	 * 保存用户信息
	 * @return [type] [description]
	 */
	function save_info(){
		$img 	=	trim(input('post.img'));
		$team_info=	trim(input('post.team_info'));
		$update	=	array();

		$uid 	=	self::$login_user['id'];

		/**
		 * 将上传的临时文件移动到正式文件夹
		 */
		if(strpos($img, 'user_temp')){
			$file = ROOT_PATH.'public/'.$img;
			if(!is_file($file)){
				return returnAjaxMsg(301,'未找到对应的头像!');
			}
			$extend_info	=	pathinfo($img,PATHINFO_EXTENSION);
			$save_img		=	ROOT_PATH.'public/static/user/'.md5($uid).'.'.$extend_info;
			$info 	=	rename($file, $save_img);
			if(!$info){
				return returnAjaxMsg(302,'保存头像失败!');
			}
			//$update['head_img']	=	$save_img;
			Db::table('user')->where(array('id'=>$uid))->update(array('head_img'=>$save_img));
		}
		/**
		 * end
		 */

		$update['introduce']	=	nl2br($team_info);
		$info 	=	Db::table('merchant_apply_record')->where(array('uid'=>$uid,'status'=>2))->update($update);
		if($info === FALSE){
			$code 	=	303;
			$msg 	=	'保存用户信息失败';
		}else{
			$code 	=	200;
			$msg 	=	'保存用户信息成功!';
		}
		return returnAjaxMsg($code, $msg);
	}

	/**
	 * 修改用户密码
	 * @return [type] [description]
	 */
	function edit_password(){ 
		$oldPassword	=	trim(input('post.oldPassword'));
		$newPassword	=	trim(input('post.newPassword'));
		$newPassword1	=	trim(input('post.newPassword1'));
		if(!$oldPassword || !$newPassword || !$newPassword1){
			return returnAjaxMsg(401, '请输入密码!');
		}

		if($newPassword != $newPassword1){
			return returnAjaxMsg(402, '两次密码不一致');
		}

		$uid 	=	self::$login_user['id'];
		$where	=	array('id'=>$uid);

		$userInfo=	Db::table('user')->field('password')->where($where)->find();
		if($userInfo['password'] != md5($oldPassword)){
			return returnAjaxMsg(403, '原密码错误!');
		}

		$data['password']	=	md5($newPassword);
		$info 	=	Db::table('user')->where($where)->update($data);
		if($info === FALSE){
			$code 	=	404;
			$msg 	=	'修改密码失败';
		}else{
			$code 	=	200;
			$msg 	=	'修改密码成功';
		}
		return returnAjaxMsg($code, $msg);
	}

	/**
	 * 用户淘宝帐号授权设置界面
	 */
	function set(){
		$uid 	=	self::$login_user['id'];
		$userInfo=	Db::table('user')->where(array('id'=>$uid))->find();
		self::$login_user	=	$userInfo;

		$where	=	array('uid'=>$uid, 'is_delete'=>0);
		$pids	=	Db::table('user_pid_records')->field('pid')->where($where)->find();

		$taobao_account	=	$userInfo['taobao_account'];

		$data	=	array('pids'=>$pids, 'taobao_account'=>$taobao_account);
		return $this->fetch('user_set', $data);
	}

	/**
	 * 保存帐号信息并授权淘宝登录
	 * @return [type] [description]
	 */
	function check_auth(){
		$taobao_account 	=	trim(input('post.taobao_account'));
		if(!$taobao_account){
			return returnAjaxMsg(501, '请输入淘宝帐号!');
		}

		$uid 				=	self::$login_user['id'];
		$info 	=	Db::table('user')->where(array('id'=>$uid))->update(array('taobao_account'=>$taobao_account)); ##保存淘宝帐号信息
		if($info === FALSE){
			return returnAjaxMsg(502, '保存淘宝帐号失败!');
		}

		$auth_url	=	$this->get_taobao_auth_url($taobao_account);
		return returnAjaxMsg(200, $auth_url);
	}

	/**
	 * 生成淘宝授权登录URL
	 * @param  [type] $taobao_account [description]
	 * @return [type]                 [description]
	 */
	function get_taobao_auth_url($taobao_account){
		$state	=	base64_encode( $taobao_account.'|'.md5( $taobao_account.'eeetui' ) );
		$url 	=	'https://oauth.taobao.com/authorize?response_type=code&client_id=23565277&redirect_uri=http://api.00o.cn/oauth.php?return=1&state='.$state.'&view=web';
		return $url;
	}

	/**
	 * 接收授权返回信息
	 * @return [type] [description]
	 */
	function return_auth(){
		$json	=	$_GET['json'];
		if($json){
			echo '<script type="text/javascript">window.parent.location.reload();</script>'
		}
	}

	/**
	 * 保存用户PID
	 * @return [type] [description]
	 */
	function save_pid(){
		$pid 	=	trim(input('post.pid'));
		if(!$pid){
			return returnAjaxMsg(601, '请输入PID');
		}

		if(!preg_match('/^mm_\d+_\d+_\d+$/', $pid)){
			return returnAjaxMsg(602, 'PID格式不正确!');
		}

		$uid 	=	self::$login_user['id'];
		$where 	=	array('uid'=>$uid);
		$res 	=	Db::table('user_pid_records')->field('id')->where($where)->find();
		if(!$res){
			$data 	=	array('uid'=>$uid, 'pid'=>$pid);
			$info 	=	Db::table('user_pid_records')->insert($data);
		}else{
			$info 	=	Db::table('user_pid_records')->where(array('id'=>$res['id']))->update(array('pid'=>$pid));
		}

		if($info === FALSE){
			$code = 603;
			$msg  = '保存PID失败!';
		}else{
			$code = 200;
			$msg  = '保存成功!';
		}
		return returnAjaxMsg($code, $msg);
	}

}