# RiSMTP
远程发送邮件 API，防止网站源站 IP 通过邮件泄漏

这是一个可以远程发送邮件的 API，使用 PHP 编写。

现在大多数网站都使用了 CDN 来避免被 CC、DDoS 攻击，而无论 CDN 的抗攻击能力再强，一封简单的邮件就可能暴露网站的源站地址，导致攻击者可以直接绕过 CDN 连接到源站。

绕过的原理很简单，大多数 SMTP 服务器会在你发信的时候，会把你的服务器 IP 地址附加在邮件 Header 里，例如 QQ 企业邮箱会在 Header 里附加一个 `Received` 邮件头，其中就包含了你的网站源站 IP。

开发这个的目的是为了防止因为邮件导致网站源站 IP 泄漏，它使用网站服务器和发信服务器分离的方式，你只需要再多花点钱，或者直接用免费的虚拟主机空间放置 RiSMTP 的发信 API 即可。

### 发信请求原理
传统的 SMTP 发送邮件的方式是这样的：
````
网站服务器 > SMTP 服务器 > 对方邮件服务器
````
这样就会导致网站服务器的 IP 直接被 SMTP 服务器暴露了。

而 RiSMTP 的原理也非常简单：
````
网站服务器 > 邮件中转服务器 > SMTP 服务器 > 对方邮件服务器
````
显然，邮件中转服务器在发信过程中充当了一个挡箭牌的功能，攻击者即使通过邮件头查 IP，也只能查到中转服务器的 IP 地址，而无法查到源站地址。

RiSMTP 的工作原理是基于 PHP 的 curl，所以请确保你的网站 PHP 启用了 **php_curl** 模块。

### 使用方法
首先需要准备以下几样东西
1. 一个支持 PHP 的虚拟主机或者服务器，作为 API 服务器
2. 一个 SMTP 服务器和账号密码

第一步，将 `server/` 目录上传到虚拟主机或者服务器

第二步，编辑 `index.php`，修改第 `293` 行的 `$apipass = "1234567890";`，将 `1234567890` 改为你想设置的 API 密码。

第三步，将 `client/` 目录中的文件复制到你的网站任意目录

第四步，通过 include 将 RiSMTP 类引入你的代码，并创建一个连接
````php
include("plugins/RiSMTP.inc.php");
$api     = "http://example.com/server/"; // API 服务器地址
$apipass = "1234567890";                 // API 密码
$host    = "smtp.example.com";           // SMTP 服务器地址
$port    = 25;                           // SMTP 服务器端口
$auth    = true;                         // 是否启用身份验证
$user    = "noreply@example.com";        // SMTP 用户名
$pass    = "1234567890";                 // SMTP 密码
$Client  = new RiSMTPClient($api, $apipass, $host, $port, $auth, $user, $pass);
````
第五步，测试发送邮件
````php
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
````
### 关于邮件发送源码
这个是我好久之前在百度上找的一个 PHP 邮件发送类，对 PHP 7 的兼容性有点问题，拿来改了一下。

至于原作者，已经无法找到了，因为这个基本上已经烂大街了，根本无法找到初始来源。

### 开源协议
RiSMTP 使用通用公共许可证协议（GNU General Public License v3.0）协议开源
