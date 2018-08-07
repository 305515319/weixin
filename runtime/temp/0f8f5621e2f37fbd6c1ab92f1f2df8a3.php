<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\phpStudy\PHPTutorial\WWW\myapp\public/../application/user\view\center\info.html";i:1533277597;}*/ ?>
<div>
    <form name="info" enctype="multipart/form-data">
        <div>头像：<input type="file" name="headpic"> </div>
        <div>昵称：<input type="text" name="nickname"> </div>
        <div>真实姓名：<input type="text" name="truename"> </div>
        <div>QQ：<input type="text" name="qq"> </div>
        <div>微信：<input type="text" name="weixin"> </div>
        <div>电话：<input type="text" name="phone"> </div>
        <div>邮箱：<input type="text" name="email"> </div>
        <div><input type="button" value="保存" onclick="info()"></div>
    </form>
</div>
<script type="text/javascript">
    function info(){
        var nickname = $("input[name='nickname']").val(),
            truename = $("input[name='truename']").val(),
            qq = $("input[name='qq']").val(),
            weixin = $("input[name='weixin']").val(),
            phone = $("input[name='phone']").val(),
            email = $("input[name='email']").val(),
            headpic = $("input[name='headpic']").val(),
            json = {
                "nickname":nickname,
                "truename":truename,
                "qq":qq,
                "weixin":weixin,
                "phone":phone,
                "email":email,
                "user_id":<?php echo $user_id; ?>,
            };
        $.ajax({
            method:"POST",
            url:"<?php echo url('user/center/'); ?>",
        })
    }
</script>