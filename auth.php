<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
require dirname(__FILE__) . '/define.php';
session_start();
require ROOT . 'boot/sub_classes/socialauther/autoload.php';
require ROOT . 'etc/social.config.php';	
require ROOT . 'etc/global.config.php';	
require ROOT . 'usr/langs/'.$config['lang'].'.system.php';	
header("Content-Type: text/html; charset=utf-8");
$adapterConfigs = array(
    'vk' => array(
        'client_id'     => $social['vk_client_id'],
        'client_secret' => $social['vk_client_secret'],
        'redirect_uri'  => $config['url'].'/auth.php?provider=vk'
    ),
    'odnoklassniki' => array(
        'client_id'     => $social['ok_client_id'],
        'client_secret' => $social['ok_client_secret'],
        'redirect_uri'  => $config['url'].'/auth?provider=odnoklassniki',
        'public_key'    => $social['ok_public_key']
    ),
    'mailru' => array(
        'client_id'     => $social['mm_client_id'],
        'client_secret' => $social['mm_client_secret'],
        'redirect_uri'  => $config['url'].'/auth.php?provider=mailru'
    ),
    'yandex' => array(
        'client_id'     => $social['ya_client_id'],
        'client_secret' => $social['ya_client_secret'],
        'redirect_uri'  => $config['url'].'/auth.php?provider=yandex'
    ),
    'google' => array(
        'client_id'     => $social['gg_client_id'],
        'client_secret' => $social['gg_client_secret'],
        'redirect_uri'  => $config['url'].'/auth.php?provider=google'
    ),
    'facebook' => array(
        'client_id'     => $social['fb_client_id'],
        'client_secret' => $social['fb_client_secret'],
        'redirect_uri'  => $config['url'].'/auth.php?provider=facebook'
    )
);

function redicret($message, $url = 'news', $text = 'Спасибо') 
			{
				$full_url = $url;
				include(ROOT . 'usr/tpl/redirect.tpl');	
			}	
			
$adapters = array();
foreach ($adapterConfigs as $adapter => $settings) {
    $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
    $adapters[$adapter] = new $class($settings);
}
if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapters) && !isset($_SESSION['user_auth'])) 
{
	$auther = new SocialAuther\SocialAuther($adapters[$_GET['provider']]);
    if ($auther->authenticate()) 
	{      
            $values = array(
                $auther->getProvider(),
                $auther->getSocialId(),
                $auther->getName(),
                $auther->getEmail(),
                $auther->getSocialPage(),
                $auther->getSex(),
                date('Y-m-d', strtotime($auther->getBirthday())),
                $auther->getAvatar()
            );
			
			$user = new stdClass();
			$user->provider   = $auther->getProvider();
			$user->socialId   = $auther->getSocialId();
			$user->name       = $auther->getName();
			$user->email      = $auther->getEmail();
			$user->sex        = $auther->getSex();
			$user->birthday   = $auther->getBirthday(); 
			$user->avatar     = $auther->getAvatar();
			
			$_SESSION['user_auth'] = $user;
			
			redicret('Вы успешно вошли сейчас вас переместит обратно!', '/profile/social_auth');
			
    }
	else
	{
			redicret('Что-то пошло не так!!', '/index.php');
	}  
}
else if (isset($_GET['url']) && array_key_exists($_GET['url'], $adapters) && !isset($_SESSION['user_auth'])) 
{	
	$url=ucfirst($_GET['url']);	
	foreach ($adapters as $title => $adapter) 
	{
		if (ucfirst($title)==$url)
		{
			header('Location: '.$adapter->getAuthUrl());			
		}
    }
	
}
else
{
	if (isset($_SESSION['user_auth']))
	{
		header('Location: /profile/social_auth');
	}
	else
	{
		header('Location: /index.php');
	}   
}

?>