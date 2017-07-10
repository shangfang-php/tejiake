$(function(){
    $("#jm").click(function(){
        var inputtype = $("#login-pwd").attr('type');
        if(inputtype == "password"){
            $("#login-pwd").attr('type','text');
            $("#jm").attr('src','/static/index/images/zhenyan.png');
        }else{
            $("#login-pwd").attr('type','password');
            $("#jm").attr('src','/static/index/images/jm.png');
        }
    });
});

function submitInfo(){
    var phone   =   $('#login-name').val();
    var password=   $('#login-pwd').val();
    var isSaveUser= $('#saveUser').attr('checked');

    isSaveUser  =   isSaveUser == 'checked' ? 1 : 0;
    var data    =   {phone:phone, password:password, isSaveUser:isSaveUser};
    $.post('/index/login/login_info', data, function(msg){
        alert(msg.msg);
        if(msg.code == 200){
            window.location.href = '/index/user/index';
        }
    },"json");

}

