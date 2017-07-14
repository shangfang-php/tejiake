<?php
use think\Db;
// 应用公共文件
//获取随机数
function getRandTime(){
    $hour   =   date('G'); ##小时
    switch($hour){
        case $hour >= 0 and $hour < 8:
            $t  =   rand(3000, 4000);
            break;
        case $hour >= 8 and $hour < 10:
            $t = rand(6000,7000);
            break;
        case $hour >= 10 and $hour < 12:
            $t = rand(10000,11000);
            break;
        case $hour >= 12 and $hour < 14:
            $t = rand(11000,12000);
            break;
        case $hour >= 14 and $hour <= 20:
            $t = rand(12000,13000);
            break;
        default :
            $t = rand(9000,9999);
    }
    return $t;
}

function pswCrypt($psw){
    $psw = md5($psw);
    $salt = substr($psw,0,4);
    $psw = crypt($psw,$salt);
    return $psw;
}

function request_post($url = '', $param = '') {
    /* if (empty($url) || empty($param)) {
         return false;
     }*/
    if (empty($url) ) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_TIMEOUT,30);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
}
function request_get($url) {
    if (empty($url)) {
        return false;
    }
    //初始化
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $output;
}

/**
 * 返回ajax信息（json格式）
 * @return [type] [description]
 */
function returnAjaxMsg( $code = 200, $msg = '', $arr = array() ){
    $data   =   array('code'=>$code, 'msg'=>$msg);
    if(!empty($arr)){
        $data   =   array_merge($data, $arr);
    }
    return json($data);
}

/**
 * 检测手机号格式
 * @param  [type] $phone [手机号]
 * @return [type] bool
 */
function checkPhone($phone){
    return preg_match("/^1[3587]\d{9}$/", $phone);
}

//获取当前URL
function getActionUrl(){
    $module = request()->module();
    $controller = request()->controller();
    $action = request()->action();
    return $module.'/'.$controller.'/'.$action;

}

/**
 * 获取通用配置
 * @return [type] [description]
 */
function getCommonConfig($name){
    $content    =   Db::table('common_config')->field('content')->where(array('name'=>$name))->find();
    $return     =   $content ? json_decode($content['content'], TRUE) : '';
    return $return;
}

/**
 * 更新用户积分
 * @param  [type] $uid       [用户UID]
 * @param  [type] $score     [更新积分数 带符号 增加无需带+ 扣除需带-]
 * @param  [type] $scoreType [积分变更类型]
 * @return [type]            [description]
 */
function updateUserScore($uid, $score, $scoreType, $remark='', $operateUid = 0){
    $score      =   $score > 0 ? '+'.$score : $score;
    $update     =   array('score'=>['exp', 'score '.$score]);
    $info       =   Db::table('user')->where(array('id'=>$uid))->update($update);
    if($info === FALSE){
        return FALSE;
    }

    $info   =   saveScoreChangeRecords($uid, $score, $scoreType, $remark, $operateUid); ##记录积分变更日志
    return $info;
}

/**
 * 记录积分变更日志
 * @param  [type] $uid       [用户ID]
 * @param  [type] $score     [变化积分]
 * @param  [type] $scoreType [变更类型]
 * @return [type]            [description]
 */
function saveScoreChangeRecords($uid, $score, $scoreType, $remark = '', $operateUid = 0){
    $score  =   str_replace('+', '', $score);
    $data   =   array('uid'=>$uid, 'score'=>$score, 'type'=>$scoreType, 'operator'=>$operateUid, 'time'=>time());
    if($remark){
        $data['remark'] =   $remark;
    }

    $info   =   Db::table('user_score_record')->insert($data);
    return $info === FALSE ? FALSE : TRUE;
}