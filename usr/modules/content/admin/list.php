<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

$core->loadModLangADM('content');
global $lang;

$module_array['content'] = array(
		'name' => $lang['static'],
		'desc' => $lang['static_desc'],
		'subAct' => array(
			$lang['static_list'] => '',
			$lang['static_add'] => 'add',			
			$lang['config'] => 'config',
		)
);

$toconfig['content'] = array
(
		'name' => $lang['static'],
		'link' => 'module/content/config',
		'param' => 'content_conf'
);