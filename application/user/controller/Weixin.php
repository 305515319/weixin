<?php
namespace app\user\controller;
use app\user\model\Weixinapi;
use think\Db;
class Weixin extends Pub
{
    private $appid;
    private $appsecret;
    private $token;
    private $key;
    public function _initialize()
    {
        parent::_initialize();
        $wx = Db::name('weixin_gzh')->where([
            'user_id'   =>session('user_auth.uid'),
            'appid'     =>input('appid')
        ])->cache()->find();
        if(!$wx) $this->error('公众号不存在');
        $this->assign('appid',$wx['appid']);
        $this->appid = $wx['appid'];
        $this->appsecret = $wx['appsecret'];
        $this->token = $wx['token'];
        $this->key = $wx['key'];
    }

    //微信公众号管理首页
    public function gzh()
    {

        echo $this->appid;
        return view();
    }

    /*
     * 微信网页授权
     * */
    public function authorized()
    {
        $wxuser = Weixinapi::openid($this->appid,$this->appsecret);
        p($wxuser);
    }
    public function create()
    {
        return ['errcode'=>1,'errmsg'=>'success','data'=>[
            'url'   => host_url(),
            'token' => random(32),
            'key'   => md5(random(32))
        ]];
    }
    public function save()
    {
        $uid        = session('user_auth.uid');
        $name       = input('name');
        $type       = input('type');
        $appid      = input('appid');
        $appsecret  = input('appsecret');
        $headpic    = $_FILES['headpic'];
        $url        = input('url');
        $token      = input('token');
        $key        = input('key');
        $hello      = input('hello');
        if(!request()->isAjax())            return ['errcode'=>0,'errmsg'=>'请求方式错误'];
        if(!$appid || !$appsecret ||!$name) return ['errcode'=>0,'errmsg'=>'name/appid/appsecret为必填参数'];
        $weixin = Db::name('weixin_gzh')->where(['user_id'=>$uid,'appid'=>$appid])->value('id');
        if($weixin)                         return ['errcode'=>0,'errmsg'=>'该公众号已被绑定'];
        !empty($headpic['name']) ?  $headpic = upload($headpic) : $headpic = '' ;
        $data = [
            'user_id'   =>$uid,
            'name'      =>$name,
            'type'      =>$type,
            'appid'     =>$appid,
            'appsecret' =>$appsecret,
            'headpic'   =>$headpic,
            'url'       =>$url,
            'token'     =>$token,
            'key'       =>$key,
            'hello'     =>$hello,
            'create_time'=>date('Y-m-d H:i:s')
        ];
        $ret = Db::name('weixin_gzh')->insert($data);
        if($ret) {
            return ['errcode'=>1,'errmsg'=>'success'];
        } else {
            return ['errcode'=>0,'errmsg'=>'写入数据库失败'];
        }
    }
}
