<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"D:\phpStudy\PHPTutorial\WWW\myapp\public/../application/user\view\center\account.html";i:1533605064;}*/ ?>
<div>
    <span><a href="javascript:;">微信公众号</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;
    <span><a href="javascript:;">微信小程序</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;
    <span><a href="javascript:;">支付宝生活号</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;
    <span><a href="javascript:;">支付宝小程序</a></span>
</div>

<?php foreach($weixin_gzh as $val): ?>
	<a href="<?php echo url('user/weixin/gzh',['id'=>$val['id'],'appid'=>$val['appid']]); ?>" class="weixin-gzg-list">
        <div class="weixin-icon"><img src="/images/toweixin.png"></div>
        <div><?php echo $val['name']; ?></div>
	</a>
<?php endforeach; ?>

<div class="authorized" onclick="create()"><span>+</span></div>

<div class="tipback">
    <div class=tipbox>
    	<div class="tipclose" onclick="hideback()">x</div>
		<form enctype="multipart/form-data" name="weixin">
			<h3>公众号开发信息</h3>
			<div class="default-input"><label><span>公众号名称</span><input type="text" name="name"></label></div>
			<div class="default-input">
				<label>
					<span>公众号类型</span><select name="type" class="df-select">
						<option value="1">订阅号</option>
						<option value="2">订阅号(已认证)</option>
						<option value="3">服务号</option>
						<option value="4">服务号(已认证)</option>
					</select>
				</label>
			</div>
			
			<div class="default-input"><label><span>开发者ID(AppID)</span><input type="text" name="appid"></label></div>
			<div class="default-input"><label><span>开发者密码(AppSecret)</span><input type="text" name="appsecret"></label></div>
			<div class="default-input"><label><span>公众号头像</span><input type="file" name="headpic"></label></div>
            <h3>服务器配置<small>（请将下列信息配置到微信管理后台）</small><a href="<?php echo url('user/weixin/setting'); ?>" target="_blank" style="font-size: 12px;float: right;font-weight: normal;line-height: 25px">教我配置？</a></h3>
			<div class="default-input"><label><span>服务器地址(URL)</span><t name="url"></t><input type="hidden" name="url"></label></div>
			<div class="default-input"><label><span>令牌(Token)</span><t name="token"></t><input type="hidden" name="token"></label></div>
			<div class="default-input"><label><span>消息加解密密钥(EncodingAESKey)</span><t name="key"></t><input type="hidden" name="key"></label></div>
			<div class="default-input"><label><span>消息加解密方式</span><select name="hello" class="df-select">
						<option value="1">明文模式</option>
						<option value="2">兼容模式</option>
						<option value="3">安全模式</option>
					</select></label></div>
			<div class="default-input"><input type="button" value="保存" onclick="save()"></div>
		</form>
    </div>
</div>
<script type="text/javascript">
function create(){
	$(".tipback").show();
	$.ajax({
        method:"POST",
        url:"<?php echo url('user/weixin/create'); ?>",
        dataType:"json",
        success:function(res){
            if(res.errcode == "1") {
                var url = res.data.url,
                    token = res.data.token,
                    key = res.data.key;
                $("t[name='url']").html(url);$("input[name='url']").val(url);
                $("t[name='token']").html(token);$("input[name='token']").val(token);
                $("t[name='key']").html(key);$("input[name='key']").val(key);
            }else{
                alert(res.errmsg);
            }
        }
	})
}
function save(){
    $.ajax({
        method:"POST",
        url:"<?php echo url('user/weixin/save'); ?>",
        data:new FormData($("form[name='weixin']")[0]),
        dataType:"json",
        processData:false,
        contentType:false,
        success:function(res){
            if(res.errcode == "1") {
                window.location.reload();
            }else {
                alert(res.errmsg);
            }
        }
    })
}
function hideback(){
	$(".tipback").hide();
}
</script>