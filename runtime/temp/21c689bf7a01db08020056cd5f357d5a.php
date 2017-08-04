<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"D:\wamp\www\tejiake\public/../application/index\view\index\goodsinfo.html";i:1501745318;s:71:"D:\wamp\www\tejiake\public/../application/index\view\public\header.html";i:1501753766;s:71:"D:\wamp\www\tejiake\public/../application/index\view\public\footer.html";i:1501745318;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
            首页
        </title>
        <link rel="stylesheet" href="/static/index/css/common.css?v=1.0.1">
        <link rel="stylesheet" type="text/css" href="/static/index/css/index.css?v=1.0.1">
        <link rel="stylesheet" href="/static/index/css/sc.css?v=1.0.1">
        <link rel="stylesheet" href="/static/index/css/style.css?v=1.0.1">
        <link rel="stylesheet" href="/static/index/css/video-js.css">
        <link rel="stylesheet" href="/static/index/css/loaders.css">
        <style type="text/css">
        .active{
            background:#c0c0c0!important;
        }
        /*弹出遮罩层*/
        .msg_body{
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            zoom: 1;
            background: #2f2e2e;
            z-index: 999;
            width:100%;
            height:100%;
            filter:alpha(opacity=50); /*IE滤镜，透明度50%*/
            -moz-opacity:0.5; /*Firefox私有，透明度50%*/
            opacity:0.5;/*其他，透明度50%*/
        }
        .msg_div {
            display:none;
            text-align: left;
            width: 562px;
            height: 395px;
            overflow: auto;
            position: absolute;
            background: #fff;
            top: 0;
            left: 0;
            z-index: 9999999;
            border-radius: 7px;
        }
        .msg_div p {
            text-align: center;
        }
        .msg_div p input{
            border: 1px solid #1a99fb;
           
            background: #fff;
            width: 90px;
            height: 30px;
            color: #1a99fb;
            line-height: 30px;
                margin-right: 25px;
            margin-top: 5px;
            border-radius: 7px;

        }
        .msg_div .close{
            position: absolute;
            right: 2px;
            top: 2px;
            z-index: 9;
            cursor: pointer;
        }
        .datetime{
            width:140px!important;
        }
        </style>
    </head>
    <body style="background: #f6f6f6;min-width: 1400px;width: 100%;">
        <header id="header" class="">
            <div class="head-mid">
                <div class="content white">
                    <div class="logo">
                        <a href="/">
                            <img src="/static/index/images/logo.png" alt="特价客">
                        </a>
                    </div>
                    <form action="<?php echo url('/index/index/search'); ?>" method="get">
                    <div class="search">
                        <div class="search-input">
                            <input type="text" name="keywords" placeholders="请输入商品关键字或者商品ID" onblur="if(placeholders=='') {placeholders='请输入商品关键字或者商品ID'}" onFocus="if(placeholders=='请输入商品关键字或者商品ID'){placeholders=''}"#ED1C24">
                            <span>
                                <img src="/static/index/images/shousuo.png" alt="">
                            </span>
                        </div>
                    </div>
                    <input type="submit" value="搜索">
                    </form>
                    <div class="admin">
                        <?php if($login_user): ?>
                            <!--登录成功之后-->
                            <div class="admin-ming">
                                  <span class="fr baiid" ><img src="/static/index/images/baiid.png" alt="" ></span>
                                  <span class="fl f12 ming" ><a href="<?php echo url('user/index'); ?>" >个人中心</a></span>
                            </div>
                            <div class="admin-an" style="position: absolute;" id="admin-an">
                                  <span class="fr baiid" ><img src="/static/index/images/exit.png" alt="" ></span>
                                  <span class="fl f12 ming" ><a href="<?php echo url('login/logout'); ?>" >退出登录</a></span>  
                            </div>
                        <?php else: ?>
                            <div class="admin-me" >
                                <a href="/register"><div class="reg">注册</div></a>
                                <a href="/user_login"> <div class="log">登录</div></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="clear: both">
                    </div>
                </div>
            </div>
            <div class="head-nav">
                <nav>
                    <ul>
                        <li class="bkd <?php if($goods_type == 'hot'): ?>yuan<?php endif; ?>" >
                            <a href="/">
                                <img src="/static/index/images/baok.png" alt="">
                                <div class="nav-dec">
                                    <strong>
                                        爆款单
                                    </strong>
                                    <p>
                                        单日已领3000券
                                    </p>
                                </div>
                            </a>
                        </li>
                        <li class="xsqg <?php if($goods_type == 'flash_sale'): ?>yuan<?php endif; ?>">
                            <a  title="" href="/flash_sale">
                                <img src="/static/index/images/xs.png" alt="">
                                <div class="nav-dec">
                                    <strong>
                                        限时抢购
                                    </strong>
                                    <p>
                                        聚划算、淘抢购
                                    </p>
                                </div>
                            </a>
                        </li>
                        <li class="zbd <?php if($goods_type == 'live'): ?>yuan<?php endif; ?>">
                            <a title="" href="/live.html">
                                <img src="/static/index/images/zb.png" alt="">
                                <div class="nav-dec">
                                    <strong>
                                        直播单
                                    </strong>
                                    <p>
                                        直播预告产品
                                    </p>
                                </div>
                            </a>
                        </li>
                        <li class="spd <?php if($goods_type == 'video'): ?>yuan<?php endif; ?>">
                            <a  title=""  href="/video.html">
                                <img src="/static/index/images/sp.png" alt="">
                                <div class="nav-dec">
                                    <strong>
                                        视频单
                                    </strong>
                                    <p>
                                        产品里面有视频
                                    </p>
                                </div>
                            </a>
                        </li>
                        <li class="gyd <?php if($goods_type == 'night'): ?>yuan<?php endif; ?>">
                            <a  title="" href="/night.html">
                                <img src="/static/index/images/yuel.png" alt="">
                                <div class="nav-dec">
                                    <strong>
                                        过夜单
                                    </strong>
                                    <p>
                                        每天0点抢购
                                    </p>
                                </div>
                            </a>
                        </li>
                        <div style="clear: both">
                        </div>
                    </ul>
                </nav>
            </div>
        </header>
