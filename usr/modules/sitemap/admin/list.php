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
$core->loadModLangADM('sitemap');

global $lang;

$module_array['sitemap'] = array(
		'name' => $lang['sitemap'],
		'desc' => $lang['sitemap_desc'],
		'subAct' => array(
			$lang['sitemap'] => '',		
			$lang['sitemap_gen'] => 'create',
			$lang['sitemap_search'] => 'update',
			$lang['config'] => 'config'
		)
);

$toconfig['sitemap'] = array
(
	'name' => $lang['sitemap'],
	'link' => 'module/sitemap/config',
	'param' => 'sitemap_config'
);