<?php
include("client/RiSMTP.inc.php");

/* Connect */

$api     = "http://example.com/server/"; // API 服务器地址
$apipass = "1234567890";                 // API 密码
$host    = "smtp.example.com";           // SMTP 服务器地址
$port    = 25;                           // SMTP 服务器端口
$auth    = true;                         // 是否启用身份验证
$user    = "noreply@example.com";        // SMTP 用户名
$pass    = "1234567890";                 // SMTP 密码
$Client  = new RiSMTPClient($api, $apipass, $host, $port, $auth, $user, $pass);

/* SendMail */

$to      = "rec@example.com";     // 收信人地址
$from    = "noreply@example.com"; // 发信人地址
$subject = "SendMail";         // 邮件主题
$body    = "RiSMTP 发信测试！"; // 邮件正文
$type    = "HTML";             // 邮件类型
$cc      = "";                 // 抄送
$bcc     = "";                 // 密送
$headers = "";                 // 附加请求头
$debug   = false;              // 是否开启调试模式
$result  = $Client->sendMail($to, $from, $subject, $body, $type, $cc, $bcc, $headers, $debug);

if(!empty($result)) {
    echo $result;
} else {
    echo "Successful!";
}