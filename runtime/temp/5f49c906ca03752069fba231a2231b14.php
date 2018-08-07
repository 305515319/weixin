<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"D:\phpStudy\WWW\myapp\public/../application/user\view\register\index.html";i:1532686233;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
    <script type="text/javascript" src="/static/user/js/jquery.min.js"></script>
</head>
<body>
<div>用户名：<input type="text" name="username"></div>
<div>手机号：<input type="text" name="phone"></div>
<div>邮箱：<input type="text" name="email"></div>
<div>密码：<input type="password" name="password"></div>
<div>确认密码：<input type="password" name="repassword"></div>
<div><input type="button" value="注册" onclick="doReg()"> </div>
<script type="text/javascript">
    function doReg(){
        var json = {
            "username":$("input[name='username']").val(),
            "phone":$("input[name='phone']").val(),
            "email":$("input[name='email']").val(),
            "password":$("input[name='password']").val(),
            "repassword":$("input[name='repassword']").val()
        };
        $.ajax({
            method:"POST",
            url:"<?php echo url('user/register/doreg'); ?>",
            data:json,
            dataType:"json",
            success:function (res) {
                alert(res.errmsg);
            }
        });
    }
</script>
</body>
</html>