<style type="text/css" src="__STATIC__/index/css/quan_detail.css"></style>
<link rel="stylesheet" href="__STATIC__/index/css/common.css">
<link rel="stylesheet" href="__STATIC__/index/css/jplayer.blue.monday.min.css">
<link rel="stylesheet" href="__STATIC__/index/css/quan_detail.css">
<style type="text/css" media="screen">
    .countdown div i {
        background: none;
        font-size: 16px;

        color: #1a99fb;
        width: 22px;
    }
    .countdown div.seperator {

        line-height: 52px;
        vertical-align: top;
        font-size: 16px;

    }
    .rong{
        padding: 11px 35px;
    }
    .rong p{
        text-align: left;
        height: 45px;
        line-height: 45px;
    }
    .rong p select{
        width: 170px;
        height: 30px;
        line-height: 30px;
        border-radius: 7px;
        padding-left: 10px;
    }
    .rong p span{
        text-align: right;

        width: 90px;
    }
    .rong p textarea{
        width: 300px;
        height: 96px;
        margin-top: 16px;
        padding: 10px;
        border-radius: 7px;
    }
    .rong p input{
        border-radius: 7px;
        background: #1a99fb;
        width: 90px;
        height: 30px;
        line-height: 30px;
        color: #fff;
        text-align: center;
    }
    .alert_pid{
        border: 1px solid #1a99fb;
        border-radius: 7px;
        width: 90px;
        height: 30px;
        line-height: 8px;
        padding: 10px 18px;
        background: #1a99fb;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
    }
    #change_pid p{
        line-height: 44px;
    }
</style>

