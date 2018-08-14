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
/*
 * 发送邮件
 * @params string $tomail : 收件人邮箱地址
 * @params string $title : 邮件标题
 * @parmas string $content : 邮件内容
 * */
function sendMail($tomail,$content,$title='来自Wx1234.cn站点的一封邮件')
{
    //实例化PHPMailer核心类
    $mail = new \PHPMailer();
    //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    $mail->SMTPDebug = 0;
    //使用smtp鉴权方式发送邮件
    $mail->isSMTP();
    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth=true;
    //链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.qq.com';
    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
    $mail->Port = 465;
    //设置smtp的helo消息头 这个可有可无 内容任意
   //$mail->Helo = 'Hello smtp.qq.com Server';
    //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
    $mail->Hostname = 'www.wx1234.cn';
    //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = 'UTF-8';
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = 'wx1234.cn团队';
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username ='wx1234cn@foxmail.com';
    //smtp登录的密码 使用生成的授权码 你的最新的授权码
    $mail->Password = 'wbnbukuqxygnbigh';
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = 'wx1234cn@foxmail.com';
    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(true);
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($tomail,$content);
    //添加多个收件人 则多次调用方法即可
    // $mail->addAddress('xxx@qq.com','lsgo在线通知');
    //添加该邮件的主题
    $mail->Subject = $title;
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $content;

    //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
    // $mail->addAttachment('./d.jpg','mm.jpg');
    //同样该方法可以多次调用 上传多个附件
    // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

    $status = $mail->send();

    //简单的判断与提示信息
    if($status) {
        return true;
    }else{
        return false;
    }
}

/*
 * 生成TOKEN
 * */
function token(){
    $code = encode('www.wx1234.cn','wx1234.cn',3600);
    return $code;
}
function verifyToken($token){
    $data = decode($token,'wx1234.cn');
    if($data != 'www.wx1234.cn'){
        return false;
    }else{
        return true;
    }
}
//加密函数，可用decode()函数解密，$data：待加密的字符串或数组；$key：密钥；$expire 过期时间
function encode($data,$key='',$expire = 0)
{
    $string=serialize($data);
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = substr(md5(microtime()), -$ckey_length);

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string =  sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    return $keyc.str_replace('=', '', base64_encode($result));
}
//encode之后的解密函数，$string待解密的字符串，$key，密钥
function decode($string,$key='')
{
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = substr($string, 0, $ckey_length);

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string =  base64_decode(substr($string, $ckey_length));
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
        return unserialize(substr($result, 26));
    }
    else
    {
        return '';
    }
}