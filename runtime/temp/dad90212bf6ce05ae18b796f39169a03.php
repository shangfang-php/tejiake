<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:75:"D:\wamp\www\tejiake\public/../application/index\view\user\user_collect.html";i:1501745318;s:71:"D:\wamp\www\tejiake\public/../application/index\view\public\header.html";i:1501814407;s:74:"D:\wamp\www\tejiake\public/../application/index\view\public\user_menu.html";i:1501745318;}*/ ?>
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
                             <input type="text" id="bb" name="keywords" value="" onblur="if(value=='') {value='请输入商品关键字或者商品ID'}" onFocus="if(value=='请输入商品关键字或者商品ID'){value=''}"#ED1C24">
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
                    
                    if( keywords === '请输入商品关键字或者商品ID' ){

                            alert(keywords);

                    } else {

                            window.location.href='http://demo.com/index/index/search.html?keywords='+keywords;    
                    }

                }

            </script>
        </header>
<link rel="stylesheet" href="__STATIC__/index/css/common.css">
<link type="text/css" href="__STATIC__/index/css/jquery-ui-1.8.16.custom.css" rel="stylesheet"  />
<!--   <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script> -->
<!--   <link rel="stylesheet" href="/css/font-awesome.min.css"> -->
<script  language="javascript" type="text/javascript" src="__STATIC__/index/js/jquery-1.4.2.min.js"></script>
<style type="text/css" media="screen">
    .magic-radio + label:before, .magic-checkbox + label:before {
        top: 6px;
        left: 14px;
        width: 16px;
        height: 16px;
    }
    .magic-radio + label:before, .checkall + label:before {
        top: 11px;
        left: -2px;
        width: 16px;
        height: 16px;

    }
    .checkall + label:after {
        top: 13px!important;
        left: 5px!important;


    }
    .magic-checkbox + label:after {
        top: 8px;
        left: 21px;


    }

    .magic-radio + label:before, .che-checkbox + label:before {
        top: 6px;
        left: -3px;
        width: 16px;
        height: 16px;
    }
    .che-checkbox + label:after {
        top: 8px;
        left: 4px;
    }
</style>
</head>
<!-- 加载每个页面内容-->
<div class="con">
    <div class="content" style="padding-top: 80px;">
        <style type="text/css">
li a{
    width:100%;
}
</style>
<div class="fl user-sideBar">
    <ul id="accordion" class="accordion">
        <li>
            <div class="link" style="    color: #1a99fb;">
              <a href="<?php echo url('user/index'); ?>">
                <i class="fa fa-paint-brush"><img src="__STATIC__/index/images/user-icon.png" alt=""></i>
                个人中心
                <i class="fa down"><img src="__STATIC__/index/images/more.png" alt=""></i>
              </a>
            </div>
        </li>
        <li onclick="showMenu(this)">
            <div class="link">
                <i class="fa fa-code"><img src="__STATIC__/index/images/deposit-icon.png" alt=""></i>
                招商放单
                <i class="fa fa-chevron-down"><img src="__STATIC__/index/images/down.png" alt=""></i>
            </div>
            <ul class="submenu" style="display: block;" >
                <li>
                    <a href="/index/publish/index">发布推广</a>
                </li>
                <li>
                    <a href="<?php echo url('Goods/index'); ?>">宝贝管理 </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="link">
                <a href="<?php echo url('Collect/index'); ?>">
                <i class="fa fa-mobile"><img src="__STATIC__/index/images/collect-icon.png" alt=""></i>
                我的收藏
                <i class="fa down"><img src="__STATIC__/index/images/more.png" alt=""></a></i>
            </div>
        </li>
        <li onclick="showMenu(this)">
            <div class="link">
                <i class="fa fa-globe"><img src="__STATIC__/index/images/setting-icon.png" alt=""></i>
                账户设置
                <i class="fa fa-chevron-down"><img src="__STATIC__/index/images/down.png" alt=""></i>
            </div>
            <ul class="submenu" style="display: block;">
                <li>
                    <a href="<?php echo url('user/basic'); ?>">
                       基本资料
                    </a>
                </li>
                <!-- <li>
                    <a href="/user/template.html">
                        自定义模板设置
                    </a>
                </li> -->
                <li>
                    <a href="/index/user/set.html">
                        阿里妈妈设置
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
<script type="text/javascript">
    function showMenu(obj){
        var className = $(obj).prop('class');
        className   =   className ? className : 'close';
        if(className == 'close'){
            var changeClass = 'open';
            var display = 'none';
            //$(obj).contents().find('.submenu').hide();
        }else{
            var changeClass = 'close';
            var display = 'block';
            //$(obj).contents().find('.submenu').show();
        }
        $(obj).prop('class', changeClass);
        $(obj).children('ul').css('display', display)
    }
