<?php
namespace app\user\model;
use think\Cache;
use think\Model;

class Wxpay extends Model
{
    //独品公众号
    private $appid = 'wxe84dd8a107b623d4';
    private $secret = '37c42bdbead5a9c363212ffd3c4275f0';
    private $mchid = '1398410802';
    private $key = '5363e11f5bac793aa691a4d1882c49e8';
    private $sslcert_path = 'data/cert/dupin/apiclient_cert.pem';
    private $sslkey_path = 'data/cert/dupin/apiclient_key.pem';

    public function __construct($appid = '', $secret = '', $mchid = '', $key = '', $sslcert_path = '',$sslkey_path = '')
    {
        parent::__construct();
        if(!empty($appid))             $this->appid = $appid;
        if(!empty($secret))            $this->secret = $secret;
        if(!empty($mchid))             $this->mchid = $mchid;
        if(!empty($key))               $this->key = $key;
        if(!empty($sslcert_path))      $this->sslcert_path = $sslcert_path;
        if(!empty($sslkey_path))       $this->sslkey_path = $sslkey_path;
    }
    /*
     * 微信内H5调起支付
     * @params string $openid : 微信用户openid
     * @params string $out_trade_no : 商家生成的订单号（唯一性）
     * @params int $total_fee : 支付金额，单位分
     * @params string $attach : 附加数据，如：深圳分店
     * @params string $body : 商品描述，如：腾讯充值中心-QQ会员充值
     * return array $ret : 返回支付时所需要的数据
     * */
    public function payForWeixin($openid,$out_trade_no,$total_fee,$attach='魔盒CMS',$body='微信支付')
    {
        //支付数据
        $data['openid'] = $openid;
        $data['out_trade_no'] = $out_trade_no;
        $data['total_fee'] = $total_fee;
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];
        $data['attach'] = $attach;
        $data['body'] = $body;
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['trade_type'] = "JSAPI";
        $data['notify_url'] = "http://test.moh.cc/home/wxpaynofiy/notify.html";

