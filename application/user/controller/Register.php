<?php
namespace app\user\controller;
use think\Controller;
use think\Db;
class Register extends Controller
{
	public function index()
	{
		return view();
	}
	public function doReg()
    {
        $username       = input('username');
        $password       = input('password');
        $repassword     = input('repassword');
        $noncestr       = random(6);
        if(!request()->isAjax())                                          return ['errcode'=>0,'errmsg'=>'请求类型错误'];
        if(!$username || !$password || !$repassword)                      return ['errcode'=>0,'errmsg'=>'缺少必要参数'];
        if($repassword != $password)                                      return ['errcode'=>0,'errmsg'=>'两次密码输入不一致'];
        if(Db::name('user')->where('user_name',$username)->value('user_id')) return ['errcode'=>0,'errmsg'=>'用户已存在'];

        $data = [
            'user_name' =>$username,
            'user_pass' =>encrypt_password($password,$noncestr),
            'noncestr'  =>$noncestr,
            'create_time' =>date('Y-m-d H:i:s')
        ];
        $uid = Db::name('user')->insertGetId($data);
        if($uid){
            return ['errcode'=>1,'errmsg'=>'注册用户成功'];
        } else {
            return ['errcode'=>0,'errmsg'=>'写入数据库失败'];
        }
    }
}