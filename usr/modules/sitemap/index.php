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

global $lang;
loadConfig('sitemap'); 

if(!empty($sitemap_conf['keywords']))
{
	$core->tpl->keywords =$sitemap_conf['keywords'];
}
if(!empty($sitemap_conf['description']))
{
	$core->tpl->description = $sitemap_conf['description'];
}
set_title(array($lang['sitemap']));
$query = $db->query("SELECT * FROM ".DB_PREFIX."_sitemap ORDER BY id ASC");
if($db->numRows($query) > 0) 
	{	
		$maps_lnk ='';
		while($maps = $db->getRow($query)) 
		{
			$maps_lnk = $maps_lnk.'<a href="'.$maps['url'].'">'.$maps['name'].'</a><br />';
		}
		$core->tpl->loadFile('sitemap');
		$core->tpl->setVar('SITEMAP', $maps_lnk);		
		$core->tpl->end();
		
	} 
	else 
	{
		$core->tpl->info($lang['sitemap_empty']);
	}