        $sign = $this->getParam($data);
        $dataXML = "<xml>
           <appid>".$data['appid']."</appid>
           <attach>".$data['attach']."</attach>
           <body>".$data['body']."</body>
           <mch_id>".$data['mch_id']."</mch_id>
           <nonce_str>".$data['nonce_str']."</nonce_str>
           <notify_url>".$data['notify_url']."</notify_url>
           <openid>".$data['openid']."</openid>
           <out_trade_no>".$data['out_trade_no']."</out_trade_no>
           <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
           <total_fee>".$data['total_fee']."</total_fee>
           <trade_type>".$data['trade_type']."</trade_type>
           <sign>".$sign."</sign>
        </xml>";
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $ret =  $this->https_post($url,$dataXML);
        if($ret['return_code'] == 'SUCCESS' && $ret['return_msg'] == 'OK') {
            return array(
                'appId'     => $this->appid,
                'timeStamp' => time(),
                'nonceStr'  => $data['nonce_str'],
                'package'   => 'prepay_id='.$ret['prepay_id'],
                'signType'  => 'MD5',
                'paySign'   => $sign
            );
        } else {
            $this->errorLog("微信支付失败，appid:".$this->appid,$ret);
            return null;
        }
    }

    /*
     *   微信二维码支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $code_url : 二维码URL链接
     */
    public function payForQrcode($out_trade_no,$total_fee,$body="魔盒CMS",$attach="微信支付")
    {
        //支付数据
        $data['out_trade_no'] = $out_trade_no;
        $data['total_fee'] = $total_fee;
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];
        $data['attach'] = $attach;
        $data['body'] = $body;
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['trade_type'] = "NATIVE";
        $data['notify_url'] = "http://test.moh.cc/home/wxpaynofiy/notify.html";

        $sign = $this->getParam($data);
        $dataXML = "<xml>
           <appid>".$data['appid']."</appid>
           <attach>".$data['attach']."</attach>
           <body>".$data['body']."</body>
           <mch_id>".$data['mch_id']."</mch_id>
           <nonce_str>".$data['nonce_str']."</nonce_str>
           <notify_url>".$data['notify_url']."</notify_url>
           <out_trade_no>".$data['out_trade_no']."</out_trade_no>
           <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
           <total_fee>".$data['total_fee']."</total_fee>
           <trade_type>".$data['trade_type']."</trade_type>
           <sign>".$sign."</sign>
        </xml>";
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $ret =  $this->https_post($url,$dataXML);
        if($ret['return_code'] == 'SUCCESS' && $ret['return_msg'] == 'OK') {
            return $ret['code_url'];
        } else {
            $this->errorLog("获取微信支付二维码失败，appid:".$this->appid,$ret);
            return null;
        }
    }

    /*
     * 订单查询
     * @params string $transaction_id : 微信订单号
     * @params string $out_trade_no : 商家订单号（与微信订单号二选一）
     * */
    public function findOrder($out_trade_no)
    {
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['out_trade_no'] = $out_trade_no;
        $sign = $this->getParam($data);
        $dataXML = "<xml>
            <appid>".$data['appid']."</appid>
            <mch_id>".$data['mch_id']."</mch_id>
            <nonce_str>".$data['nonce_str']."</nonce_str>
            <out_trade_no>".$data['out_trade_no']."</out_trade_no>
            <sign>".$sign."</sign>
         </xml>";
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $ret =  $this->https_post($url,$dataXML);
        if($ret['return_code'] == 'SUCCESS' && $ret['return_msg'] == 'OK') {
            return $ret;
        } else {
            $this->errorLog("查询微信支付订单失败，appid:".$this->appid,$ret);
            return null;
        }
    }

    /*
    * 退款订单查询
    * @params string $transaction_id : 微信订单号
    * @params string $out_trade_no : 商家订单号（与微信订单号二选一）
    * */
    public function findRefundOrder($out_trade_no)
    {
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['out_trade_no'] = $out_trade_no;
        $sign = $this->getParam($data);
        $dataXML = "<xml>
            <appid>".$data['appid']."</appid>
            <mch_id>".$data['mch_id']."</mch_id>
            <nonce_str>".$data['nonce_str']."</nonce_str>
            <out_trade_no>".$data['out_trade_no']."</out_trade_no>
            <sign>".$sign."</sign>
         </xml>";
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $ret =  $this->https_post($url,$dataXML);
        if($ret['return_code'] == 'SUCCESS' && $ret['return_msg'] == 'OK') {
            return $ret;
        } else {
            $this->errorLog("查询微信支付退款订单失败，appid:".$this->appid,$ret);
            return $ret['err_code_des'];
        }
    }

    /*
     * 申请退款
     * @params string $out_trade_no : 商户订单号
     * @params string $out_refund_no : 商户退款单号
     * @params int $total_fee : 订单金额
     * @params int $refund_fee : 退款金额
     * @params string $refund_desc : 退款原因
     * */
    public function refund($out_trade_no,$out_refund_no,$total_fee,$refund_fee,$refund_desc='退款')
    {
        $data['appid']  = $this->appid;
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['out_trade_no'] = $out_trade_no;
        $data['out_refund_no'] = $out_refund_no;
        $data['total_fee'] = $total_fee;
        $data['refund_fee'] = $refund_fee;
        $data['refund_desc'] = $refund_desc;
        $data['notify_url'] = "http://test.moh.cc/home/wxpaynofiy/refund.html";
        $sign = $this->getParam($data);
        $dataXML = "<xml>
           <appid>".$data['appid']."</appid>
           <mch_id>".$data['mch_id']."</mch_id>
           <nonce_str>".$data['nonce_str']."</nonce_str>
           <out_trade_no>".$data['out_trade_no']."</out_trade_no>
           <out_refund_no>".$data['out_refund_no']."</out_refund_no>
           <total_fee>".$data['total_fee']."</total_fee>
           <refund_fee>".$data['refund_fee']."</refund_fee>
           <refund_desc>".$data['refund_desc']."</refund_desc>
           <notify_url>".$data['notify_url']."</notify_url>
           <sign>".$sign."</sign>
        </xml>";
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $ret =  $this->https_post($url,$dataXML,true);

        return $ret;

        if($ret['return_code'] == 'SUCCESS' && $ret['return_msg'] == 'OK') {
            if($ret['result_code'] == 'FAIL') {
                return $ret['err_code_des'];
            } else {
                return $ret['transaction_id'];
            }
        } else {
            $this->errorLog("微信退款失败，appid:".$this->appid,$ret);
            return isset($ret['err_code_des']) ? $ret['err_code_des'] : $ret['return_msg'];
        }
    }
    /*
     * 企业付款至用户零钱
     * @params string $openid : 用户openid
     * @params int $total_fee : 付款金额，单位分
     * @params string $out_trade_no : 商家订单号
     * @params string $username : 微信用户名称（注意微信昵称若为空时支付会出错）
     * @params string $desc : 付款描述
     * @params string $check_name : 是否检测用户名
     * */
    public function payForUser($openid,$total_fee,$out_trade_no,$username='魔盒CMS',$desc='魔盒CMS付款给用户',$check_name='NO_CHECK')
    {
        $data['amount'] = $total_fee;
        $data['check_name'] = $check_name;
        $data['desc'] = $desc;
        $data['mch_appid'] = $this->appid;
        $data['mchid'] = $this->mchid;
        $data['nonce_str'] = random(12);
        $data['openid'] = $openid;
        $data['partner_trade_no'] = $out_trade_no;
        $data['re_user_name'] = $username;
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];
        $sign = $this->getParam($data);

        $dataXML="<xml>
        <mch_appid>".$data['mch_appid']."</mch_appid>
        <mchid>".$data['mchid']."</mchid>
        <nonce_str>".$data['nonce_str']."</nonce_str>
        <partner_trade_no>".$data['partner_trade_no']."</partner_trade_no>
        <openid>".$data['openid']."</openid>
        <check_name>".$data['check_name']."</check_name>
        <re_user_name>".$data['re_user_name']."</re_user_name>
        <amount>".$data['amount']."</amount>
        <desc>".$data['desc']."</desc>
        <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
        <sign>".$sign."</sign>
        </xml>";

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $ret =  $this->https_post($url,$dataXML,true);
        return $ret;
        if($ret['return_code']=='SUCCESS' && $ret['result_code'] == 'SUCCESS')
        {
            //支付成功返回商户订单号、微信订单号、微信支付成功时间
            $result['partner_trade_no'] = $ret['partner_trade_no'];
            $result['payment_no'] = $ret['payment_no'];
            $result['payment_time'] = $ret['payment_time'];
            return $ret;
        } else {
            $this->errorLog('付款给用户失败，appid:'.$this->appid,$ret);
            return $ret['err_code_des'];
        }
    }
    /*
     * 普通红包
     * @params string $out_trade_no : 商家订单号
     * @params string $openid : 接收红包用户的openid
     * @params int $total_fee : 红包金额，单位分
     * @params int $total_num : 红包发放总人数
     * @params string $wishing : 红包祝福语
     * @params string $act_name : 活动名称
     * @params string $remark : 备注
     * @params string $scene_id ：场景值ID。发放红包使用场景，红包金额大于200或者小于1元时必传。PRODUCT_1:商品促销、PRODUCT_2:抽奖、PRODUCT_3:虚拟物品兑奖 、PRODUCT_4:企业内部福利、PRODUCT_5:渠道分润、PRODUCT_6:保险回馈、PRODUCT_7:彩票派奖、PRODUCT_8:税务刮奖
     * */
    public function redPack($openid,$total_fee,$out_trade_no,$total_num = 1,$wishing = '感谢您光临魔盒CMS平台进行购物',$act_name='魔盒CMS购物发红包',$remark = '购物领红包')
    {
        $data['mch_billno']     = $out_trade_no;
        $data['mch_id']         = $this->mchid;
        $data['wxappid']        = $this->appid;
        $data['send_name']      = '魔盒CMS';
        $data['re_openid']      = $openid;
        $data['total_amount']   = $total_fee;
        $data['total_num']      = $total_num;
        $data['wishing']        = $wishing;
        $data['client_ip']      = $_SERVER["REMOTE_ADDR"];
        $data['act_name']       = $act_name;
        $data['remark']         = $remark;
        $data['nonce_str']      = random(12);
        $sign = $this->getParam($data);

        $dataXML="<xml>
        <sign>".$sign."</sign>
        <mch_billno>".$data['mch_billno']."</mch_billno>
        <mch_id>".$data['mch_id']."</mch_id>
        <wxappid>".$data['wxappid']."</wxappid>
        <send_name>".$data['send_name']."</send_name>
        <re_openid>".$data['re_openid']."</re_openid>
        <total_amount>".$data['total_amount']."</total_amount>
        <total_num>".$data['total_num']."</total_num>
        <wishing>".$data['wishing']."</wishing>
        <client_ip>".$data['client_ip']."</client_ip>
        <act_name>".$data['act_name']."</act_name>
        <remark>".$data['remark']."</remark>
        <nonce_str>".$data['nonce_str']."</nonce_str>
        </xml>";

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $ret =  $this->https_post($url,$dataXML,true);
        return $ret;
        if($ret['return_code']=='SUCCESS' && $ret['result_code'] == 'SUCCESS')
        {
            return $ret;
        } else {
            $this->errorLog('发放普通红包失败,appid:'.$this->appid,$ret);
            return null;
        }

    }

    /*
    * 裂变红包：一次可以发放一组红包。首先领取的用户为种子用户，种子用户领取一组红包当中的一个，并可以通过社交分享将剩下的红包给其他用户。
     * 裂变红包充分利用了人际传播的优势。
    * @params string $out_trade_no : 商家订单号
    * @params string $openid : 接收红包用户的openid
    * @params int $total_fee : 红包金额，单位分
    * @params int $total_num : 红包发放总人数
    * @params string $wishing : 红包祝福语
    * @params string $act_name : 活动名称
    * @params string $remark : 备注
    * @params string $scene_id ：场景值ID。发放红包使用场景，红包金额大于200或者小于1元时必传。PRODUCT_1:商品促销、PRODUCT_2:抽奖、PRODUCT_3:虚拟物品兑奖 、PRODUCT_4:企业内部福利、PRODUCT_5:渠道分润、PRODUCT_6:保险回馈、PRODUCT_7:彩票派奖、PRODUCT_8:税务刮奖
    * */
    public function redPackGroup($openid,$total_fee,$out_trade_no,$total_num,$wishing = '感谢您光临魔盒CMS平台进行购物',$act_name='魔盒CMS购物发红包',$remark = '购物领红包')
    {
        $data['mch_billno']     = $out_trade_no;
        $data['mch_id']         = $this->mchid;
        $data['wxappid']        = $this->appid;
        $data['send_name']      = '魔盒CMS';
        $data['re_openid']      = $openid;
        $data['total_amount']   = $total_fee;
        $data['amt_type']       = 'ALL_RAND';   //ALL_RAND—全部随机,商户指定总金额和红包发放总人数，由微信支付随机计算出各红包金额
        $data['total_num']      = $total_num;
        $data['wishing']        = $wishing;
        $data['client_ip']      = $_SERVER["REMOTE_ADDR"];
        $data['act_name']       = $act_name;
        $data['remark']         = $remark;
        $data['nonce_str']      = random(12);
        $sign = $this->getParam($data);

        $dataXML="<xml>
        <sign>".$sign."</sign>
        <mch_billno>".$data['mch_billno']."</mch_billno>
        <mch_id>".$data['mch_id']."</mch_id>
        <wxappid>".$data['wxappid']."</wxappid>
        <send_name>".$data['send_name']."</send_name>
        <re_openid>".$data['re_openid']."</re_openid>
        <total_amount>".$data['total_amount']."</total_amount>
        <amt_type>".$data['amt_type']."</amt_type> 
        <total_num>".$data['total_num']."</total_num>
        <wishing>".$data['wishing']."</wishing>
        <client_ip>".$data['client_ip']."</client_ip>
        <act_name>".$data['act_name']."</act_name>
        <remark>".$data['remark']."</remark>
        <nonce_str>".$data['nonce_str']."</nonce_str>
        </xml>";

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
        $ret =  $this->https_post($url,$dataXML,true);
        if($ret['return_code']=='SUCCESS' && $ret['result_code'] == 'SUCCESS')
        {
            return $ret;
        } else {
            $this->errorLog('发放裂变红包失败,appid:'.$this->appid,$ret);
            return $ret['err_code_des'];
        }

    }
    /*
     * 查询红包记录
     * @params string $out_trade_no : 商家订单号
     * */
    public function findRedPack($out_trade_no)
    {
        $data['mch_billno']     = $out_trade_no;
        $data['mch_id']         = $this->mchid;
        $data['appid']          = $this->appid;
        $data['bill_type']      = 'MCHT';           //MCHT:通过商户订单号获取红包信息。
        $data['nonce_str']      = random(12);
        $sign = $this->getParam($data);

        $dataXML="<xml>
        <sign>".$sign."</sign>
        <mch_billno>".$data['mch_billno']."</mch_billno>
        <mch_id>".$data['mch_id']."</mch_id>
        <appid>".$data['appid']."</appid>
        <bill_type>".$data['bill_type']."</bill_type> 
        <nonce_str>".$data['nonce_str']."</nonce_str>
        </xml>";

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
        $ret =  $this->https_post($url,$dataXML,true);
        return $ret;
        if($ret['return_code']=='SUCCESS' && $ret['result_code'] == 'SUCCESS')
        {
            return $ret;
        } else {
            $this->errorLog('查询红包记录失败,appid:'.$this->appid,$ret);
            return $ret['err_code_des'];
        }
    }
    /*
    * 获取用户微信的OPENID
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
    /*
     * JSSDK签名数据包
     * */
    public function getSignPackage()
    {
        $redirect_url = (get_url());
        $nonceStr = random(12);
        $timeStamp = time();
        $string = "jsapi_ticket=".$this->get_jsapi_ticket()."&noncestr=".$nonceStr."&timestamp=".$timeStamp."&url=".$redirect_url;

        $signature = sha1($string);
//        return ['a'=>$string,'b'=>$signature];die();
        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timeStamp" => $timeStamp,
            "url"       => $redirect_url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }
    /*
     * 获取access_token
     * */
    private function get_access_token()
    {
        $access_token = Cache::get('access_token_'.$this->appid);
        if(!$access_token) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
            $res = json_decode(https_get($url));
            $access_token = $res->access_token;
            if ($access_token)  Cache::set('access_token_'.$this->appid,$access_token,$res->expire_time);
        }
        return $access_token;
    }
    /*
     * 获取jsapi_ticket
     * */
    private function get_jsapi_ticket()
    {
        $jsapi_ticket = Cache::get('jsapi_ticket_'.$this->appid);
        if(!$jsapi_ticket) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$this->get_access_token();
            $res = json_decode(https_get($url));
            $jsapi_ticket = $res->ticket;
            if($jsapi_ticket)   Cache::set('jsapi_ticket_'.$this->appid,$jsapi_ticket,$res->expire_time);
        }
        return $jsapi_ticket;
    }
    //对参数排序，生成SHA1加密签名
    private function signSha1(array $params)
    {
        $paramStr = '';
        ksort($params);
        $i = 0;
        foreach ($params as $key => $value)
        {
            if ($i == 0){
                $paramStr .= '';
            }else{
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . $value;
            ++$i;
        }
        $sign=sha1($paramStr);
        return $sign;
    }

    //对参数排序，生成MD5加密签名
    private function getParam($paramArray, $isencode=false)
    {
        $paramStr = '';
        ksort($paramArray);
        $i = 0;
        foreach ($paramArray as $key => $value)
        {
            if ($key == 'Signature'){
                continue;
            }
            if ($i == 0){
                $paramStr .= '';
            }else{
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . ($isencode?urlencode($value):$value);
            ++$i;
        }
        $stringSignTemp=$paramStr."&key=".$this->key;
        $sign=strtoupper(md5($stringSignTemp));
        return $sign;
    }
    //POST提交数据
    private function https_post($url,$data,$ssl = false)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        if($ssl) {
            curl_setopt ( $ch,CURLOPT_SSLCERT,ROOT_PATH.$this->sslcert_path);
            curl_setopt ( $ch,CURLOPT_SSLKEY,ROOT_PATH.$this->sslkey_path);
        }
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno: '.curl_error($ch);
        }
        curl_close($ch);
        return xmlToArray($result);
    }
    private function errorLog($msg,$ret)
    {
        file_put_contents(ROOT_PATH . 'runtime/error/wxpay.log', "[" . date('Y-m-d H:i:s') . "] ".$msg."," .json_encode($ret).PHP_EOL, FILE_APPEND);
    }
}