<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/jquery.downCount.js"></script>
<!-- 加载每个页面内容-->
<div class="con">

    <div class="content" style="padding-top: 80px;">
        <div class="fl zuo" id="zuo">
            <div class="team">
                <div class="team-head">
                    <img style="width:100px;height:100px;" src="<?php if($data['head_img']): ?><?php echo $data['head_img']; else: ?>/static/index/images/tou.png<?php endif; ?>" alt="">
                    <p class="f16"><?php echo $data['nickname']; ?>&nbsp;
                        <span class="f12">
                            <?php if($data['mtype'] == 1): ?>个人
                            <?php else: ?>团队
                            <?php endif; ?>
                        </span>
                    </p>
                    <div class="tubiao">
                        <img src="__STATIC__/index/images/tubiao.png" alt="">
                    </div>

                </div>

                <div class="team-dec">
                    <div class="time fl">
                        <p class="f12">入驻时间<br/><span class="f16"><?php echo $data['addday']; ?>天</span></p>
                    </div>
                    <div class="line fl">
                        <p class="f12">在线商品<br/><span class="f16"><?php echo $data['line_goods_num']; ?>款</span></p>
                    </div>
                </div></div>
            <div class="xiang1" id="collect_action">
                <?php if($data['is_collect'] == 1): ?>
                <p>已收藏</p>
                <?php else: ?>
                <p><a href="javascript:;" class="add_cart_large btnCart">点击收藏</a></p>
                <?php endif; ?>
            </div>
        </div>


        <div class="fr you">
            <div class="ti-ti-box">
                <p class="fl f20 lan"><img src="__STATIC__/index/images/shipd.png" alt="">&nbsp;
                    <?php if($data['type'] == 1): ?>爆款单
                    <?php elseif($data['type'] == 2): ?>限时抢购&nbsp;<img src="__STATIC__/index/images/time-icon.png" alt="">&nbsp;
                    <div class="lia fl f16">
                        <div class="countdown" >
                            <div>剩余</div>
                            <div><i class="days">00</i></div>天
                            <div><i class="hours">00</i></div>
                            <div class="seperator" >:</div>
                            <div><i class="minutes">00</i></div>
                            <div class="seperator" >:</div>
                            <div><i class="seconds">00</i></div>
                            <div>结束</div>
                        </div>
                    </div>
                <script>
                    $('.countdown').downCount({
                        /*date: '7/29/2017 00:00:00',*/
                        date: "<?php echo date('m-d-Y H:i:s',$data['show_time']); ?>",
                        offset: +8
                    }, function () {
                        //alert('倒计时结束!');
                        var now = parseInt(new Date().getTime()/1000);
                        var end_time = <?php echo $data['end_time']; ?>;
                        if(now >= end_time){
                            $('.countdown').html('<p style="color: red;">活动已结束!</p>');
                        }else{
                            $('.countdown').html('<p style="color: red;">活动正在火热进行中!</p>');
                        }

                    });
                </script>
                <?php elseif($data['type'] == 3): ?>过夜单
                    <?php elseif($data['type'] == 4): ?>直播单
                    <?php elseif($data['type'] == 5): ?>视频单
                    <?php endif; ?>
                </p>
                <p class="fr red f14" onclick="show()" style="cursor: pointer;"><img src="__STATIC__/index/images/tuiguang-shibai.png" alt="" >&nbsp;商品纠错</p>
            </div>
            <div class="you-content">
                <?php if($data['type'] == 5): ?>
                <div class="datu-box fl">
                    <div class="detail-wrap">
                        <div class="detail-content-l">
                            <div class="detail-content-l-main">
                                <div class="goods-img">
                                    <div class="goods-big-img">
                                        <div id="jp_container_1" data-video="<?php echo $extends_data['video_url']; ?>" data-post="<?php echo $data['main_img']; ?>" class="jp-video jp-video-360p" role="application" aria-label="media player">
                                            <div class="jp-type-single">
                                                <div id="jquery_jplayer_1" class="jp-jplayer" style="width: 100%; height: 288px;">
                                                    <img id="jp_poster_0" src="<?php if(!empty($goods_image[0]) == true): ?><?php echo $goods_image[0]['image_url']; else: ?><?php echo $data['main_img']; endif; ?>" style="width: 100%; height: 288px; display: inline;">
                                                    <video controls="controls" id="jp_video_0" src="<?php echo $extends_data['video_url']; ?>" style="width: 0; height: 0;">
                                                    </video>
                                                   <!-- <video  width="0" height="0" controls id="jp_video_0">
                                                        <source src="<?php echo $extends_data['video_url']; ?>" type="video/mp4">
                                                    </video>-->
                                                </div>
                                                <div class="jp-gui">
                                                    <div class="jp-video-play" style="display: block;">
                                                        <button class="jp-video-play-icon" role="button" tabindex="0">
                                                            play
                                                        </button>
                                                    </div>
                                                    <div class="jp-interface">
                                                        <div class="jp-progress">
                                                            <div class="jp-seek-bar" style="width: 100%;">
                                                                <div class="jp-play-bar" style="width: 0%;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="jp-controls-holder">
                                                            <div class="jp-controls">
                                                                <button class="jp-play" role="button" tabindex="0">
                                                                    play
                                                                </button>
                                                                <button class="jp-stop" role="button" tabindex="0">
                                                                    stop
                                                                </button>
                                                            </div>
                                                            <div class="jp-volume-controls">
                                                                <button class="jp-mute" role="button" tabindex="0">
                                                                    mute
                                                                </button>
                                                                <div class="jp-volume-bar">
                                                                    <div class="jp-volume-bar-value" style="width: 50%;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="jp-toggles">
                                                                <button class="jp-repeat" role="button" tabindex="0">
                                                                    repeat
                                                                </button>
                                                                <button class="jp-full-screen" role="button" tabindex="0">
                                                                    full screen
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="jp-no-solution" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="goods-small-img">
                                        <ul>
                                            <li class="cur">
                                                <img src="<?php echo $data['main_img']; ?>" data-src="<?php echo $data['main_img']; ?>" data-500="<?php echo $data['main_img']; ?>">
                                                <div class="cover">
                                        <span>
                                            &nbsp;
                                        </span>
                                                </div>
                                            </li>

                                            <?php if(is_array($goods_image) || $goods_image instanceof \think\Collection || $goods_image instanceof \think\Paginator): $key = 0; $__LIST__ = $goods_image;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;if($key == 1): ?>
                                            <li>
                                                <img src="<?php echo $vo['image_url']; ?>"  data-src="<?php echo $vo['image_url']; ?>" data-500="<?php echo $vo['image_url']; ?>">
                                                <div class="cover">
                                        <span>
                                            &nbsp;
                                        </span>
                                                </div>
                                            </li>
                                            <?php endif; endforeach; endif; else: echo "" ;endif; ?>

                                           <!-- <li >
                                                <img src="" data-src="" data-500="">
                                                <div class="cover">
                                        <span>
                                            &nbsp;
                                        </span>
                                                </div>
                                            </li>-->



                                            <div class="jp-video-txt">
                                                <div class="jp-video-txt-list">

                                                    <div class="jp-video-txt-item">
                                                        <a href="<?php echo $extends_data['video_url']; ?>"
                                                           download="">
                                        <span class="jp-play-down">
                                            下载视频
                                        </span>
                                                        </a>
                                                    </div>
                                                    <div class="jp-video-txt-item">
                                                        <!--视频地址-->

                                                        <!--   <span class="jp-play-down" aria-label="http://www.ffquan.com/v/1519260b/">复制视频地址</span> -->
                                                        <input class="jp-play-down" aria-label="<?php echo $extends_data['video_url']; ?>" value="复制视频地址" type="button" style="background: #fff;color: #1a99fb"></input>
                                                    </div>

                                                </div>
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php else: ?>
                <div class="datu-box fl">
                    <div class="dadada">
                        <img src="<?php echo $data['main_img']; ?>" alt="">
                    </div>
                    <div class="choice-box">
                        <ul>
                            <li class="cur" >
                                <img src="<?php echo $data['main_img']; ?>" data-src="<?php echo $data['main_img']; ?>"   data-500="<?php echo $data['main_img']; ?>">
                                <div class="cover">
                                    <span>
                                        &nbsp;
                                    </span>
                                </div>
                            </li>
                            <?php if(is_array($goods_image) || $goods_image instanceof \think\Collection || $goods_image instanceof \think\Paginator): $i = 0; $__LIST__ = $goods_image;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <li>
                                <img src="<?php echo $vo['image_url']; ?>"  data-src="<?php echo $vo['image_url']; ?>" data-500="<?php echo $vo['image_url']; ?>">
                                <div class="cover">
                                        <span>
                                            &nbsp;
                                        </span>
                                </div>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <div class="clear"></div>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>


                <div class="choice-dec fl">
                    <h1>
                        <?php if($data['is_tmall'] == 1): ?><!--天猫-->
                        <img style="width: 23px;height: 23px;" src="__STATIC__/index/images/tianmao.png" alt="">
                        <?php else: ?>
                        <img src="__STATIC__/index/images/tao.png" alt="">
                        <?php endif; ?>
                        &nbsp;<?php echo $data['short_title']; ?></h1>
                    <input type="hidden" name="gid" id="gid" value="<?php echo $data['id']; ?>">
                    <div class="qu-qu-qu">
                        <p class="fl f14" style=" margin-left: 15px;">
                            券后 <span style="color: #fe5c30;font-size: 36px;">
                    ￥<?php echo $data['real_money']; ?>&nbsp;&nbsp;</span>原价：<i><?php echo $data['price']; ?></i>
                            <!--   <span class="f14 ac fr" style="    margin-left: 88px;">
                              销量 1212</span> -->
                        </p>
                        <p class="f14 ac fr" style="margin-right: 70px;">销量 <?php echo $data['sell_num']; ?></p>

                        <div class="clear">

                        </div>
                    </div>

                    <div class="ji-ji-ji">
                        <span class="un  quan">￥<?php echo round($data['coupon_money']); ?></span>
                        <span class="un">佣金：<i class="lan">￥<?php echo round($data['taoke_money'],2); ?></i></span>
                        <span class="un f12" style="border: none">计划 <?php echo round($data['taoke_money_percent'],2); ?>%</span>

                    </div>
                    <p class="f14" style="line-height: 36px;margin-top: 10px;text-indent: 15px;">已领<i style="color: #fe5c30;"><?php echo $data['coupon_apply_num']; ?></i>/<?php echo $data['coupon_total_num']; ?>&nbsp;张</p>
                    <p class="f14" style="line-height: 36px;text-indent: 15px;margin-bottom: 10px;">优惠券有效时间 <?php echo date('Y-m-d',$data['coupon_start_time']); ?>至<?php echo date('Y-m-d',$data['coupon_end_time']); ?></p>
                    <p class="dashed"></p>
                    <p class="f14 ac" style="line-height: 25px;padding: 10px 0 25px 0"><?php echo $data['title']; ?></p>
                    <p class="dashed"></p>
                    <p class="zhuan" onclick="show2()"><span>一键转链</span></p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tgwa-box">
                <h1><img src="__STATIC__/index/images/tgwa.png" alt="">&nbsp;&nbsp;推广文案&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="__STATIC__/index/images/fzline.png" alt=""></h1>
                <div class="tgwa-con media-text-area">
                    <div class="im-im fl">
                        <img style="width:279px;height: auto" src="<?php if(!empty($data['long_img']) == true): ?><?php echo $data['long_img']; else: ?><?php echo $data['main_img']; endif; ?>" alt="">
                    </div>
                    <div class="fl fm-fm" >
                        <p>
                            <?php echo $data['short_title']; ?><br>
                            【淘抢购<?php echo $data['price']; ?>元】券后【<?php echo $data['real_money']; ?>元】<br>
                            领券：<a href="<?php echo $data['coupon_link']; ?>" target="_blank" class="lan"><?php echo $data['coupon_link']; ?></a><br>
                            <div id="rob">
                                抢购：<br>
                                <a href="<?php echo $data['link']; ?>" target="_blank" class="lan"><?php echo $data['link']; ?></a><br>
                                <?php echo $data['guide_info']; ?>
                            </div>

                        </p>

                        <p class="yjfz">
                            <input class="fz fr copy f12" type="button" value=" 一键复制" >
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>

    </div>
    <div id="flyItem" class="fly_item">
        <img src="__STATIC__/index/images/sc.png" width="40" height="40">
    </div>


