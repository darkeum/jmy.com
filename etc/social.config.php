<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $social;
$social = array();
$social['switch'] = "0";
$social['pass'] = "1";
$social['admin'] = "0";
$social['vk_is'] = "1";
$social['vk_client_id'] = "";
$social['vk_client_secret'] = "";
$social['ok_is'] = "1";
$social['ok_client_id'] = "";
$social['ok_client_secret'] = "";
$social['ok_public_key'] = "";
$social['fb_is'] = "1";
$social['fb_client_id'] = "";
$social['fb_client_secret'] = "";
$social['gg_is'] = "1";
$social['gg_client_id'] = "";
$social['gg_client_secret'] = "";
$social['ya_is'] = "1";
$social['ya_client_id'] = "";
$social['ya_client_secret'] = "";
$social['mm_is'] = "1";
$social['mm_client_id'] = "";
$social['mm_client_secret'] = "";

