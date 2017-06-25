<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


$module_array['feedsend'] = array(
		'name' => _ADMIN_FEEDSEND,
		'icon' => 'media/admin/pages.png',
		'desc' => _ADMIN_MOD_FEEDSEND_DESC,
		'subAct' => array(
			
		)
);
