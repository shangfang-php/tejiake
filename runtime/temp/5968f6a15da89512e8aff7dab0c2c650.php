<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:68:"D:\wamp\www\tejiake\public/../application/index\view\index\live.html";i:1501745318;s:71:"D:\wamp\www\tejiake\public/../application/index\view\public\header.html";i:1501763825;s:71:"D:\wamp\www\tejiake\public/../application/index\view\public\footer.html";i:1501745318;}*/ ?>
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
                                <img src="/static/index/images/shousuo.png" alt="" onclick="search()">
                            </span>
                        </div>
                    </div>
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
    <script type="text/javascript" src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
            
            <script>
                
                function search() {

                    var keywords = $("input[name='keywords']").val();
                    
                    if(keywords.length==0){

                        alert('请输入商品关键字或者商品ID');

                    } else {

                        window.location.href = 'http://www.baidu.com';
                    }

                }

            </script>
        </header>
<style>
    .con .good{margin-top: 64px;width: 1220px;}
    .con .good .good-list ul li{height: 580px;width: 380px;margin-right: 24px;}
    .con .good .good-list ul li .good-img{width: 380px;height: 380px;        overflow: hidden;}
    .con .good .good-list ul li .good-img img{
       
         transition:transform 0.4s;
    -moz-transition: transform 0.4s;
    -webkit-transition: transform 0.4s;
    -o-transition: transform 0.4s;
    }
    .con .good .good-list ul li .good-img img:hover{
         transform: scale(1.2);
    -moz-transform:scale(1.2);
    -webkit-transform:scale(1.12);
    -ms-transform:scale(1.2);
    -o-transform:scale(1.2);
    }
    .con .good .good-list ul li .good-collect{top: 362px;z-index: 99}
    .countdown{color: #6fc1fe;}
    .countdown div i{width: 16px;background: none;color: #6fc1fe}
    .countdown div.seperator{color: #6fc1fe;line-height: 32px;}
    .con .good .good-list ul li .good-title {
    margin: 10px 10px 0;
}
</style>

<!-- 加载每个页面内容-->
<div class="con">
    <div class="content">
        <div class="hot">
            <img src="__STATIC__/index/images/live.png" alt="爆款单" style="padding-top: 64px">
            <span class="f12">
                <img src="__STATIC__/index/images/tu.png" alt="">
                &nbsp;在线淘客数：<?php echo $online_num; ?>
            </span>
        </div>
        <div class="live-dec">
            <div class="live-box">
                <p>
                    直播做的好，单子跑不了，<br/>
                    每天直播预告多条产品介绍的好单子，<br/>
                    发单员会准时放出领券下单链接！
                </p>
            </div>
        </div>
        <div class="good">
            <div class="good-list" >
                <ul>
                    <?php foreach($goods_list as $val): ?>
                    <li data_id="<?php echo $val['id']; ?>">
                        <div class="good-img">
                            <a href="/item?id=<?php echo $val['id']; ?>" target="_blank"><img src="<?php echo $val['main_img']; ?>"></a>
                        </div>
                        <div class="good-collect">
                            <a  href="javascript:;" class="add_cart_large btnCart"><img src="/static/index/images/sc.png" alt=""></a>
                        </div>
                        <div class="good-title">
                            <a href="/item?id=<?php echo $val['id']; ?>" title="<?php echo $val['short_title']; ?>" target="_blank">
                                <i class="<?php if($val['is_tmall']): ?>icon-tianmao<?php else: ?>tk-icon-14<?php endif; ?>">
                                </i>
                                <?php echo $val['title']; ?>
                            </a>
                        </div>
                        
                        <div class="good-fit">
                           <div class="fl f12" style="width: 124px; height: 23px; line-height: 19px;">
                                券后
                                <span class="f16 price" style="color: #fe5c30;">
                                    ￥<?php echo sprintf('%0.2f',$val['real_money']); ?>
                                </span>
                            </div>
                            <div class="fl quan">
                               ￥<?php echo sprintf('%0.2f',$val['coupon_money']); ?>&nbsp;
                            </div>
                            <div class="charges f12 fr"  style="width: 110px;">
                                佣金
                                <span class="a3">
                                    ￥<?php echo sprintf('%0.2f',$val['taoke_money']); ?>
                                </span>
                                (<?php echo round($val['taoke_money_percent']); ?>%)
                            </div>
                        
                        </div>

                        <div class="good-lu">
                            <p>本单有<i class="lan"><?php echo $extends[$val['id']]['live_count']['1']; ?></i>段文字，<i class="lan"><?php echo $extends[$val['id']]['live_count']['1']; ?></i>张图片，<i class="lan"><?php echo $extends[$val['id']]['live_count']['2']; ?></i>段视频</p>
                        </div>

                        <div class="good-sales">
                            <!--<div class="skipper fl f12">
                                近2个小时跑单
                            </div>
                            <div class="lia fl f12">
                                <span>
                                    2
                                </span>
                                <span>
                                    2
                                </span>
                                <span>
                                    2
                                </span>
                            </div>-->
                            <div class="xiao fr f12">
                                销量&nbsp;<?php echo $val['sell_num']; ?>
                            </div>
                        </div>
                        <div class="good-line"></div>
                        <div class="good-price">
                            <?php if(!$extends[$val['id']]['diff_time']): ?>
                                <div class="f14" style="text-align: center;">
                                  <p>已开放链接</p>
                                </div>
                            <?php else: ?>
                                <div class="f14" style="text-align: center;">
                                    <p style="color: #6fc1fe;width: 130px;text-align: right" class="fl">距开抢还有:</p>
                                   <div class="lia fl f12" date='<?php echo $extends[$val['id']]['diff_time']; ?>'>
                                        <div class="countdown" >
                                            <div><i class="days">00</i></div>
                                            <div>天</div>
                                            <div><i class="hours">00</i></div>
                                            <div> 小时</div>
                                            <div class="seperator">:</div>
                                            <div><i class="minutes">00</i></div>
                                            <div> 分钟</div>
                                            <div class="seperator" >:</div>
                                            <div><i class="seconds">00</i></div>
                                            <div> 秒</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </li>
                    <?php endforeach; ?>
                    <div id="flyItem" class="fly_item">
                      <img src="/static/index/images/sc.png" width="40" height="40">
                    </div>
                    <div class="clear"></div>
                </ul>
            </div>
            <div class="page">
                <?php echo $goods_list->render(); ?>
            </div>
        </div>
    </div>
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
<script type="text/javascript" src="/static/index/js/jquery.downCount.js?v=1"></script>
<script type="text/javascript">
    $('.lia').each(function(){
        var day = $(this).attr('date');
        $(this).downCount({
            date: day,
            offset: +10
        }, function () {});
    });
</script>