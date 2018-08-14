<?php
namespace app\user\controller;
use think\Controller;
use think\Db;
class Login extends Controller
{
    /*
     * 用户登录
     * @params string $user_name : 登录名
     * @params string $user_pass : 登录密码
     * return json $ret : 成功或失败
     * */
    public function doLogin()
    {
        $user_name = input('user_name');
        $user_pass = input('user_pass');
        $token     = input('token');
        if(!request()->isAjax())                    return ['errcode'=>0,'errmsg'=>'请求方式错误'];
        if(!$user_name || !$user_pass || !$token)   return ['errcode'=>0,'errmsg'=>'缺少必要参数'];
        if(!verifyToken($token))                    return ['errcode'=>0,'errmsg'=>'token认证失败'];
        $user = Db::name('user')->where('user_name',$user_name)->find();
        if(!$user)                                  return ['errcode'=>0,'errmsg'=>'用户不存在'];
        $pass = encrypt_password($user_pass,$user['noncestr']);
        if($pass != $user['user_pass'])             return ['errcode'=>0,'errmsg'=>'密码错误'];
        $userData = ['uid'=>$user['user_id'],'user_name'=>$user['user_name']];
        session('user_auth',$userData);
        session('user_auth_sign',data_signature($userData));
        return ['errcode'=>1,'errmsg'=>'登录成功'];
    }

    public function logout()
    {
        session("user_auth",null);
        session("user_auth_sign",null);
        return redirect("index/index");
    }
}