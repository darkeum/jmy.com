<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}

 
global $sendmail_conf;
$sendmail_conf = array();
$sendmail_conf['count_send'] = "10";
$sendmail_conf['count_user'] = "10";
$sendmail_conf['message_num'] = "10";
$sendmail_conf['users_num'] = "10";
$sendmail_conf['name'] = "Официальный сайт поддержки JMY CMS";
$sendmail_conf['email'] = "4jmy@mail.ru";
$sendmail_conf['formats'] = "jpg,jpeg,png,gif,zip,rar";
$sendmail_conf['file_size'] = "10000000";