<?php
namespace app\user\controller;
use think\Db;
class Weixin extends Pub
{
    //微信公众号管理首页
    public function gzh()
    {
        return view();
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
