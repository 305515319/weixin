<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"D:\phpStudy\PHPTutorial\WWW\myapp\public/../application/user\view\register\index.html";i:1533198429;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
    <script type="text/javascript" src="/static/user/js/jquery.min.js"></script>
</head>
<body>
<div>用户名：<input type="text" name="username" placeholder="手机号或邮箱"></div>
<div>密码：<input type="password" name="password"></div>
<div>确认密码：<input type="password" name="repassword"></div>
<div><input type="button" value="注册" onclick="doReg()"> </div>
<script type="text/javascript">
    function doReg(){
        var json = {
            "username":$("input[name='username']").val(),
            "password":$("input[name='password']").val(),
            "repassword":$("input[name='repassword']").val()
        };
        $.ajax({
            method:"POST",
            url:"<?php echo url('user/register/doreg'); ?>",
            data:json,
            dataType:"json",
            success:function (res) {
                if(res.errcode == "1") {
                    window.location.href="<?php echo url('user/login/index'); ?>";
                }else {
                    alert(res.errmsg);
                }

            }
        });
    }
</script>
</body>
</html>