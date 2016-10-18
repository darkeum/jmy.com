<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}
$core->loadModLangADM('sitemap');

$module_array['sitemap'] = array(
		'name' => _SM_SITEMAP,
		'desc' => _SM_SITEMAP_DESC,
		'subAct' => array(
			_SM_SITEMAP => '',					
			_CONFIG => 'config',
		)
);

$topbar_array['sitemap'] = array(		
		'subAct' => array(
			_SM_SITEMAP_GEN => array('create','fa fa-refresh','ajax', "notif(\'primary\', \'"._AJAX_INFO."\', \'"._AJAX_COMPL."\');"),
			_SM_SITEMAP_UPDATE => 'update'
		)
);

$toconfig['sitemap'] = array
(
	'name' => _SM_SITEMAP,
	'link' => 'module/sitemap/config',
	'param' => 'sitemap_config'
);