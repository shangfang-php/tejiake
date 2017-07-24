var isLogin;

var gid = $('#comment').data('id');

//设置cookie
function setCookie(c_name, value) {
    var exdate = new Date();
    exdate.setTime(exdate.getTime() + 99999 * 24 * 60 * 60 * 1000);
    document.cookie = c_name + "=" + escape(value) + (";expires=" + exdate.toGMTString());
}

// 获取cookie
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) c_end = document.cookie.length;
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return ""
}
$(function () {


    $('.goods-info .descript-tag-list .dse-tag-item a').click(function(){
        var str = "商品详情 特色标签" + ($(this).parent().index()+1);
        umDataStatistics(str, "点击",'' ,'' , '');
    });
    $('.goods-info .price-num-status .goods-brand').click(function () {
        umDataStatistics("商品详情 品牌", "点击", '', '', '');
    });


    if ($(window).scrollTop() > $('.detail-wrap').offset().top) {
        $('.detail-content-l-main').addClass('fixed');
    } else {
        $('.detail-content-l-main').removeClass('fixed');
    }


//视频
//<![CDATA[
    var videoUrl = $('#jp_container_1').data('video'),
        videoPost = $('#jp_container_1').data('post');
    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
            if($(this).find('video')){
                $(this).find('video').attr('webkit-playsinline','webkit-playsinline');
            }
            $(this).jPlayer("setMedia", {
                m4v: videoUrl,
                m4a: videoUrl,
                poster: videoPost
            });
            $('#jquery_jplayer_1').click(function(){
                $('#jp_container_1 .jp-play').click();
            });
            $('.jp-play-btn').click(function(){
                $('#jp_container_1 .jp-play').click();
                if($(this).html()=="播放视频"){
                    $(this).html("暂停播放");
                }else{
                    $(this).html("播放视频");
                }
            });

        },
        swfPath: "./js",
        supplied: "webmv, ogv, m4v",
        useStateClassSkin: true,
        autoBlur: false,
        volume : 0.5,
        smoothPlayBar: true,
        keyEnabled: true,
        remainingDuration: true,
        toggleDuration: true,
        ended: function() {
            $('#jp_container_1 .jp-video-play').fadeIn();
            $('.jp-play-bar').attr('style','width:100%');
            $('#jp_container_1 .jp-play-bar').val('100%');
        },

        //播放器点击播放按钮
        play: function() {
            $('#jp_container_1 .jp-video-play').fadeOut();
            $(this).on('click', function() { $('#jp_container_1').jPlayer('pause');});
            $(this).jPlayer("pauseOthers");
        },
        //暂停回调函数
        pause: function() {
            $('#jp_container_1 .jp-video-play').fadeIn();
            $('#jp_container_1 .jp-video-play-icon').unbind('click');
        },
        //声音改变回调函数
        volumechange: function(event) {
            if(event.jPlayer.muted) {
                $('#jp_container_1 .jp-volume-controls').val(0);
            } else {
                $('#jp_container_1 .jp-volume-controls').val(event.jPlayer.volume);
            }
        }
    });
//]]>




  

    // 焦点图片
    $('.goods-small-img li').on(
        {
            mouseenter: function () {
                $('.goods-small-img li').removeClass('cur');
                $(this).addClass('cur');
                var bigImg = $(this).find('img').eq(0).data('500');
                $('.goods-big-img img').attr('src', bigImg);
            },
          
        }
    );





// 保存模板
    $('.modSetBtn').click(function () {
        setSelfmod(qqWx());
    });

    $('.mod-bot-btn .reset').click(function () {
        getSysmod(qqWx());
    });
    $('.mod-bot-btn .discard-changes').click(function () {
        layer.closeAll();
    });


    $('.mod-choice span').click(function () {
        var index = $(this).index();
        if (!$(this).hasClass('cur')) {
            $('.mod-high ul').addClass('hide');
            $('.mod-high ul').eq(index).removeClass('hide');
            $('.mod-main textarea').addClass('hide');
            $('.mod-main textarea').eq(index).removeClass('hide');
            $('.mod-choice span').removeClass('cur');
            $(this).addClass('cur');
        }
    });
})


var qqWx = function () {
    return $('.mod-choice .cur').index();
}
//判断是否支持一键复制 0 不支持 1 支持
        if(typeof Clipboard != "undefined"){
            ClipboardSupport = 1;
            var dseClip = new Clipboard('.jp-play-down', {
                text: function(trigger) {
                    return trigger.getAttribute('aria-label');
                }
            });
            dseClip.on('success', function(e) {
                // layer.msg('复制成功', {
                //         time: 2000
                //     }
                // );
                  $('.jp-play-down').val('复制成功');
                e.clearSelection();
            });
            dseClip.on('error', function (e) {
                // layer.msg('复制失败，请升级或更换浏览器后重新复制！', {
                //         time: 2000
                //     }
                // );
                e.clearSelection();
            });
        }
