<?php
namespace app\user\controller;

use think\Controller;

class Pub extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        $isLogin = $this->is_login();
        $this->assign('islogin',$isLogin);
        $this->assign('token',token());
        $ctrl = $this->request->controller();
        if(!$isLogin && $ctrl!=='Index' && $ctrl!=='Register' && $ctrl!=='Login') $this->redirect('index/index');
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