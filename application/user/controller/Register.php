<?php
namespace app\user\controller;
use think\Db;
class Register extends Pub
{
    public function index()
	{
		return view();
	}
	public function forgot()
    {
        return view();
    }

	public function doReg()
    {
        $username       = input('username');
        $email          = input('email');
        $password       = input('password');
        $repassword     = input('repassword');
        $token          = input('token');
        $noncestr       = random(6);
        if(!request()->isAjax())                                          return ['errcode'=>0,'errmsg'=>'请求类型错误'];
        if(!$username || !$password || !$repassword || !$token)           return ['errcode'=>0,'errmsg'=>'缺少必要参数'];
        if($repassword != $password)                                      return ['errcode'=>0,'errmsg'=>'两次密码输入不一致'];
        if(!verifyToken($token))                                          return ['errcode'=>0,'errmsg'=>'token认证失败'];
        if(Db::name('user')->where('user_name',$username)->value('user_id')) return ['errcode'=>0,'errmsg'=>'用户已存在'];
        if(Db::name('user')->where('user_email',$email)->value('user_id')) return ['errcode'=>0,'errmsg'=>'Email已被注册'];

        $data = [
            'user_name' =>$username,
            'user_email' =>$email,
            'user_pass' =>encrypt_password($password,$noncestr),
            'noncestr'  =>$noncestr,
            'create_time' =>date('Y-m-d H:i:s')
        ];
        $uid = Db::name('user')->insertGetId($data);
        if($uid){
            $userData = ['uid'=>$uid,'user_name'=>$username];
            session('user_auth',$userData);
            session('user_auth_sign',data_signature($userData));
            return ['errcode'=>1,'errmsg'=>'注册用户成功'];
        } else {
            return ['errcode'=>0,'errmsg'=>'写入数据库失败'];
        }
    }

    public function doForgot()
    {
        $username = input('username');
        $email = input('email');
        $token = input('token');
        if(!request()->isAjax())                                          return ['errcode'=>0,'errmsg'=>'请求类型错误'];
        if(!$username || !$email || !$token)                              return ['errcode'=>0,'errmsg'=>'缺少必要参数'];
        if(!verifyToken($token))                                          return ['errcode'=>0,'errmsg'=>'token认证失败'];
        $user = Db::name('user')->where('user_name',$username)->field('user_id,user_email')->find();
        if(!$user['user_id'])                                             return ['errcode'=>0,'errmsg'=>'用户不存在'];
        if(!$user['user_email'])                                          return ['errcode'=>0,'errmsg'=>'该账号未设置Email，请联系客服 q'];
        if($email != $user['user_email'])                                 return ['errcode'=>0,'errmsg'=>'Email地址不匹配'];
        $noncestr = random(6);
        Db::name('user')->where('user_name',$username)->update([
            'user_pass' =>encrypt_password($noncestr,$noncestr),
            'noncestr'  =>$noncestr,
        ]);
        $content = '您好，这是来自wx1234.cn团队的一封邮件，您的账号：'.$username.'。新密码为：'.$noncestr.'。请登录 <a href="https://www.wx1234.cn">https://www.wx1234.cn</a> 站点及时修改您的密码！';
        $res = sendMail($email,$content);
        if($res){
            return ['errcode'=>1,'errmsg'=>'新密码已发送至您的邮箱，请注意查收'];
        }else {
            return ['errcode'=>0,'errmsg'=>'发送邮件失败，请联系客服'];
        }
    }

}