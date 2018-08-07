<?php
namespace app\user\controller;
use think\Db;
class Center extends Pub
{
    public function index()
    {
        return view();
    }
    public function manager()
    {
        $user_id = session('user_auth.uid');
        $model = input('model');
        switch ($model){
            case 'info':
                $info = Db::name('userinfo')->where('user_id',$user_id)->find();
                $this->assign('info',$info);
                $this->assign('user_id',session('user_auth.uid'));
                break;

            default:
                $weixin_gzh = Db::name('weixin_gzh')->where('user_id',$user_id)->select();
                $this->assign('weixin_gzh',$weixin_gzh);
                break;
        }
        return view($model);
    }

    public function modifyInfo()
    {
        $headpic  = $_FILES['headpic'];
        $nickname = input('nickname');
        $truename = input('truename');
        $qq       = input('qq');
        $weixin   = input('weixin');
        $phone    = input('phone');
        $email    = input('email');
        $user_id  = session('user_auth.uid');
        if(!request()->isAjax())        return ['errcode'=>0,'errmsg'=>'请求方式错误'];
        $user = Db::name('user')->where('user_id',$user_id)->value('user_id');
        if(!$user)                      return ['errcode'=>0,'errmsg'=>'用户不存在'];

    }
}
