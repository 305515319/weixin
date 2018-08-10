<?php
namespace app\user\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        $user_auth      = session('user_auth');
        $user_auth_sign = session('user_auth_sign');
        $isLogin = $user_auth_sign == data_signature($user_auth) ? $user_auth['uid'] : 0;
        $this->assign('islogin',$isLogin);
        return view();
    }
}
