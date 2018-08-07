<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7 0007
 * Time: 14:02
 */
namespace app\user\model;
use think\Model;

class Weixinmessage extends Model
{
    /*
    * 微信消息管理
    * */
    public function message()
    {
        if (isset($_GET['echostr']) && $this->checkSignature()) {
            echo $_GET['echostr'];
        } else {
            $this->response(xmlToArray($this->decodeMsg()));
        }
    }
    /*
     * 响应消息
     * @params array $postArr : 公众号发送来的消息
     * */
    private function response(array $postArr)
    {
        switch ($postArr['MsgType'])
        {
            case 'event':
                //关注事件
                if ($postArr['Event'] == 'subscribe') {

                }
                //取关事件
                if ($postArr['Event'] == 'unsubscribe') {

                }
                //点击事件
                if ($postArr['Event'] == 'CLICK') {

                }
                //接收小程序审核结果
                if($postArr['Event'] == 'weapp_audit_success') {

                }
                break;
            case 'text':
                //文本消息
                break;
        }
    }
    /*
     * 验证消息来自微信服务器
     * */
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if (trim($tmpStr) == trim($signature)) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * 消息加密处理
     * @params xml $text
      */
    private function encodeMsg($text)
    {
        $encryptMsg = '';
        $errorCode = $this->wxobj->encryptMsg($text, time(), random(6), $encryptMsg);
        if ($errorCode == 0) {
            return $encryptMsg;
        } else {
            file_put_contents(ROOT_PATH . 'runtime/error/encodeMsg.log', "[" . date('Y-m-d H:i:s') . "] token :" . $this->token . "发送消息加密出错,errorCode:" . $errorCode . PHP_EOL, FILE_APPEND);
        }
    }

    /*
     * 消息解密处理
     * */
    private function decodeMsg()
    {
        $xml = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $msg = '';
        $errorCode = $this->wxobj->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $xml, $msg);
        if ($errorCode == 0) {
            return $msg;
        } else {
            file_put_contents(ROOT_PATH . 'runtime/error/decodeMsg.log', "[" . date('Y-m-d H:i:s') . "] token :" . $this->token . "接收消息解密出错,errorCode:" . $errorCode . PHP_EOL, FILE_APPEND);
        }
    }
}