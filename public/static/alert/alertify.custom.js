$(function(){
    $(window).scroll(function(){
        var top = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
        $(".msg_div").css({
            "top": (document.documentElement.clientHeight/2 + top - $(".msg_div").css("height").replace("px","")/2 )+"px",
            "left":($(window).width() - $(".msg_div").css("width").replace("px",""))/2 +"px"
        });
    })
})

function msg_show(content){
    $('.msg_content').html(content);

    $(".msg_body").css({"height":window.screen.availHeight});
    $(".msg_body").show();

    var st=$(document).scrollTop(); //页面滑动高度
    var objH=$(".msg_div").height();//浮动对象的高度
    var ch=$(window).height();//屏幕的高度  
    var objT=Number(st)+(Number(ch)-Number(objH))/2;   //思路  浮动高度+（（屏幕高度-对象高度））/2
    $(".msg_div").css("top",objT);
     
    var sl=$(document).scrollLeft(); //页面滑动左移宽度
    var objW=$(".msg_div").width();//浮动对象的宽度
    var cw=$(window).width();//屏幕的宽度  
    var objL=Number(sl)+(Number(cw)-Number(objW))/2; //思路  左移浮动宽度+（（屏幕宽度-对象宽度））/2
    $(".msg_div").css("left",objL);
    $(".msg_div").slideDown("20000");//这里显示方式多种效果
}

function msg_close(){
    $(".msg_body").hide();
    $(".msg_div").hide();
}