<!--弹出框-->
<div class="lbOverlay"  onclick="closeDiv()"></div>
<div class="hidden_pro_au tanchu" style="width: 500px;height: 366px;">
    <img src="__STATIC__/index/images/close.png" alt=""  class="close" onclick="closeDiv()">
    <h1>商品纠错</h1>
    <div class="rong">
        <p><span class="fl">商品标题：</span><?php echo $data['short_title']; ?></p>
        <p>
            <span class="fl">类型：</span>
            <select name="type" id="type">
                <option value="1">价格不对/优惠异常</option>
                <option value="2">佣金不对/计划取消</option>
                <option value="3">其他异常</option>
            </select>
        </p>
        <p style="height: 120px;">
            <span class="fl">详情描述：</span>
            <textarea name="" id="error_reason" placeholder="详情描述" ></textarea>
        </p>
        <p style="margin-top: 20px;"><span class="fl">&nbsp;</span><input type="button" name="" onclick="submit_error();" value="提交" ></p>
    </div>
</div>

<div class="zhuanlian tanchu " style="display:none;width: 240px;height: 220px;background: #fff;text-align: center;padding: 30px;">
    <img src="/static/index/images/close.png" alt=""  class="close" onclick="closeDiv()" style="position: absolute;right: 2px;top: 2px;z-index: 9;cursor: pointer;">
    <div class="">
     <img src="/static/index/images/dengl.png" alt="">
    </div>
    <!--前往登录-->
    <div id="change_pid">
        <p >你还没登录账号</p>
        <a href="/user_login"><input type="button" name="" value="前往登录" class="alert_pid"></a>
    </div>

    <!--前往更新-->
      <!-- <p >您的PID已过期</p>
    <a href="<?php echo url('User/set'); ?>"><input type="button" value="前往更新" class="alert_pid"></a>
