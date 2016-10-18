<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Anton Goncharenko
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

$core->loadModLangADM('blog');

$module_array['blog'] = array(
		'name' => _AP_BLOG,
		'icon' => 'icon-pencil6',
		'desc' => _AP_BLOG_DESC,
		'subAct' => array(
			_AP_BLOG_LIST => '',
			_AP_BLOG_LIST_ARTICLES => 'articles',
			_AP_BLOG_CREATE => 'blog_add',
			_AP_BLOG_ADD => 'article_add',						
			_AP_CONF => 'config',
		)
);

$toconfig['blog'] = array
(
'name' => _AP_BLOG,
'link' => 'module/blog/config',
'param' => 'blog_conf'
);