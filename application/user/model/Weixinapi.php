<?php
namespace app\user\model;
use think\Model;
class Weixinapi extends Model
{
    private $appid;
    private $appsecret;

    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid;
        $this->appsecret;
    }


    /*
     * 获取微信用户的信息
     * @params bool $c : true获取用户的完整信息，false获取用户的openid
     * return json $db ： 微信用户的信息
     * */
    public function openid($c=false)
    {
        if($_GET['state']!="zhangguoming"){
            $t = $c ? "snsapi_userinfo" : "snsapi_base";
            $url=urlencode(get_url());
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$url."&response_type=code&scope=".$t."&state=zhangguoming#wechat_redirect";
            echo "<html><script>window.location.href='$url';</script></html>";
            exit;
        }
        if($_GET['code']){
            $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code=".$_GET['code']."&grant_type=authorization_code";
            $wx_db=json_decode(https_get($url));
            if($c){
                $url_2="https://api.weixin.qq.com/sns/userinfo?access_token=".$wx_db->access_token."&openid=".$wx_db->openid."&lang=zh_CN";
                $db=json_decode(https_get($url_2));
                return $db;
            }else{
                return $wx_db->openid;
            }
        }
    }


}