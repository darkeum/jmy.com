<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


$config = array();
$config['description'] = "мой первый сайт на JMY CMS";
$config['slogan'] = "Современная система управления сайтом!";
$config['keywords'] = "ключевые, слова, сайта";
$config['divider'] = " - ";
$config['mainModule'] = "news";
$config['lang'] = "ru";
$config['timezone'] = "Europe/Kaliningrad";
$config['tpl'] = "JMY_white";
$config['tpl_change'] = "0";
$config['smartphone'] = "1";
$config['dbType'] = "mysql";
$config['imageEffect'] = "shadowbox";
$config['off'] = "0";
$config['off_text'] = "Сайт закрыт.<br /> Ведутся профилактические работы.";
$config['cache'] = "1";
$config['dbCache'] = "0";
$config['comments'] = "1";
$config['plugin'] = "0";
$config['hide'] = "1";
$config['name'] = "JMY CMS";
$config['charset'] = "utf-8";
$config['gzip'] = "1";
$config['mod_rewrite'] = "1";
$config['url'] = "http://jmy.com";
$config['uniqKey'] = "";
$config['support_mail'] = "";