-->
    <!--前往设置-->
     <!-- <p >请先设置淘宝账号并设置PID</p>
    <a href="/index/user/set.html"><input type="button" value="前往设置" class="alert_pid"></a>-->
</div>

    <footer>
        <div class="footer-content">
            <div class="fl waper">
                <img src="/static/index/images/logo.png" alt="">
                <ul>
                    <li>
                        <a href="/index//user/index.html" target="_blank">
                            个人中心
                        </a>
                    </li>
                    <li>
                        <a href="/index/collect/index.html" target="_blank">
                           我的收藏
                        </a>
                    </li>
                    <li>
                        <a href="/index/publish/index.html" target="_blank">
                            放单后台
                        </a>
                    </li>

                    <li>
                        <a href="#" target="_blank">
                            开放API
                        </a>
                    </li>
                    <li>
                        <a href="/index/user/set.html" target="_blank">
                            联盟设置
                        </a>
                    </li>
                    <li>
                        <a href="/index/news/zs_news.html" target="_blank">
                            联系我们
                        </a>
                    </li>
                </ul>
            </div>
            <div class="fl shu">
            </div>
            <div class="fl follow">
                <h1>
                    特价客的单子至少可以提升你3倍以上的收入
                </h1>
                <p class="f12">
                    特价客CopyRight 2017粤ICP备15077059号-4
                </p>
            </div>
            <div class="clear">
            </div>
        </div>
    </footer>
       <!--置顶-->
    <div id="scroll">
    </div>
    <!--收藏 商品数量 quick_links.js控制-->
    <div class="mui-mbar-tabs" id="sc">
        <div class="quick_links_panel">
            <div id="quick_links" class="quick_links">
                <li id="shopCart">
                    <a href="/index/collect/index.html" class="message_list">
                        <span class="cart_num">
                             <?php if($login_user): ?><?php echo $collect_count; else: ?>0
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            </div>
        </div>
    </div>
