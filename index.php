<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


require dirname(__FILE__) . '/define.php';
require ROOT . 'etc/global.config.php';
require ROOT . 'etc/admin.config.php';
require ROOT . 'etc/security.config.php';
require ROOT . 'etc/files.config.php';
require ROOT . 'etc/cache.config.php';
require ROOT . 'etc/smiles.config.php';
require ROOT . 'etc/user.config.php';
require ROOT . 'etc/log.config.php';
require ROOT . 'lib/php_funcs.php';
require ROOT . 'lib/global.php';

if(isset($_COOKIE['theme']))
{
	if(file_exists(ROOT . 'usr/tpl/' . $_COOKIE['theme'] . '/index.tpl'))
		$config['tpl'] = filter($_COOKIE['theme']);
	else
		setcookie('theme', false, time(), '/');
		
}

if($config['timezone'] !== "")
{
	date_default_timezone_set($config['timezone']);
}

if($config['gzip'] && !DEBUG) 
{
	ob_start("ob_gzhandler");
}

function __autoload($class_name) 
{
	$class_path = ROOT.'boot/sub_classes/'.mb_strtolower($class_name).'.class.php';
	$class_path = str_replace("\\", "/", $class_path);
	if (file_exists($class_path)) 
	{
	require_once($class_path);
	}
}
$cache = new cache;
require ROOT . 'boot/db/' . $config['dbType'] . '.db.php';
require ROOT . 'boot/db' . (($config['dbCache'] == 1) ? '_cache' : '') . '.class.php';
require ROOT . 'boot/auth.class.php';
require ROOT . 'boot/template.class.php';
require ROOT . 'boot/core.class.php';
require ROOT . 'boot/loader.php';