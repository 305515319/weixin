<?php
namespace app\user\controller;
use think\Db;
class Center extends Pub
{
    private $user_id;
    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = session('user_auth.uid');
        $action = $this->request->action();
        $this->assign('action',$action);
    }
    /*
     * 会员中心
     * */
    public function index()
    {
        return view();
    }
    /*
     * 微信公众号
     * */
    public function wx()
    {
        $weixin_gzh = Db::name('weixin_gzh')->where('user_id',$this->user_id)->select();
        $this->assign('weixin_gzh',$weixin_gzh);
        return view();
    }
    /*
     * 微信小程序
     * */
    public function wxapp()
    {
        return view();
    }
    /*
     * 支付宝生活号
     * */
    public function ali()
    {
        return view();
    }
    /*
     * 支付宝小程序
     * */
    public function aliapp()
    {

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
    /*
     * 修改密码
     * @params string $password : 原始密码
     * @params string $newpassword : 新密码
     * @params string $repassword :确认密码
     * */
    public function modifyPass()
    {
        $password = input('password');
        $newpassword = input('newpassword');
        $repassword  = input('repassword');
        $token    = input('token');
        $user_id = session('user_auth.uid');
        if(!request()->isAjax())        return ['errcode'=>0,'errmsg'=>'请求方式错误'];
        if(!$password || !$newpassword || !$repassword || !$token)
                                        return ['errcode'=>0,'errmsg'=>'缺少必要参数'];
        if($repassword != $newpassword) return ['errcode'=>'-1','errmsg'=>'两次密码不一致'];
        if(!verifyToken($token))        return ['errcode'=>0,'errmsg'=>'token认证失败'];
        $noncestr = Db::name('user')->where('user_id',$user_id)->value('noncestr');
        $pass = encrypt_password($password,$noncestr);
        $user = Db::name('user')->where(['user_id'=>$user_id,'user_pass'=>$pass])->value('user_id');
        if(!$user)                      return ['errcode'=>'-2','errmsg'=>'原始密码错误'];
        $noncestr = random(6);
        $newpass = encrypt_password($newpassword,$noncestr);
        $res = Db::name('user')->where('user_id',$user_id)->update(['user_pass'=>$newpass,'noncestr'=>$noncestr]);
        if($res){
            return ['errcode'=>1,'errmsg'=>'修改密码成功'];
        }else {
            return ['errcode'=>'-3','errmsg'=>'修改密码失败'];
        }
    }
}