</body>

<div class="msg_body" onclick="msg_close()" style="height: 824px; display: none;"></div>
<div class="msg_div tanchu" style="height: 220px; top: 1650px; left: 559.5px; display: none;">
    <img src="__STATIC__/index/images/close.png" alt="" class="close" onclick="msg_close()">
    <h1 class='mmsg_title'>消息</h1>
    <p class="er f16 msg_content" style="overflow:auto;text-align:center;padding-left:10px;line-height:25px;text-indent:0px;margin-top:10px;padding-top:20px;height:80px;">
    </p>
    <p class="san" style="margin-top: 10px;"><input type="button" onclick="msg_close()" value="确定" class="fr msg_button"></p>
</div>

<script type="text/javascript" src="/static/index/js/jquery.1.4.2.min.js"></script>
<script type="text/javascript" src="/static/index/js/layer.js"></script>
<!--<script type="text/javascript" src="/static/index/js/imgLarzy.js"></script>-->
<script type="text/javascript" src="/static/index/js/clipboard.js"></script>
<script type="text/javascript" src="/static/index/js/common.js?v=1.0.2"></script>
<script type="text/javascript" src="/static/index/js/quick_links.js"></script>
<!--[if lte IE 8]><script src="js/ieBetter.js"> </script><![endif]-->
<script type="text/javascript" src="/static/index/js/parabola.js"></script>
<script type="text/javascript" src="/static/alert/alertify.custom.js"></script>

