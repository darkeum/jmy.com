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
require (ROOT.'etc/content.config.php');

function view($name)
{
global $db, $config, $core, $url, $headTag, $content_conf, $lang;
	if($name)
	{
		$name = str_replace(array('.html', '.htm'), '', $name);
		
		$query = $db->query("SELECT c.*, l.* FROM " . DB_PREFIX . "_content as c LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=c.id and l.module='content') WHERE translate='" . $db->safesql($name) . "'  AND l.lang = '" . $core->InitLang() . "'");
		$static = $db->getRow($query);

		if($static)
		{
			$core->tpl->uniqTag[] = 'view';
			$core->tpl->uniqTag[] = 'view-'.$static['id'];
			$core->tpl->uniqTag[] = $static['translate'];
			
			
			if(!empty($static['keywords']))
			{
				$core->tpl->keywords = $static['keywords'];
			}
			if(!empty($static['description']))
			{
				$core->tpl->description = $static['description'];
			}
		
			$ptitle = $static['title'];
			if(!empty($static['fulltitle']))
			{
				$ptitle =$static['fulltitle'];
			}
			set_title(array($ptitle));
			
			if(!empty($static['theme']))
			{
				$theme = $static['theme'].'/';
			}
			else
			{
				$theme = '';
			}
			$cat = $static['cat'] !== ',0,' ? $core->getCat('content', $static['cat'], 'short', 3) : '';
			$link = $static['cat'] !== ',0,' ? 'content/' . $core->getCat('content', $static['cat'], 'development') . '/' : 'content/';
			$core->tpl->loadFile('content/'.$theme.'content-view');
			$core->tpl->setVar('TITLE', $static['title']);
			$core->tpl->setVar('TEXT', $core->bbDecode($static['short'], $static['id'], true));
			$core->tpl->setVar('TRANSLATE', $static['translate']);
			$core->tpl->setVar('KEYWORDS', $static['keywords']);
			$core->tpl->setVar('DATE', $static['date']);
			$core->tpl->setVar('PREVIEW', $static['preview']);
			$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
			$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $static['preview']);
			$core->tpl->sources = preg_replace("#\\[img:([0-9]*?)\\]#is", (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''), $core->tpl->sources);
			$core->tpl->setVar('CATEGORY', $cat);
			$core->tpl->setVar('ALTNAME', $static['translate']);
			$core->tpl->end();
			
			if($content_conf['allowComm'] == 1)
			{
				show_comments('content', $static['id'], $content_conf['comments_num']);
			}
		}
		else
		{
				$core->tpl->info($lang['static_notfound']);
		}
	}
	else
	{
		include(ROOT . 'usr/tpl/404.tpl');
	}
}
 
global $lang;
switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		if(eregStrt('.htm', mjsEnd($url)))
		{
			view(mjsEnd($url));
		}
		else
		{
			$nn = $content_conf['num'];
			$page = init_page();
			$cut = ($page-1)*$nn;
			
			$where = '';
			$file = 'index';
			$link = '';
			$core->tpl->title($lang['static']);
			$core->tpl->uniqTag = 'main';			
			if(isset($url[1]) && $url[1] != 'page')
			{
				$cat = mjsEnd($url);
				
				$altname = filter($cat, 'a');
				$cat_query = $db->query("SELECT id as cid, name FROM ".DB_PREFIX."_categories WHERE altname='" . $altname . "'");
				
				if($db->numRows($cat_query) == 0)
				{
					location();
				}
				
				$cat_info = $db->getRow($cat_query);
				$pLink = '/' . $core->getCat('content', $cat_info['cid'], 'development');
				$where = "AND cat like '%," . $cat_info['cid'] . ",%'";
					if(!empty($cat_info['fulltitle']))
					{
						set_title(array($cat_info['fulltitle']));
					}
					else
					{
						set_title(array($cat_info['name']));
					}
					if(!empty($cat_info['keywords']))
					{
						$core->tpl->keywords = $cat_info['keywords'];
					}
					if(!empty($cat_info['description']))
					{
						$core->tpl->description = $cat_info['description'];
					}	
				
			}
			else
			{
				$pLink = '';
				$where = '';
				set_title(array($lang['static']));
			}
			
			if(!INDEX)
			{
				$core->getCat('content', isset($cat_info['cid']) ? $cat_info['cid'] : '', 'breadcrumb', 1);
				$core->getCatList(isset($cat_info['cid']) ? $cat_info['cid'] : '', 'content', 3);
			}
			
			$query = $db->query("SELECT c.*, l.* FROM " . DB_PREFIX . "_content as c LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=c.id and l.module='content') WHERE l.lang = '" . $core->InitLang() . "' " . $where . " ORDER BY c.date DESC LIMIT " . $cut . ", " . $nn . "");
			
			if($db->numRows($query) > 0) 
			{
				while($static = $db->getRow($query))
				{
					$cat = $static['cat'] !== ',0,' ? $core->getCat('content', $static['cat'], 'short', 3) : '';
					$link = $static['cat'] !== ',0,' ? 'content/' . $core->getCat('content', $static['cat'], 'development') . '/' : 'content/';	
					$short = $core->bbDecode(str($static['short'], 500), $static['id'], true);					
					$core->tpl->loadFile('content/content-main');
					$core->tpl->setVar('TITLE', $static['title']);
					$core->tpl->setVar('SHORT', '<div id="short-' . $static['id'] . '">' . $core->bbDecode(str($static['short'], 500), $static['id'], true) . '</div>');
					$core->tpl->setVar('TEXT', $core->bbDecode($static['short'], $static['id'], true));
					$core->tpl->setVar('DATE', formatDate($static['date']));
					$core->tpl->setVar('CATEGORY', $cat);		
					$core->tpl->setVar('ALTNAME', $static['translate']);
					$core->tpl->setVar('PREVIEW', $static['preview']);
					$core->tpl->sources = preg_replace_callback("#\\[short:([0-9]*?)\\]#is",  
					function($match) use ($short)
					{
						return str($short, $match[1]);
					},$core->tpl->sources);
					$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));	
					$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
					$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $static['preview']);
					$altname = $static['translate'];
					$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
						function($match) use ($link, $altname)
						{
							return format_link($match[1], $link . $altname . '.html');
						},$core->tpl->sources);
					$core->tpl->setVar('ID', $static['id']);
					$core->tpl->end();
				}
				
				list($all) = $db->fetchRow($db->query("SELECT count(c.id) FROM " . DB_PREFIX . "_content as c LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=c.id and l.module='content') WHERE l.lang = '" . $core->InitLang() . "' " . $where));

				
				$core->tpl->pages($page, $nn, $all, 'content' . $pLink . '/{page}');
			}
			else
			{
				$core->tpl->info($lang['static_empty']);
			}
		}
		break;
		

}