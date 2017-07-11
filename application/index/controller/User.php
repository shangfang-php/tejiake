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
		$userInfo=	Db::table('user')->where(array('id'=>$uid))->find();
		$this->assign('login_user', $userInfo);
		return $this->fetch('user_basic');
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
			$update['head_img']	=	$save_img;
		}
		/**
		 * end
		 */

		$update['team_info']	=	nl2br($team_info);
		$info 	=	Db::table('user')->where(array('id'=>$uid))->update($update);
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

}