<script type="text/javascript">
    $(function(){
        var nav = $(".head-nav nav li");
        nav.click(function(){
            $(this).addClass("yuan").siblings().removeClass("yuan");
        });
        
        //复制文案显示
        $('.fz').mouseover(function(){
            $(this).parents('.good-price').siblings('.fm').css('display','block');
        });
        //复制文案隐藏
        $('.fz').mouseout(function(){
            $(this).parents('.good-price').siblings('.fm').css('display','none');
        });
        //隐藏登录注册
        $('.admin-ming,.admin-an').mouseover(function(){
            $('.admin-an').css('display', 'block');
            $('.admin-ming').css('border-bottom-right-radius', '0px');
             $('.admin-ming').css('border-bottom-left-radius', '0px');
        });

        $('.admin-ming,.admin-an').mouseout(function(){
            $('.admin-an').css('display', 'none');
             $('.admin-ming').css('border-bottom-right-radius', '7px');
             $('.admin-ming').css('border-bottom-left-radius', '7px');
        });
    });

    var clipboard = new Clipboard('.copy', {
        target: function(e) {
            var id  =   $(e).parents('li').attr('data_id');
            var elem=   typeof(id) == 'undefined' ? '.media-text-area' : '.copy'+id;
            return document.querySelector(elem);
        }
    });
    clipboard.on('success', function(e) {
        var obj     =   $(e).attr('trigger');
        $(obj).val('复制成功');
        //$('.copy').val('复制成功');
        
        /*console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);*/
        e.clearSelection();
    });

    //置顶
    // 获取置顶对象
    var obj = document.getElementById('scroll');
    

    // 置顶对象点击事件
    obj.onclick = function() {
        var timer = setInterval(function() {
            window.scrollBy(0, -50);
            if (document.body.scrollTop == 0) {
                clearInterval(timer);
            };
        },
        30);
    }

    // 窗口滚动检测
    window.onscroll = function() {
        obj.style.display = (document.body.scrollTop >= 300) ? "block": "none";
    }
    

    //图片懒加载
    /*var imgLoad = lazyload.init({
        anim: true,
        selectorName: ".lazy"
    });*/

    //添加收藏数量
    // 元素以及其他一些变量
    var eleFlyElement = document.querySelector("#flyItem"),
    eleShopCart = document.querySelector("#shopCart");

    var numberItem = parseInt($('.cart_num').html());

    // 抛物线运动
    var myParabola = funParabola(eleFlyElement, eleShopCart, {

        speed: 400,
        //抛物线速度
        curvature: 0.0008,
        //控制抛物线弧度
        complete: function() {
            eleFlyElement.style.visibility = "hidden";
            eleShopCart.querySelector("span").innerHTML = ++numberItem;

        }

    });

    // 绑定点击事件
    if (eleFlyElement && eleShopCart) {

        [].slice.call(document.getElementsByClassName("btnCart")).forEach(function(button) {

            button.addEventListener("click",
            function(event) {
                var goods_id = $(this).parents('li').attr('data_id');
                $.post('/index/index/addCollect?gid='+goods_id,function(msg){
                    if(msg.code == 101){
                        msg_show('请先登录再收藏!');
                        $('.msg_button').bind('click', function(){
                            window.location.href = msg.url;
                        });
                    }else if(msg.code == 1){
                        // 滚动大小
                        var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft || 0,
                        scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;
                        eleFlyElement.style.left = event.clientX + scrollLeft + "px";
                        eleFlyElement.style.top = event.clientY + scrollTop + "px";
                        eleFlyElement.style.visibility = "visible";
                        // 需要重定位
                        myParabola.position().move();    
                    }else{
                        msg_show(msg.msg);
                    }
                },"json");
                

            });

        });

    }
</script>
</html>
<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/detail.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/modernizr-custom-v2.7.1.min.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/videojs-ie8.min.js"></script>

