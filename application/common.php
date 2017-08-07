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
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'https://www.baidu.com');
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
function checkQq($qq) {
    $pattern = '/^[1-9]\d{4,8}$/';
    return preg_match($pattern, $qq);
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
function updateUserScore($uid, $score, $scoreType, $remark='', $operateUid = 0, $temp_score = 0, $gid = 0){
    $score      =   $score >= 0 ? '+'.$score : $score;
    $temp_score =   $temp_score >= 0 ? '+'.$temp_score : $temp_score;
    $update     =   array('score'=>['exp', 'score '.$score], 'temp_score'=>['exp', 'temp_score '.$temp_score]);
    $info       =   Db::table('user')->where(array('id'=>$uid))->update($update);
    if($info === FALSE){
        return FALSE;
    }

    $info   =   saveScoreChangeRecords($uid, $score, $scoreType, $remark, $operateUid, $temp_score, $gid); ##记录积分变更日志
    return $info;
}

/**
 * 记录积分变更日志
 * @param  [type] $uid       [用户ID]
 * @param  [type] $score     [变化积分]
 * @param  [type] $scoreType [变更类型]
 * @return [type]            [description]
 */
function saveScoreChangeRecords($uid, $score, $scoreType, $remark = '', $operateUid = 0, $temp_score = 0, $gid = 0){
    $score  =   str_replace('+', '', $score);
    $data   =   array('uid'=>$uid, 'score'=>$score, 'temp_score'=>$temp_score,'type'=>$scoreType, 'operator'=>$operateUid, 'time'=>time());
    if($remark){
        $data['remark'] =   $remark;
    }

    if($gid){
        $data['gid']    =   $gid;
    }

    $info   =   Db::table('user_score_record')->insert($data);
    return $info === FALSE ? FALSE : TRUE;
}

/**
 * 调用阿里淘宝客接口获取商品信息
 * @return [type] [description]
 */
function getGoodsInfo($goods_id){
    $url    =   'http://tbapi.00o.cn/good.php';
    $params =   'num_iids='.$goods_id.'&platform=1';
    $content=   request_post($url, $params);
    $content=   $content ? json_decode($content, TRUE) : '';
    return $content ? $content['results']['n_tbk_item'] : '';
}

/**
 * 获取链接中的商品ID
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function getGoodsId($url){
    preg_match('/[&\?]id=(\d+)&?/', $url, $matches);
    return $matches ? $matches[1] : '';
}

/**
 * 获取优惠券信息
 * @return [type] [description]
 */
function getCouponInfo($activityId, $goods_id){
    $url    =   'https://uland.taobao.com/cp/coupon?activityId='.$activityId.'&itemId='.$goods_id;
    $content=    request_get($url);
    $content=   $content ? json_decode($content, true) : '';
    return $content;
}

/**
 * 获取优惠券活动ID
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function getCouponActivityId($url){
    preg_match('/[&\?](activityId|activity_id)=(\w+)&?/', $url, $matches);
    return $matches ? $matches[2] : '';
}

/**
 * 获取营销计划的优惠券信息
 * @param  [type] $param_me [description]
 * @return [type]           [description]
 */
function getYingxiaoCouponInfo($param_me){
    $url    =   'https://uland.taobao.com/cp/coupon?me='.$param_me;
    $coupon_info    =   request_get($url);
    return $coupon_info ? json_decode($coupon_info, TRUE) : '';
}

/**
 * 获取发布产品需要的积分
 * @param  [type] $real_price  [券后价]
 * @param  [type] $coupon_nums [优惠券剩余未领取总数]
 * @return [type]              [description]
 */
function countGoodsScore($real_price, $coupon_nums){
    $single_score   =   0;
    switch($real_price){
        case $real_price > 0 and $real_price <= 10:
            $single_score   =   1;
            break;
        case $real_price > 10 and $real_price <= 20:
            $single_score   =   3;
            break;
        case $real_price > 20 and $real_price <= 50:
            $single_score   =   5;
            break;
        case $real_price > 50 and $real_price <= 100:
            $single_score   =   7;
            break;
        case $real_price > 100;
            $single_score   =   9;
            break;
    }
    $need_scores    =   $coupon_nums * $single_score;
    return compact('single_score', 'need_scores');
}

/**
 * 初始化商品信息
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function init_goods_data($data){
    $goods_validate =   \think\Loader::validate('Goods');
    $goods_id       =   getGoodsId($data['link']); ##获取链接中的商品ID
    if(!$goods_id){
        return '商品链接不正确!';
    }
    $goods_info     =   getGoodsInfo($goods_id); ##获取商品信息
    if(!$goods_info){
        return '未获取到商品信息!';
    }
    $data['taobao_goodsId'] =   $goods_id;
    $data['title']  =   $goods_info['title'];
    $data['sell_num']=  $goods_info['volume'];
    $data['price']  =   $goods_info['zk_final_price'];
    $images_arr     =   $goods_info['small_images']['string']; ##图片集合
    $data['is_tmall']=  checkGoodsIsTmall($data['link']);

    if($data['common_type'] == 1){
        $activityId     =   getCouponActivityId($data['coupon_link']); ##获取优惠券活动ID
        if(!$activityId){
            return '优惠券链接不正确!';
        }
        $coupon_info    =   getCouponInfo($activityId, $goods_id);
        $data['coupon_link']    =   'https://market.m.taobao.com/apps/aliyx/coupon/detail.html?wh_weex=true&activity_id='.$activityId.'&seller_id='.$goods_info['seller_id'];
        $data['coupon_id']  =   $activityId;
    }else{
        preg_match('/me=(.+)&?/', $data['coupon_link'], $matches);
        if(!$matches){
            return '优惠券链接不正确';
        }
        $param_me   =   $matches[1];
        $coupon_info=   getYingxiaoCouponInfo($param_me);
    }
    
    if(!$coupon_info){
        return '未获取到优惠券信息!';
    }
    if($coupon_info['result']['retStatus'] != '0'){
        return '优惠券已失效!';
    }
    $coupon_info    =   $coupon_info['result'];
    if(time() >= strtotime($coupon_info['effectiveEndTime'])){
        return '优惠券已过期!';
    }

    if($data['type'] == 3){ ##过夜单
        $next_day_start =   strtotime(date('Y-m-d', strtotime('+1 day')));
        $diff_time      =   strtotime($coupon_info['effectiveStartTime']) - $next_day_start;
        if($diff_time <0 || $diff_time > 86399){
            return '过夜单只能提交第二天开始的优惠券商品!';
        }
        $data['show_time']  =   strtotime(date('Y-m-d 20:00:00'));
    }

    $data['coupon_limit']   =   $coupon_info['startFee'];
    $data['coupon_money']   =   $coupon_info['amount'];
    $data['coupon_start_time']= strtotime($coupon_info['effectiveStartTime']);
    $data['coupon_end_time']=   strtotime($coupon_info['effectiveEndTime']);
    $data['real_money']     =   bcsub($data['price'], $data['coupon_money'], 2);
    
    switch($data['type']){
        case 2: ##限时抢购
            $scene  =   'flash_sale';
            break;
        case 5: ##视频单
            $scene  =   'video';
            break;
        default:
            $scene  =   'default';
    }

    $check  =   $goods_validate->scene($scene)->check($data);
    if(!$check){
        return $goods_validate->getError();
    }else{
        $extend     =   array();
        $data['taoke_money']    =   $data['taoke_money_percent'] * $data['real_money'] * 0.01;
        return array('data'=>$data, 'images_arr'=>$images_arr, 'extend'=>$extend);
    }
}

/**
 * 检测用户发布的商品类型是否免审
 * @return [type] [description]
 */
function check_goods_mianshen($uid, $goods_type){
    $userInfo   =   Db::table('merchant_apply_record')->field('free_trial')->where(['uid'=>$uid,'status'=>2])->find();
    if(!empty($userInfo) && $userInfo['free_trial']){
        return in_array($goods_type, json_decode($userInfo['free_trial']));
    }else{
        return false;
    }
}

/**
 * 保存base64格式的图片数据
 * @param  [type] $file_path  [description]
 * @param  [type] $file       [description]
 * @param  [type] $image_data [description]
 * @return [type]             [description]
 */
function saveGoodsBase64Img($file_path, $file,$image_data){
    clearstatcache();
    if(!is_dir($file_path)){
        $mk     =   mkdir($file_path, '0777', true);
        if(!$mk){
            return false;
        }
    }
    $file   =   $file_path.$file;
    if (file_put_contents($file, base64_decode($image_data) ) ){
        return true;
    }else{
        return false;
    }
}

/**
 * 保存商品直播信息
 * @param  [type] $goods_id  [description]
 * @param  [type] $live_info [description]
 * @return [type]            [description]
 */
function saveGoodsLiveInfo($uid, $goods_id, $live_info){
    $info       =   Db::table('goods_live_extends')->where(['gid'=>$goods_id])->delete();
    if($info === false){
        return false;
    }

    $file_path  =   ROOT_PATH.'public/static/live/'.$uid.'/';
    $data       =   array();
    foreach($live_info as $key=>$val){
        $url    =   $val['url'];
        if(preg_match('/^(data:\s*image\/(.+);base64,)/', $url, $result)){
            $img_data   =   str_replace($result[1], '', $url);
            $file       =   $goods_id.'_'.$key.'.'.$result[2];
            $save       =   saveGoodsBase64Img($file_path, $file, $img_data);
            if(!$save){
                return false;
            }
            $url    =   '/static/live/'.$uid.'/'.$file;
        }
        $data[]   =   array('gid'=>$goods_id,'url'=>$url, 'content'=>$val['content'], 'type'=>$val['type'],'create_time'=>time());
    }
    $info   =   Db::table('goods_live_extends')->insertAll($data);
    return $info;
}

/**
 * 保存视频单链接信息
 * @param  [type] $uid      [description]
 * @param  [type] $goods_id [description]
 * @param  [type] $url      [description]
 * @return [type]           [description]
 */
function saveGoodsVideInfo($uid, $goods_id, $url){
    $info       =   Db::table('goods_video_extends')->where(['gid'=>$goods_id])->delete();
    if($info === false){
        return false;
    }
    $data   =   ['uid'=>$uid,'gid'=>$goods_id, 'video_url'=>$url];
    $info   =   Db::table('goods_video_extends')->insertGetId($data);
    return $info;
}

/**
 * reverse_array()
 * 从一个数组中拿出指定的字段作为键名，另外一个字段作为值生成数组
 * @param mixed $array
 * @param mixed $val_filter  作为值的字段
 * @param string $key_filter 作为键名的字段 为空则为key值
 * @param bool $isMulti 是否生成多维数组
 * @return void
 */
function reverse_array($array, $key_filter, $val_filter, $isMulti = FALSE){
    $return     =   array();
    if(empty($array)){
        return $return; //增加拦截传递为空数组的情况
    }
    foreach($array as $key=>$val){
        $k  =   $key_filter ? $val[$key_filter] : $key;
        $v  =   $val_filter ? $val[$val_filter] : $val;
        if($isMulti){
            $return[$k][]   =   $v;
        }else{
            $return[$k]     =   $v;
        }
    }
    return array_filter($return);
}

/**
 * 获取产品扩展信息
 * @param  [type] $goods_id   [description]
 * @param  [type] $goods_type [description]
 * @return [type]             [description]
 */
function getGoodsExtendsInfo($goods_id, $goods_type){
    $extends    =   array();
    switch($goods_type){
        case 1: ##爆款
        case 2: ##限时抢购
        case 3: ##过夜单
            break;
        case 4: ##直播单
            $extends    =   Db::table('goods_live_extends')->where(['gid'=>$goods_id])->select();
            $extends    =   array_pad($extends,5, array());
            break;
        case 5: ##视频单
            $extends    =   Db::table('goods_video_extends')->where(['gid'=>$goods_id])->find();
            break;
        default:
    }
    return $extends;
}

/** 
 * 把秒数转换为时分秒的格式 
 * @param Int $times 时间，单位 秒 
 * @return String 
 */  
function secondToTime($times){  
        $result     =   array('hour'=>0,'minute'=>0,'second'=>0);
        if($times>0) {  
                $hour   =   floor($times/3600);
                $hour   =   $hour < 10 ? '0'.$hour : $hour;

                $minute =   floor(($times-3600 * $hour)/60);
                $minute =   $minute < 10 ? '0'.$minute : $minute;

                $second =   floor((($times-3600 * $hour) - 60 * $minute) % 60);
                $second =   $second < 10 ? '0'.$second : $second;
                
                $result = compact('hour','minute','second'); 
        }
        return $result;  
}

/**
 * 检测链接是否是天猫
 * @param  [type] $link [description]
 * @return [type]       [description]
 */
function checkGoodsIsTmall($link){
    $check  =   strpos($link, 'tmall');
    return $check === false ? 0 : 1;
}

/**
 * 生成注册时的密钥
 * @return [type] [description]
 */
function makeSecretKey(){
    $string     =   uniqid(rand(1,1000)).rand(1,10000).rand(1,10000).rand(1,10000);
    return md5($string);
}

/**
 * 完结商品
 * @Author   Gary
 * @DateTime 2017-08-03T15:34:08+0800
 * @param    [type]  $goods_id   [商品表ID]
 * @param    [type]  $score_type [积分类型 5商品完结 7商品下架]
 * @param    string  $remark     [备注]
 * @param    string  $goods_info [商品信息]
 * @return   [type]  [description]
 */
function endGoods($goods_id, $score_type = 5, $remark = '', $goods_info = '', $operator = 0){
    if(!$goods_info){
        $goods_info     =   Db::table('goods')->where('id', $goods_id)->find();
    }
    $uid            =   $goods_info['uid'];
    $goods_score    =   $goods_info['scores'];
    $goods_score    =   $goods_score > 0 ? '-'.$goods_score : $goods_score;
    $remark     =   $remark ? $remark : '商品结算';

    $info       =   updateUserScore($uid, 0, $score_type, $remark, $operator, $goods_score, $goods_id);
    if(!$info){
        return false;
    }

    $score_type == 5 && $update_status = 3; ##商品过期状态
    $score_type == 7 && $update_status = 5; ##商品下架状态
    $info   =   Db::table('goods')->where('id', $goods_id)->update(['status'=>$update_status]);
    if($info === false){
        return false;
    }

    $info   =   Db::table('goods_collect')->where('gid', $goods_id)->update(['is_delete'=>1]);
    if($info === false){
        return false;
    }
    return TRUE;
}