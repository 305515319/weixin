<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function p($data)
{
    echo '<pre>';
    print_r($data);
    die();
}
/**
 * 所有用到密码的不可逆加密方式
 * @param string $password
 * @param string $password_salt
 * @return string
 */
function encrypt_password($password, $password_salt)
{
    return md5(md5($password).md5($password_salt));
}
/**
 * 随机字符
 * @param int $length 长度
 * @param string $type 类型
 * @param int $convert 转换大小写 1大写 0小写
 * @return string
 */
function random($length=10, $type='letter', $convert=0)
{
    $config = array(
        'number'=>'1234567890',
        'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if(!isset($config[$type])) $type = 'letter';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) -1;
    for($i = 0; $i < $length; $i++){
        $code .= $string{mt_rand(0, $strlen)};
    }
    if(!empty($convert)){
        $code = ($convert > 0)? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/**
 * 数据签名
 * @param array $data 被认证的数据
 * @return string 签名
 */
function data_signature($data = [])
{
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data);
    $code = http_build_query($data);
    $sign = sha1($code);
    return $sign;
}

/**
 *取得当前页面完整URL
 *
 */
function get_url($type=false) {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    if(!$type)
        return  $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    else
        return  $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$php_self.$path_info;

}
function host_url(){
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    return  $sys_protocal.$_SERVER['HTTP_HOST'];

}
function https_get($url){
    $curl = curl_init();
    // $header[0]="Content-Type: text/html; Encoding=utf-8;";
    curl_setopt($curl, CURLOPT_URL, $url); //指定url值
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $header) ; //信息头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE 不直接打印 ，FALSE 直接打印
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //读取https证书是否有效,FALSE不读取
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); //验证证书,FALSE不验证
    curl_setopt($curl, CURLOPT_HEADER, FALSE) ; //取得信息头,FALSE不取
    curl_setopt($curl, CURLOPT_TIMEOUT,60);     //60s超时
    if (curl_errno($curl)) {
        return 'Errno'.curl_error($curl);//如果出错，输出错误信息
    }
    else{$result=curl_exec($curl);}//执行结果赋值给变量
    curl_close($curl);
    // return json_decode($result);//把结果转为JSON对象
    return $result;
}
function https_post($url,$data) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
/*
 * xml转array
 * @params xml $xml : XML数据
 * */
function xmlToArray($xml)
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring),true);
    return $val;
}
function upload($file)
{
    $filename = $file['name'];
    $filesize = $file['size'];
    $filetemp = $file['tmp_name'];
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));;
    $allowSzie= 2*1024*1024;
    $allowExt = ['jpg','jpeg','png','bmp'];
    $filepath = '/public/data/images/'.session('user_auth.uid').'/';
    $file     = random(30) . '.' . $filetype;
    if($filesize > $allowSzie)          return ['errcode'=>0,'errmsg'=>'只允许上传2M大小的图片'];
    if(!in_array($filetype,$allowExt))  return ['errcode'=>0,'errmsg'=>'只允许上传jpeg/jpg/bpm格式的图片'];
    if(!is_dir(ROOT_PATH.$filepath))  mkdir(ROOT_PATH.$filepath,0777);
    if(move_uploaded_file($filetemp,ROOT_PATH.$filepath.$file)) {
        return $filepath.$file;
    } else {
        exit(json_encode(['errcode'=>0,'errmsg'=>'上传图片到本地失败']));
    }

}