<script type="text/javascript">
    //浮动左边导航
    $(window).scroll(function(){
        if($(this).scrollTop() > 280){
            $("#zuo").addClass('zuofu');
        }else{
            $("#zuo").removeClass('zuofu');
        }

        var top = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
        $(".hidden_pro_au").css({
            "top": (document.documentElement.clientHeight/2 + top - $(".hidden_pro_au").css("height").replace("px","")/2 )+"px",
            "left":($(window).width() - $(".hidden_pro_au").css("width").replace("px",""))/2 +"px"
        });

        $(".zhuanlian").css({
            "top": (document.documentElement.clientHeight/2 + top - $(".zhuanlian").css("height").replace("px","")/2 )+"px",
            "left":($(window).width() - $(".zhuanlian").css("width").replace("px",""))/2 +"px"
        });
    })

    // 焦点图片
    $('.choice-box li').bind({
                mouseenter: function () {
                    $('.choice-box li').removeClass('cur');
                    $(this).addClass('cur');
                    var bigImg = $(this).find('img').eq(0).attr('data-500');
                    $('.dadada img').attr('src', bigImg);
                },
            }
    );
    function show(){
        $(".lbOverlay").css({"height":window.screen.availHeight});
        $(".lbOverlay").show();

        var st=$(document).scrollTop(); //页面滑动高度
        var objH=$(".hidden_pro_au").height();//浮动对象的高度
        var ch=$(window).height();//屏幕的高度
        var objT=Number(st)+(Number(ch)-Number(objH))/2;   //思路  浮动高度+（（屏幕高度-对象高度））/2
        $(".hidden_pro_au").css("top",objT);

        var sl=$(document).scrollLeft(); //页面滑动左移宽度
        var objW=$(".hidden_pro_au").width();//浮动对象的宽度
        var cw=$(window).width();//屏幕的宽度
        var objL=Number(sl)+(Number(cw)-Number(objW))/2; //思路  左移浮动宽度+（（屏幕宽度-对象宽度））/2
        $(".hidden_pro_au").css("left",objL);
        $(".hidden_pro_au").slideDown("20000");//这里显示方式多种效果
    }

    function show2(){
        var gid = $('#gid').val();
        $.get("<?php echo url('Index/change_link'); ?>",{gid:gid},function(data){

            if (data.code == 200)
            {
                var rob = '';
                rob += data.data;
                $('#rob').html(rob);
                //alert(200);//转链数据显示
                return false;
            }
            else
            {
                var htmll = '';
                if (data.code == 101)
                {
                    htmll += '<p >你还没登录账号</p><a href="/user_login"><input type="button" name="" value="前往登录" class="alert_pid"></a>';
                }
                else if (data.code == 102)
                {
                    htmll += '<p >请先设置淘宝账号并设置PID</p> <a href="/index/user/set.html"><input type="button" value="前往设置" class="alert_pid"></a>';
                }
                else if (data.code == 103)
                {
                    htmll += '<p >您的PID已过期,请重新设置</p> <a href="<?php echo url('User/set'); ?>"><input type="button" value="前往设置" class="alert_pid"></a>';
                }
                else if (data.code == 104)
                {
                    htmll += '<p >您的PID有误</p> <a href="<?php echo url('User/set'); ?>"><input type="button" value="前往设置" class="alert_pid"></a>';
                }
                else
                {
                    htmll += '<p>数据有误</p>';
                }
                $('#change_pid').html(htmll);

                $(".lbOverlay").css({"height":window.screen.availHeight});
                $(".lbOverlay").show();

                var st=$(document).scrollTop(); //页面滑动高度
                var objH=$(".zhuanlian").height();//浮动对象的高度
                var ch=$(window).height();//屏幕的高度
                var objT=Number(st)+(Number(ch)-Number(objH))/2;   //思路  浮动高度+（（屏幕高度-对象高度））/2
                $(".zhuanlian").css("top",objT);

                var sl=$(document).scrollLeft(); //页面滑动左移宽度
                var objW=$(".zhuanlian").width();//浮动对象的宽度
                var cw=$(window).width();//屏幕的宽度
                var objL=Number(sl)+(Number(cw)-Number(objW))/2; //思路  左移浮动宽度+（（屏幕宽度-对象宽度））/2
                $(".zhuanlian").css("left",objL);
                $(".zhuanlian").slideDown("20000");//这里显示方式多种效果
                $('.zhuanlian').show();
            }


        });

    }
    function closeDiv(){
        $(".lbOverlay").hide();
        $(".hidden_pro_au").hide();
        $(".zhuanlian").hide();
    }


    //添加收藏数量
    // 元素以及其他一些变量
    var eleFlyElement = document.querySelector("#flyItem"),
            eleShopCart = document.querySelector("#shopCart");

    var cart_num = parseInt($('.cart_num').html());

    var numberItem = cart_num;

    // 抛物线运动
    var myParabola = funParabola(eleFlyElement, eleShopCart, {

        speed: 400,
        //抛物线速度
        curvature: 0.0008,
        //控制抛物线弧度
        complete: function() {

            eleFlyElement.style.visibility = "hidden";

            eleShopCart.querySelector("span").innerHTML = ++numberItem;

        }

    });

    // 绑定点击事件
    if (eleFlyElement && eleShopCart) {

        [].slice.call(document.getElementsByClassName("btnCart")).forEach(function(button) {

            button.addEventListener("click",
                    function(event) {
                        var gid = $('#gid').val();
                        $.get("<?php echo url('Index/addCollect'); ?>",{gid:gid}, function (data) {

                            if(data.code == 101){
                                window.location.href = data.url;return false;
                            }
                            if(data.code == 1){
                                // 滚动大小
                                var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft || 0,
                                        scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;

                                eleFlyElement.style.left = event.clientX + scrollLeft + "px";

                                eleFlyElement.style.top = event.clientY + scrollTop + "px";

                                eleFlyElement.style.visibility = "visible";

                                // 需要重定位
                                myParabola.position().move();

                                $('#collect_action').html("<p>已收藏</p>");return false;
                            }
                            alert(data.msg);
                        });



                    });

        });

    }

    function submit_error(){
        var gid = $('#gid').val();
        var error_reason = $('#error_reason').val();
        if(error_reason == ''){
            alert('请详细描述纠错原因');return false;
        }
        var type = $('#type').val();
        $.post("<?php echo url('Index/addErrorRecovery'); ?>",{gid:gid,error_reason:error_reason,type:type},function(data){
            if (data.code == 1)
            {
                alert(data.msg);
                window.location.href = '/goods?id='+gid;
                return false;
            }
            else
            {
                alert(data.msg); return false;
            }
        });
    }
</script>