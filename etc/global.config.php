<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $config;
$config = array();
$config['url'] = "http://jmy.com";
$config['name'] = "JMY CMS";
$config['description'] = "мой первый сайт на JMY CMS";
$config['slogan'] = "Современная система управления сайтом!";
$config['keywords'] = "ключевые, слова, сайта";
$config['divider'] = " - ";
$config['charset'] = "utf-8";
$config['mainModule'] = "news";
$config['lang'] = "ru";
$config['uniqKey'] = "";
$config['timezone'] = "Europe/Kaliningrad";
$config['tpl'] = "JMY_white";
$config['tpl_change'] = "0";
$config['smartphone'] = "1";
$config['dbType'] = "mysqli";
$config['imageEffect'] = "shadowbox";
$config['support_mail'] = "";
$config['gzip'] = "1";
$config['off'] = "0";
$config['off_text'] = "Сайт закрыт.<br /> Ведутся профилактические работы.";
$config['cache'] = "1";
$config['dbCache'] = "0";
$config['mod_rewrite'] = "1";
$config['comments'] = "1";
$config['plugin'] = "0";

