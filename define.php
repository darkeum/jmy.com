<?php

/*
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


define('ACCESS', true);
define('TIMER', microtime(1));
define('ROOT', dirname(__FILE__) . '/');
define('PLUGINS', '/usr/plugins/');
define('COOKIE_AUTH', 'auth_jmy');
define('COOKIE_PAUSE', 'pause_jmy');
define('PAUSE_TIME', 120);
define('VERSION_ID', 'x.x.x');  
define('COOKIE_TIME', 2592000);
define('ADMIN', 'administration');
define('ADMIN_TPL', 'usr/tpl/admin/');
define('HACK_SQL', '/SELECT|INSERT|ALTER|DROP|UNION|OUTFILE|WHERE/i');
define('DEBUG', false);
define('INDEX', isset($_GET['url']) ? false : true);
@ini_set('allow_url_fopen', 1);
header('Content-type: text/html; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0'


mb_internal_encoding("UTF-8");
