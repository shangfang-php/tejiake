<?php

// 应用公共文件
//获取随机数
function getRandTime(){
    $time = time();
    $date = @date('Y-m-d');
    switch($time){
        case $time< @strtotime($date." 8:00:00") && $time > @strtotime($date." 00:00:00"):
            $t = rand(8000,8999);
            break;
        case $time < @strtotime($date." 10:00:00") && $time > @strtotime($date." 8:00:00"):
            $t = rand(1000,1999);
            break;
        case $time < @strtotime($date." 12:00:00") && $time > @strtotime($date." 10:00:00"):
            $t = rand(2000,2999);
            break;
        case $time < @strtotime($date." 14:00:00") && $time > @strtotime($date." 12:00:00"):
            $t = rand(3000,3999);
            break;
        case $time < @strtotime($date." 18:00:00") && $time > @strtotime($date." 14:00:00"):
            $t = rand(4000,4999);
            break;
        case $time < @strtotime($date." 20:00:00") && $time > @strtotime($date." 18:00:00"):
            $t = rand(5000,5999);
            break;
        case $time < @strtotime($date." 22:00:00") && $time > @strtotime($date." 20:00:00"):
            $t = rand(6000,6999);
            break;
        case $time < @strtotime($date." 24:00:00") && $time > @strtotime($date." 22:00:00"):
            $t = rand(7000,7999);
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
