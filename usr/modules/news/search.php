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
	loadConfig('news');
	$core->loadModLang('news');
	$core->tempModule = 'news';
	global $core, $lang;	
	$where = " AND title LIKE '%" . $db->safesql($query) . "%' OR short LIKE '%" . $db->safesql($query) . "%'  OR short LIKE '%" . $db->safesql($query) . "%'";
	$where .= ' AND c.lang = \'' . $core->InitLang() . '\'';
	$page = init_page();
	$cut = ($page-1)*$news_conf['num'];
	$queryDB = $db->query("SELECT n.*, c.* FROM ".DB_PREFIX."_news as n LEFT JOIN ".DB_PREFIX."_langs as c on(c.postId=n.id and c.module='news') WHERE active!='0' " . $where . " ORDER BY fixed DESC, date DESC LIMIT " . $cut . ", " . $news_conf['num'] . "");

	if($db->numRows($queryDB) > 0) 
	{
		$core->tpl->title($lang['news'].': '.$db->numRows($queryDB));
		
		while($news = $db->getRow($queryDB)) 
		{
			$tag_list = explode(', ', $news['tags']);
				$tag_count = 0;
				$tags = false;			
				foreach($tag_list as $tag) 
				{
					$tag_count++;
					if($tag_count < ($news_conf['tags_num']+1)) 
					{
						$tags .= '<a href="news/tags/' . $tag . '" title="' . $tag . '">' . ($headTag == $tag ? '<strong>' . $tag . '</strong>' : $tag) . '</a>, ';
					}
				}
				$catInfo = $news['cat'] !== ',0,' ? $core->catInfo('news', $news['cat']) : '';
				$cat = $news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'short', 3) : '';
				$news_link = $news['cat'] !== ',0,' ? 'news/' . $core->getCat('news', $news['cat'], 'development') . '/' : 'news/';
				$cat_one = $news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'altname', 1) : 'index';
				$short = $core->bbDecode($news['short'], $news['id'], true);
				$core->tpl->loadFile('news/news-main');
				include(loadTag('news'));	
				$core->tpl->end();
				unset($tags);
		}

		list($all) = $db->fetchRow($db->query("SELECT count(n.id) FROM ".DB_PREFIX."_news as n LEFT JOIN ".DB_PREFIX."_langs as c on(c.postId=n.id and c.module='news') WHERE active!='0' " . $where));
		$core->tpl->pages($page, $news_conf['num'], $all, 'search/' . $query . '/{page}');
	}
	else 
	{
		$result[] = $lang['news_empty'];
	}