</script>
        <div class="fr user-main">
            <div class="basic">
                <a href="<?php echo url('Collect/index'); ?>?action=collect" class="<?php if($action == "collect"): ?>thisclass<?php endif; ?> fl a1" style="width: 200px;"><img src="__STATIC__/index/images/qbsc-icon.png" alt="">&nbsp;全部收藏(<?php echo $collect_count; ?>)</a>
                <a href="<?php echo url('Collect/index'); ?>?action=spread" class="<?php if($action == "spread"): ?>thisclass<?php endif; ?> fl a2" style="width: 200px;"><img src="__STATIC__/index/images/tui-icon.png" alt="">&nbsp;推广列表(<?php echo $spread_count; ?>)</a>
            </div>
            <!--推广列表-->
            <div  class="tui-list" style="display: block;">
                <form action="#" method="get" id="typesubmit">
                    <ul class="duoxuan magic-type">
                        <!--1爆款单2限时抢购3过夜单4直播单5视频单-->
                        <li>
                            <input class="magic-checkbox" type="checkbox"  id="c6" <?php if(in_array(1,$type_arr) == true): ?>checked<?php endif; ?> value="1">
                            <label for="c6">爆款单</label>
                        </li>
                        <li>
                            <input class="magic-checkbox" type="checkbox" id="c7" <?php if(in_array(2,$type_arr) == true): ?>checked<?php endif; ?> value="2">
                            <label for="c7">  &nbsp;  &nbsp;限时抢购</label>
                        </li>
                        <li>
                            <input class="magic-checkbox" type="checkbox"  id="c8" value="4" <?php if(in_array(4,$type_arr) == true): ?>checked<?php endif; ?>>
                            <label for="c8">直播单</label>
                        </li>
                        <li>
                            <input class="magic-checkbox" type="checkbox"  id="c9" value="5" <?php if(in_array(5,$type_arr) == true): ?>checked<?php endif; ?>>
                            <label for="c9">视频单</label>
                        </li>
                        <li>
                            <input class="magic-checkbox" type="checkbox"  id="c10" value="3" <?php if(in_array(3,$type_arr) == true): ?>checked<?php endif; ?>>
                            <label for="c10">过夜单</label>
                        </li>
                        <li>
                            <input type="hidden" name="action" value="<?php echo $action; ?>">
                            <input type="hidden" name="type" value="">
                            <button type="button" style="color: #fff;background: #1a99fb;width: 74px;height: 26px;line-height: 28px; border-radius: 7px; font-size: 12px;border: none;" id="getType">确定提交</button>
                        </li>
                    </ul>
                </form>

                <div class="quanxuan">
                    <p class="fl f18">
                        <input class="magic-checkbox checkall" type="checkbox" name="layout" id="c12" onclick = "choise2(this.checked)">
                        <label for="c12">全选
                            <!--<span class="f14">共<i>20</i>件</span>-->
                        </label>
                    </p>
                    <p class="fr">
                        <input type="button" name="" value="清空" class="qingk" id="qingk">
                        <?php if($action == "collect"): ?>
                        <input type="button" name="" value="加入推广" class="add" onclick="show(0)">
                        <?php endif; ?>
                    </p>
                </div>
                <form  id="idsubmit">
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <div class="box-content">
                        <div class="hhhh">
                            <div class="fl che getgcid">
                                <!--  <input type="checkbox" name = "nobby" checked="checked"> -->

                                <input class="magic-checkbox che-checkbox" value="<?php echo $vo['id']; ?>" type="checkbox"  name="gcid[]"   id="check<?php echo $vo['id']; ?>" >
                                <label for="check<?php echo $vo['id']; ?>"> </label>


                            </div>
                            <div class="pic-img fl"><img src="__STATIC__/index/images/zhibopic.png" alt=""></div>
                            <div class="box-title fl">
                                <p class="f14"><img src="__STATIC__/index/images/tao.png" alt=""><?php echo $vo['short_title']; ?></p>
               <span class="ban f14"><img src="__STATIC__/index/images/tao.png" alt="" style="width:20px;height: 20px;">
                  <?php if($vo['type'] == 1): ?>
                   <img src="__STATIC__/index/images/BAO.png" alt="">
                        <?php elseif($vo['type'] == 2): ?>
                   <img src="__STATIC__/index/images/XIAN.png" alt="">
                        <?php elseif($vo['type'] == 3): ?>
                   <img src="__STATIC__/index/images/YE.png" alt="">
                        <?php elseif($vo['type'] == 4): ?>
                   <img src="__STATIC__/index/images/BO.png" alt="">
                        <?php elseif($vo['type'] == 5): ?>
                   <img src="__STATIC__/index/images/SHI.png" alt="">
                    <?php endif; ?>
               </span>
                            </div>
                            <div class="box-price fl">
                                <p>￥<?php echo $vo['real_money']; ?><br/><i class="back f12"><?php echo $vo['price']; ?></i></p>
                            </div>

                            <div class="box-quan fl">
                                <p class="quan f12">￥<?php echo $vo['coupon_money']; ?></p>
                                <p class="f12 yu">剩余 <i><?php echo $vo['coupon_total_num']-$vo['coupon_apply_num']; ?></i>／<?php echo $vo['coupon_total_num']; ?> </p>
                                <p class="f12 yu">到期 <i><?php echo date('Y-m-d',$vo['end_time']); ?></i></p>
                            </div>
                            <div class="box-price fl">
                                <p><span class="f14">佣金：</span><?php echo $vo['taoke_money']; ?>元<br/>
                                    <!--1定向计划2通用计划3鹊桥4营销计划-->
                                    <i class="back f12">
                                        计划
                                        <?php echo $vo['taoke_money_percent']; ?>%</i>
                                </p>
                            </div>
                            <div class=" box-btn fl">
                                <?php if($vo['is_spread'] == 0): ?>
                                <input type="button" name="" value="推广" class="more" onclick="show(<?php echo $vo['id']; ?>)">
                                <?php endif; ?>
                                <input type="button" value="删除" class="delete" onclick="delgc(<?php echo $vo['id']; ?>)" >

                            </div>
                            <div class="clear">

                            </div>
                        </div>
                        <p class="fl time f12 " style="margin-left: 20px;color: #1a99fb">
                            <?php if($vo['is_spread'] == 1): ?>
                            <img src="__STATIC__/index/images/tuiguangcg.png" alt="">&nbsp;推广成功</p>
                            <?php elseif($vo['is_spread'] == 2): ?>
                            <img src="__STATIC__/index/images/tuiguang-shibai.png" alt="">&nbsp;推广失败  原因：<?php echo $vo['remark']; endif; ?>
                        </p>
                        <?php if($vo['is_spread'] == 0): ?>
                        <p class="fr time f12">发布时间：<?php echo date("Y-m-d H:i:s",$vo['time']); ?></p>
                        <?php else: ?>
                        <p class="fr time f12">推广时间：<?php echo date("Y-m-d H:i:s",$vo['spread_time']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </form>
                <div class="page" style="margin-bottom: 30px;">
                    <?php echo $show; ?>
                </div>

                <div class="E-tui">
                    <h1>以下软件已经支持获取推广列表</h1>
                    <p style="    margin-top: 10px;"><span>E推软件</span></p>

                </div>
                <p class="f12 red" style="    line-height: 35px;    text-indent: 20px;">* 其他软件开发团队需要接入请联系我们！</p>
            </div>
        </div>
    </div>
    <div class="clear">   </div>
</div>
<div id="flyItem" class="fly_item">
    <img src="__STATIC__/index/images/sc.png" width="40" height="40">
</div>
<footer>
    <div class="footer-content">
        <div class="fl waper">
            <img src="__STATIC__/index/images/logo.png" alt="">
            <ul>
                <li>
                    <a href="/user/user.html" target="_blank">
                        个人中心
                    </a>
                </li>
                <li>
                    <a href="/user/collect.html" target="_blank">
                        我的收藏
                    </a>
                </li>
                <li>
                    <a href="/user/fbtg.html" target="_blank">
                        放单后台
                    </a>
                </li>

                <li>
                    <a href="/user/api.html" target="_blank">
                        开放API
                    </a>
                </li>
                <li>
                    <a href="/user/setting.html" target="_blank">
                        联盟设置
                    </a>
                </li>
                <li>
                    <a href="/index/news.html" target="_blank">
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
<!--弹出框-->
<div class="lbOverlay"  onclick="closeDiv()"></div>
<form action="" method="post">
    <div class="hidden_pro_au tanchu">
        <img src="__STATIC__/index/images/close.png" alt=""  class="close" onclick="closeDiv()">
        <h1>推广选择</h1>
        <p class="er f12">开始推送时间：<input type="text"  value="2017-07-06 12:25" id="time"></p>
        <input type="hidden" name="spread_gcid" value="">
        <p class="san"><input type="button" name="" value="确定" class="fr" id="gettime"></p>
    </div>
</form>
<!--收藏 商品数量 quick_links.js控制-->
<div class="mui-mbar-tabs" id="sc">
    <div class="quick_links_panel">
        <div id="quick_links" class="quick_links">
            <li id="shopCart">
                <a href="#" class="message_list">
                    <span class="cart_num">
                        0
                    </span>
                </a>
            </li>
        </div>
    </div>
</div>
</body>
<script  language="javascript" type="text/javascript" src="__STATIC__/index/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/common.js"></script>
<script type="text/javascript" src="__STATIC__/index/js/quick_links.js"></script>
<!--[if lte IE 8]><script src="__STATIC__/index/js/ieBetter.js"> </script><![endif]-->
<script type="text/javascript" src="__STATIC__/index/js/parabola.js"></script>

<script  language="javascript" type="text/javascript" src="__STATIC__/index/js/jquery-ui-1.8.16.custom.min.js" charset='utf-8'></script>
<script language="javascript" type="text/javascript" src="__STATIC__/index/js/jquery-ui-timepicker-addon.js" charset='utf-8'></script>
<script language="javascript" type="text/javascript" src="__STATIC__/index/js/jquery.ui.datepicker-zh-CN.js" charset='utf-8'></script>
<script type="text/javascript">
    //时间选择器
    $(function(){
        $("#time").datetimepicker({
            changeYear : true,
            changeMonth : true,
            showSecond : true,
            timeFormat : 'hh:mm:ss',
            dateFormat : 'yy-mm-dd',
            stepHour : 1,
            stepMinute : 1,
            stepSecond : 1
        });
    });
</script>

<script type="text/javascript">
    //置顶
    // 获取置顶对象
    var obj = document.getElementById('scroll');
    //获取类型
    $(function () {
        $('#getType').click(function(){
            //alert(window.location.href);return false;
            //alert(this.value);
            var type = $('.magic-type input[type=checkbox]:checked');
            //var type = $('.magic-type input[name=layout[]]:checked');
            var type_val = new Array();
            for(var i=0;i<type.length;i++){
                type_val[i] = type[i].value;
            }
            if(type_val == null || type_val == ''){
                alert('选择要查看的类型');return false;
                //给爆款单添加checked
            }
            var typeval = type_val.join(",");
            $('input[name=type]').val(typeval);
            $('#typesubmit').submit();
        });

        $('#qingk').click(function () {
            var gcids = $('.getgcid input[type=checkbox]:checked');
            var gcids_val = new Array();
            for(var i=0;i<gcids.length;i++){
                gcids_val[i] = gcids[i].value;
            }
            if(gcids_val == null || gcids_val == ''){
                alert('选择要删除的收藏数据');return false;
                //给爆款单添加checked
            }
            if(confirm('确定要删除当前当前数据？')){
                $.post("<?php echo url('Collect/clear'); ?>",{gcids_arr:gcids_val},function(dat){
                    var data = JSON.parse(dat);
                    alert(data.msg);
                    if(data.code == 1){
                        window.location.href = window.location.href;
                    }
                    return false;
                });
            }
        });

        $('#gettime').click(function () {
            var time = $('#time').val();
            var timestamp2 = Date.parse(new Date(time));
            timestamp2 = timestamp2 / 1000;  //时间戳
            //alert(timestamp2);
            var spreaf_gcid = $('input[name=spread_gcid]').val();
            $.post("<?php echo url('Collect/addSpread'); ?>",{spread_time:timestamp2,gcid:spreaf_gcid},function (data) {
                alert(data.msg);
                if(data.code == 1){
                    window.location.href = "<?php echo url('Collect/index'); ?>?action=spread";
                }
                return false;
            });
        });

    });

    function delgc(param){
        if(confirm('确定要删除当前当前数据？')){
            $.get("<?php echo url('Collect/clear'); ?>",{gcid:param},function(dat){
                var data = JSON.parse(dat);
                alert(data.msg);
                if(data.code == 1){
                    window.location.href = window.location.href;
                }
                return false;
            });
        }
    }


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
    var imgLoad = lazyload.init({
        anim: true,
        selectorName: ".lazy"
    });

    //收藏全选
    function choise(value)
    {
        var checkBoxs = document.getElementsByName("hobby");
        for (var i = 0;i < checkBoxs.length ;i++ )
        {
            if(checkBoxs[i].type == "checkbox")
            {
                checkBoxs[i].checked = value;
                $('.collect-box .hhhh').css('background',' #fafafa');
            }

        }
    }
    //列表全选
    function choise2(value)
    {
        var checkBoxs = document.getElementsByName("gcid[]");
        for (var i = 0;i < checkBoxs.length ;i++ )
        {
            if(checkBoxs[i].type == "checkbox")
            {
                checkBoxs[i].checked = value;
                $('.tui-list .hhhh').css('background',' #fafafa');
            }


        }
    }


</script>

<script>

    function show(param){
        if(param == 0){
            //多个选择 获取多个gcid的值
            var gcids = $('.getgcid input[type=checkbox]:checked');
            var gcids_val = new Array();
            for(var i=0;i<gcids.length;i++){
                gcids_val[i] = gcids[i].value;
            }
            if(gcids_val == null || gcids_val == ''){
                alert('选择要推广的数据');return false;
            }
            $('input[name=spread_gcid]').val(gcids_val);
        }else{
            //单个选择
            $('input[name=spread_gcid]').val(param);
        }
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

    function closeDiv(){
        $(".lbOverlay").hide();
        $(".hidden_pro_au").hide();
    }

    $(document).ready(function(){
        $(window).scroll(function(){
            var top = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
            $(".hidden_pro_au").css({
                "top": (document.documentElement.clientHeight/2 + top - $(".hidden_pro_au").css("height").replace("px","")/2 )+"px",
                "left":($(window).width() - $(".hidden_pro_au").css("width").replace("px",""))/2 +"px"
            });
        })

    })
</script>
</html>