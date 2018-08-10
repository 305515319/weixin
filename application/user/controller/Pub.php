<?php
namespace app\user\controller;

use think\Controller;

class Pub extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        $isLogin = $this->is_login();
        if(!$isLogin) $this->redirect('index/index');
        $this->assign('islogin',$isLogin);
    }
    /*
     * 检查用户是否登录
     * */
    protected function is_login()
    {
        $user_auth      = session('user_auth');
        $user_auth_sign = session('user_auth_sign');
        return $user_auth_sign == data_signature($user_auth) ? $user_auth['uid'] : 0;
    }
}