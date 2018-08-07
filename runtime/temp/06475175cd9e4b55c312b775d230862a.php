<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\phpStudy\PHPTutorial\WWW\myapp\public/../application/user\view\center\index.html";i:1533529264;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>会员中心</title>
    <script type="text/javascript" src="/static/user/js/jquery.min.js"></script>
    <style type="text/css">
        body{background-color: #e1e1e1;margin: 0;padding: 0}
        .header{background-color: #fff;height: 50px;line-height: 50px}
        .center{
            width: 1200px;
            height: 800px;
            border:1px #ccc solid;
            margin: 30px auto;
            background-color: #fff;
        }
        .center-left{
            float:left;
            height: 100%;
            width:200px;
            text-align: center;
            line-height: 50px;
            border-right: 1px #ccc solid;
        }
        .center-right{
            height: 100%;
            width:800px;
            padding: 15px;
            margin-left: 220px;
        }
        .weixin-gzg-list{
            height: 255px;
            width:200px;
            border:1px #ccc solid;
            text-align: center;
            border-radius: 5px;
            float: left;
            margin-top: 10px;
            margin-right: 15px;
            margin-bottom: 15px;
            background-color: #0bb20c;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
        }
        .weixin-gzg-list .weixin-icon{
            margin: 20px 0px;
        }
        .weixin-gzg-list .weixin-name{
           
        }
        .authorized{
            border:2px dashed #ccc;
            height: 250px;
            width:200px;
            margin-top: 10px;
            text-align: center;
            font-size: 60px;
            color: #ccc;
            line-height: 240px;
            cursor: pointer;
            float: left;
            margin-right: 15px;
            margin-bottom: 15px;
        }
        .tipback{
          background-color:rgba(0, 0, 0, 0.5);
          width:100%;
          height: 100%;
          position: fixed;
          top:0;
          left:0;
          display: none
        }
        .tipbox{
          width: 529px;
          height: 620px;
          background-color: #ffffff;
          border-radius: 5px;
          position: fixed;
          top:50%;
          left: 50%;
          margin-top: -310px;
          margin-left: -320px;
          padding: 0px 20px;
        }
        .tipclose{
            position: absolute;
            top:0px;
            right: 0px;
            font-size: 20px;
            display: block;
            width: 30px;
            height: 30px;
            color:#666;
            text-align: center;
            cursor: pointer;
        }
        .default-input{
            margin-bottom: 20px
        }
        .default-input span{
            display: inline-block;
            width: 130px;
            font-size: 14px
        }
        .default-input input[type="text"]{
            width:380px;
            height: 23px;
            line-height: 23px;
            outline: none;
            padding-left: 5px;
            border:1px #ccc solid;
            border-radius: 2px;
        }
        .default-input .df-select{
            width: 385px;
            height: 32px;
            outline: none;
            border:1px #ccc solid;
            border-radius: 2px;
        }
        .default-input input[type="button"]{
            margin: 0px auto;
            display: block;
            border:1px #44b549 solid;
            background: #44b549;
            color: #fff;
            width: 100px;
            height: 35px;
            border-radius: 5px;
            outline: none;
            cursor: pointer;
        }
        .default-input t{
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="header"></div>
<div class="center">
    <div class="center-left">
        <div><a href="javascirpt:;" onclick="manager('account')">帐号管理</a></div>
        <div><a href="javascirpt:;" onclick="manager('info')">个人信息</a></div>
        <div><a href="javascirpt:;" onclick="manager('pass')">修改密码</a></div>
        <div><a href="javascirpt:;" onclick="manager('pass')">积分兑换</a></div>
    </div>

    <div class="center-right"></div>
</div>

<script type="text/javascript">
    $(function(){
       manager('account');
    });
    function manager($model){
        $.post("<?php echo url('user/center/manager'); ?>",{"model":$model},function(res){
            $(".center-right").html(res);
        });
    }
</script>

</body>
</html>