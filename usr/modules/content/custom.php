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
			global $core, $db, $news_conf;
			$where = 'l.lang = \'' . $core->InitLang() . '\'';
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

			$core->loadModLang('content');
			$core->tempModule = 'content';

			$custom = '';

			$query = $db->query("SELECT c.*, l.* FROM " . DB_PREFIX . "_content as c LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=c.id and l.module='content') WHERE " . $where . " ORDER BY c.date DESC LIMIT 0, " . $limit . "");
			
			if($db->numRows($query) > 0) 
			{
				while($static = $db->getRow($query))
				{
					$cat = $static['cat'] !== ',0,' ? $core->getCat('content', $static['cat'], 'short', 3) : '';
					$link = $static['cat'] !== ',0,' ? 'content/' . $core->getCat('content', $static['cat'], 'development') . '/' : 'content/';
					$short = $core->bbDecode(str($static['short'], 500), $static['id'], true);
					$miniImg = _getCustomImg($short);
					ob_start();
					$core->tpl->loadFile($template);
					$core->tpl->setVar('TITLE', $static['title']);
					$core->tpl->setVar('SHORT', '<div id="short-' . $static['id'] . '">' . $short . '</div>');
					$core->tpl->setVar('DATE', formatDate($static['date']));					
					$core->tpl->setVar('PREVIEW', $static['preview']);
					$core->tpl->sources = preg_replace_callback("#\\[short:([0-9]*?)\\]#is",  
					function($match) use ($short)
					{
						return str($short, $match[1]);
					},$core->tpl->sources);
					$core->tpl->sources = if_sets("#\\[mini_img\\](.*?)\\[/mini_img\\]#is", $core->tpl->sources, (!empty($miniImg[0]) ? true : ''));	
					$core->tpl->sources = if_sets("#\\[category\\](.*?)\\[/category\\]#is", $core->tpl->sources, $cat);
					$core->tpl->sources = if_sets("#\\[preview\\](.*?)\\[/preview\\]#is", $core->tpl->sources, $static['preview']);
					$core->tpl->sources = preg_replace("#\\[img:([0-9]*?)\\]#is", (!empty($miniImg[0]) ? '<img src="' . $miniImg[0] . '" border="0" width="\\1" />' : ''), $core->tpl->sources);
					$core->tpl->setVar('CATEGORY', $cat);
					$core->tpl->setVar('ALTNAME', $static['translate']);
					$altname = $static['translate'];
					$core->tpl->sources = preg_replace_callback("#\\[more\\](.*?)\\[/more\\]#is",  
						function($match) use ($link, $altname)
						{
							return format_link($match[1], $link . $altname . '.html');
						},$core->tpl->sources);
					$core->tpl->setVar('ID', $static['id']);
					$core->tpl->end();
					$custom .= $core->tpl->return_end();
				}
			}

