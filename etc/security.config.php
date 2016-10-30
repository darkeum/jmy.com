<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $security;
$security = array();
$security['xNums'] = "123456";
$security['stopNick'] = "%laed*";
$security['stopMails'] = "";
$security['stopWords'] = "хуй,пизда,шлюха";
$security['stopReplace'] = "*Цензор*";
$security['allowHTML'] = "b,i,s,u,font,color";
$security['banIp'] = "192.15.*.%
127.1.*.*
23.2332.2323.3";
$security['banIpMessage'] = "Ваш ip-адрес находится в черном списке!";
$security['switch_cp'] = "1";
$security['recaptcha'] = "0";
$security['recaptcha_public'] = "";
$security['recaptcha_private'] = "";
$security['captcha_width'] = "120";
$security['captcha_height'] = "60";
$security['captcha_lenght'] = "6";

