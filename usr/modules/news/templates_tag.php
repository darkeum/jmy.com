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
	
	$core->tpl->loadFile('news/news-'.(is_array($core->tpl->uniqTag) ? $core->tpl->uniqTag[0] : empty($core->tpl->uniqTag) ? 'main' : $core->tpl->uniqTag));
	$core->tpl->setVar('TITLE', $news['title']);
	$core->tpl->setVar('SHORT', $short);
	$core->tpl->setVar('FULL', '<div id="full-' . $news['id'] . '">' . $core->bbDecode($news['full'], $news['id'], true) . '</div>');
	$core->tpl->setVar('CATEGORY', $cat);
	$core->tpl->setVar('CAT_ONE', $cat_one);
	$core->tpl->setVar('ALTNAME', $news['altname']);
	$core->tpl->setVar('ICON', isset($catInfo['icon']) ? $core->getCatImg($news_link, $catInfo['icon'], $catInfo['title']) : '');
	$core->tpl->setVar('AUTHOR', '<a href="profile/' . $news['author'] . '" title="' . _PAGE . ': ' . $news['author'] . '">' . $news['author'] . '</a>');
	$core->tpl->setVar('VIEWS', $news['views']);
	$core->tpl->setVar('PREVIEW', $news['preview']);
	$core->tpl->setVar('COMMENTS', $news['comments']);
	$core->tpl->setVar('TAGS', mb_substr($tags, 0, -2));
	$core->tpl->setVar('FULL_LINK', $news_link . $news['altname'] . ".html");	
	$miniImg = _getCustomImg($short);
	$core->tpl->sources = if_sets("#\\[tags\\](.*?)\\[/tags\\]#is", $core->tpl->sources, $news['tags']);
	$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $news['preview']);
	$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
	$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));
	$altname = $news["altname"];
	$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
		function($match) use ($news_link, $altname)
		{
			return format_link($match[1], $news_link . $altname . '.html');
		},$core->tpl->sources);
		
	$news_date = $news['date'];
	$core->tpl->sources = preg_replace_callback("#\\{%MYDATE:(.*?)%\\}#is",  
		function($match) use ($news_date)
		{
			return date($match[1], $news_date);
		},$core->tpl->sources);	
		
	$news_title = $news['title'];
	$core->tpl->sources = preg_replace_callback("#\\{%TITLE:(.*?)%\\}#is",  
		function($match) use ($news_title)
		{
			return short($match[1], $news_title);
		},$core->tpl->sources);	
		
	$core->tpl->sources = preg_replace_callback("#\\{%SHORT:(.*?)%\\}#is",  
		function($match) use ($short)
		{
			// return short($match[1], processText($short));
			return short($match[1], $short); // Убираем \ перед кавычками
		},$core->tpl->sources);	
	
	$array_replace = array(		
		"#\\[edit\\](.*?)\\[/edit\\]#is" => (($core->auth->isModer||$core->auth->isAdmin)  ? "\${1}" : ''),		
		"#\\[img:([0-9]*?)\\]#is" => (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''),	
	);
	
	if(!empty($news['fields']) && $news['fields'] != 'N;')
	{
		$fields = unserialize($news['fields']);
		foreach($fields as $xId => $xData)
		{
			if(!empty($xData[1]))
			{
		$array_replace["#\\[xfield_value:" . $xId . "\\]#is"] = $xData[1];
			}
		}
	}
	
	$news_fields = $news['fields'];
	$core->tpl->sources = preg_replace_callback("#\\[xfield:([0-9]*?)\\](.*?)\\[/xfield:([0-9]*?)\\]#is",  
		function($match) use ($news_fields)
		{
			return ifFields($news_fields, $match[1], $match[2]);
		},$core->tpl->sources);
		
	if($news_conf['showBreadcumb'] == '1')
	{
		$catId = explode(',', $news['cat']);
		$core->tpl->setVar('BREADCUMB', $core->getCat('news', ($catId[1] != 0) ? $catId[1] : '', 'breadcrumb', 1));
	}	
	
	$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?):(.*?)%\\}#is", (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : "\${2}") , $core->tpl->sources);
	$core->tpl->sources = preg_replace("#\\{%IMG:(.*?)%\\}#is",  (!empty($miniImg[(int)"\${1}"]) ? $miniImg[(int)"\${1}"] : ''), $core->tpl->sources);
	$core->tpl->setVar('DATE', formatDate($news['date']));
	$core->tpl->setVar('ID', $news['id']);
	$core->tpl->setVar('RATING', $news['allow_rating'] ? draw_rating($news['id'], 'news', $news['score'], $news['votes']) : '');
	$core->tpl->setVar('EDIT', ($core->auth->isModer||$core->auth->isAdmin)  ? '<a href="news/edit/'.$news['id'].'">'._EDIT.'</a>' : '');
	
	$related_cache = $cache->do_get('related_'.$news['id']);
		if(empty($related_cache) && $news_conf['related_news'] > 0)
		{
	$body_text = $news['title'] . strip_tags(stripslashes(" " . (!empty($news['full']) ? $news['full'] : $news['short'])));
	if(!empty($body_text))
	{
		$rel_query = $db->query("SELECT n.*, l.* FROM ".DB_PREFIX."_news AS n LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='news') WHERE MATCH (`title`, `short`, `full`) AGAINST ('+(" . $db->safesql($body_text) . ")' IN BOOLEAN MODE) AND n.id != " . $news['id'] . " LIMIT ".$news_conf['related_news'], true);
		$related_cache = '';
		if($db->numRows($rel_query) > 0)
		{
			while($related = $db->getRow($rel_query)) 
			{
		$rel_link = $related['cat'] !== ',0,' ? 'news/' . $core->getCat('news', $related['cat'], 'development') . '/' : 'news/';
		$related_cache .= '<li><a href="'.$rel_link . $related['altname'] . '.html">'.$related['title'].' (' . formatdate($related['date']) . ')</a></li>';
			}
		}
		
		$cache->do_put('related_'.$news['id'], $related_cache, 3600);
	}
		}
		$core->tpl->setVar('RELATED', $related_cache);
		$array_replace["#\\[related\\](.*?)\\[/related\\]#is"] = (!empty($related_cache) ? '\\1' : '');
		$core->tpl->sources = preg_replace(array_keys($array_replace), array_values($array_replace), $core->tpl->sources);
	$core->tpl->end();