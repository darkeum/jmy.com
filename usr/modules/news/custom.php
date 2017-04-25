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

	global $db, $config, $core, $tags, $news_conf, $url, $headTag, $cache;
	$where = ' AND c.lang = \'' . $core->InitLang() . '\'';
	if($category != 'all')
	{
		$catsArr = array_map('trim', explode(',', $category));
		$i = 0;
		foreach($catsArr as $cat)
		{
			$i++;
			if($i == 1) $where .= " AND "; else $where .= " OR ";
			$where .= "cat like '%," . $cat . ",%'";
		}
	}

	if(!empty($notin))
	{
		$notcatsArr = array_map('trim', explode(',', $notin));
		foreach($notcatsArr as $cat)
		{
			$where .= " AND cat NOT LIKE '%," . $cat . ",%'";
		}
	}

	if(($order!='date')&($order!='views')&($order!='votes')&($order!='comments')&($order!='allow_index'))
	{
		$order='date';
	}	
	if(($short!='DESC')&($short!='ASC'))
	{
		$short='DESC';
	}
	
	$core->loadModLang('news');
	$core->tempModule = 'news';
	$queryDB = $db->query("SELECT n.*, c.* FROM ".DB_PREFIX."_news as n LEFT JOIN ".DB_PREFIX."_langs as c on(c.postId=n.id and c.module='news') WHERE active='1' " . $where . " ORDER BY " . $order . " " . $short . " LIMIT 0, " . $limit . "");
	$custom = '';
	if($db->numRows($queryDB) > 0) 
	{
		$news = '';
		while($news = $db->getRow($queryDB)) 
		{
			$tag_list = explode(',', $news['tags']);
				$tag_count = 0;
				$tags = false;			
				foreach($tag_list as $tag) 
				{
					$tag_count++;
					if($tag_count < ($news_conf['tags_num']+1)) 
					{
						$tags .= '<a href="news/tags/' . $tag . '" title="' . $tag . '">' . ($headTag == $tag ? '<strong>' . $tag . '</strong>' : $tag) . '</a>';
					}
				}	
			$catInfo = $news['cat'] !== ',0,' ? $core->catInfo('news', $news['cat']) : '';
			$cat = $news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'short', 3) : '';
			$news_link = $news['cat'] !== ',0,' ? 'news/' . $core->getCat('news', $news['cat'], 'development') . '/' : 'news/';
			$cat_one = $news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'altname', 1) : 'index';
			$short = $core->bbDecode($news['short'], $news['id'], true);
			$miniImg = _getCustomImg($short);		
			ob_start();
			$core->tpl->loadFile($template);
			include(loadTag('news'));	
			$custom .= $core->tpl->return_end();
			unset($tags);
			ob_end_clean();
		}
	}