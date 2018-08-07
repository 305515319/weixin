<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\phpStudy\PHPTutorial\WWW\myapp\public/../application/user\view\login\index.html";i:1533089540;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>用户登录</title>
    <script type="text/javascript" src="/static/user/js/jquery.min.js"></script>
</head>
<body>
<form>
    <div>用户名：<input type="text" name="user_name"></div>
    <div>密码：<input type="password" name="user_pass"></div>
    <div><input type="button" value="登录" onclick="dologin()"> </div>
</form>
<script type="text/javascript">
    function dologin(){
        var user_name = $("input[name='user_name']").val(),
            user_pass = $("input[name='user_pass']").val();
        if(user_name == "" || user_pass =="") {
            alert("登录名或密码不能为空");
        }else{
            $.ajax({
                method:"POST",
                url:"<?php echo url('user/login/dologin'); ?>",
                data:{"user_name":user_name,"user_pass":user_pass},
                dataType:"json",
                success:function(res){
                    if(res.errcode == "1") {
                        window.location.href="<?php echo url('user/center/index'); ?>";
                    }else{
                        alert(res.errmsg);
                    }
                }
            })
        }
    }
</script>
</body>
</html>