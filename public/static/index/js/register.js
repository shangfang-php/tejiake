$(function(){
	$('#drag').drag();
    //发送验证码
    $("#sendCode").click(function () {

    	//检测是否已滑动验证
    	if($('#handler_drag').val() != '1'){
    		return false;
    	}

    	//防止重复提交
    	var tim =  $("#sendCode").val();
        if(tim >1){
            alert('请稍后再重新发送');
            return false;
        }

    	var phone	=	$('#reg-name').val();
    	var patten	=	/^1[3578]\d{9}$/;
    	if(!patten.test(phone)){
    		alert('请输入正确手机号!');
    		return false;
    	}

    	$('#sendCode').removeClass('fasong');
        $.post("/index/login/send_code",{phone:phone});
        var num = 61;
        var timer = setInterval(function(){
            if(num == 1 ||num <1){
                $("#sendCode").val('重新发送');
                clearInterval(timer);
                $('#sendCode').addClass('fasong');
            }else{
                num--;
                $("#sendCode").val(num);
            }
        },1000);

    });

    $("#jm").click(function () {
        //toggle();
        var inputtype = $("#reg-pwd").attr('type');
        if(inputtype == "password"){
            $("#reg-pwd").attr('type','text');
            $("#jm").attr('src','/static/index/images/zhenyan.png');
        }else{
            $("#reg-pwd").attr('type','password');
            $("#jm").attr('src','/static/index/images/jm.png');
        }
    });
    $("#jm2").click(function () {
        //toggle();
        var inputtype = $("#reg-pwd2").attr('type');
        if(inputtype == "password"){
            $("#reg-pwd2").attr('type','text');
            $("#jm2").attr('src','/static/index/images/zhenyan.png');
        }else{
            $("#reg-pwd2").attr('type','password');
            $("#jm2").attr('src','/static/index/images/jm.png');
        }
    });

    $("#onsubmit").click(function(){
    	var isReceive	=	$('#isReceive').attr('checked');
    	if(isReceive != 'checked'){
    		alert('请先勾选接受协议');
    		return false;
    	}

        var handler_drag = $("#handler_drag").val();//1则为通过
        if(handler_drag != 1){
        	alert('请滑动验证码');
        	return false;
        }

        var code = $('#code').val();
        if(!code){
			alert('请输入手机验证码!');
			return false;
        }

        var password	=	$('#reg-pwd').val();
        var password1	=	$('#reg-pwd2').val();
        if(!password || !password1){
        	alert('请输入密码');
        }

        if(password != password1){
        	alert('两次密码不一致!');
        	return false;
        }
        var phone	=	$('#reg-name').val();

        var data	=	{code:code, password:password,password1:password1,phone:phone};
        $.post('/index/login/register_info', data, function(msg){
        	alert(msg.msg)
        	if(msg.code == 200){
        		window.location.href = '/';
        	}
        },"json");

    });
});