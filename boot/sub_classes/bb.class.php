<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

class bb
{

	var $codeArr = array();
	
	function parse($text, $pubId = false, $html = false)
	{
		global $smileRepl, $smiles, $core, $config, $lang;
		$result = $text;	
		if($pubId === true) $html = true;		
		foreach($smiles as $smile => $info)
		{
			$smileRepl .= $smile.'|';
		}	
		$result = preg_replace_callback("%\[code=(php|sql|html|javascript|css|text)\](.+?)\[\/code\]%ius", array(&$this, 'prepareCode'), $result);
		$result = preg_replace_callback("%\[thumb(=left|=right|=center)? alt=(.+?)\](.+?)\[\/thumb\]%ius", array(&$this, 'thumbnailParse'), $result);
		$result = preg_replace_callback("%\[img(=left|=right|=center)? alt=(.+?)\](.+?)\[\/img\]%ius", array(&$this, 'imageParse'), $result);	
		$result = preg_replace_callback("%\[thumb(=left|=right|=center)?\](.+?)\[\/thumb\]%ius", array(&$this, 'thumbnailParse'), $result);$result = preg_replace_callback("%\[img(=left|=right|=center)?\](.+?)\[\/img\]%ius", array(&$this, 'imageParse'), $result);			
		$result = preg_replace_callback("%\[url=(.+?)\](.+?)\[\/url\]%ius", array(&$this, 'formatBBUrl'), $result);	
		$result = preg_replace_callback("%\[email=(.+?)\](.+?)\[\/email\]%ius", array(&$this, 'formatBBEmail'), $result);	
		$result = preg_replace_callback("%\[video\](.+?)\[\/video\]%ius", array(&$this, 'formatBBVideo'), $result);
		$result = preg_replace_callback("%\[audio\](.+?)\[\/audio\]%ius", array(&$this, 'formatBBVideo'), $result);
		$result = preg_replace_callback("%\[spoiler\]%ius", array(&$this, 'spoiler'), $result);	
		$result = preg_replace_callback("%\[spoiler\=(.+?)\]%ius", array(&$this, 'spoiler'), $result);	
		$result = preg_replace_callback('%(' . mb_substr($smileRepl, 0, -1, 'UTF-8') . ')%ius', array(&$this, 'formatSmile'), $result);
				
		$in[] = '%\[b\](.+?)\[/b\]%ius';
		$out[] = '<strong>\\1</strong>';

		$in[] = '%\[i\](.+?)\[\/i\]%ius';
		$out[] = '<i>\\1</i>';

		$in[] = '%\[u\](.+?)\[\/u\]%ius';
		$out[] = '<u>\\1</u>';

		$in[] = '%\[s\](.*)\[\/s\]%ius';
		$out[] = '<s>\\1</s>';    
		
		$in[] = '%\[ul\](.*)\[\/ul\]%ius';
		$out[] = '<ul>\\1</ul>';	
		
		$in[] = '%\[ol\](.*)\[\/ol\]%ius';
		$out[] = '<ol>\\1<li>\\2</li></ol>';
		
		$in[] = '%\[color=(.+?)\](.+?)\[\/color\]%ius';
		$out[] = "<span style=\"color:\\1\">\\2</span>";	
		
		$in[] = '%\[size=([0-9])\](.+?)\[\/size\]%ius';
		$out[] = "<span style=\"font-size:1\\1pt\">\\2</span>";	

		$in[] = '%\[hr\]%iu';
		$out[] = '<hr />';   

		$in[] = '%\[br\]%iu';
		$out[] = '<br />';    
		
		$in[] = '%\[left\](.+?)\[\/left\]%ius';
		$out[] = '<div align="left">\\1</div>';       
		
		$in[] = '%\[flash\](.+?)\[\/flash\]%ius';
		$out[] = '<!--flash--><object align="middle" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="sameDomain" name="allowScriptAccess"><param value="\\3" name="movie"><param value="high" name="quality"><param value="#ffffff" name="bgcolor"><param value="transparent" name="wmode"><embed width="\\1" height="\\2" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" name="bubbles" wmode="transparent" bgcolor="#ffffff" quality="high" src="\\3"></object><!--flash:end-->';        
		
		$in[] = '%\[flash=([0-9]+?)x([0-9]+?)\](.+?)\[\/flash\]%ius';
		$out[] = '<!--flash:\\1x\\2--><object align="middle" width="\\1" height="\\2" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="sameDomain" name="allowScriptAccess"><param value="\\3" name="movie"><param value="high" name="quality"><param value="#ffffff" name="bgcolor"><param value="transparent" name="wmode"><embed align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" name="bubbles" wmode="transparent" bgcolor="#ffffff" quality="high" src="\\3"></object><!--flash:end-->';        
		
		$in[] = '%\[justify\](.+?)\[\/justify\]%ius';
		$out[] = '<div align="justify">\\1</div>';    

		$in[] = '%\[center\](.+?)\[\/center\]%ius';
		$out[] = '<div align="center">\\1</div>';

		$in[] = '%\[right\](.+?)\[\/right\]%ius';
		$out[] = '<div align="right">\\1</div>';	
		
		$in[] = '%\[/spoiler\]%si';
		$out[] = '</div></div><!--spoiler:end-->';			
		$result = preg_replace($in, $out, $result);
		$replace = array(
			'[quote]' => '<!--quote--><div class="quote"><strong>' . $lang['quote'] . ':</strong><br />',
			'[/quote]' => '</div><!--quote:end-->',
			'[*]' => '<li>',
             '%\[blockquote\](.*?)\[/blockquote\]%si' => "<blockquote>\\1</blockquote>",
             '%\[sub\](.*?)\[/sub\]%si' => "<sub>\\1</sub>",
             '%\[sup\](.*?)\[/sup\]%si' => "<sup>\\1</sup>",
             '%\[li\](.*?)\[\/li\]%si' => "<li>\\1</li>",
             '%\[h1\](.+?)\[/h1\]%si' => "<h1>\\1</h1>",
             '%\[h2\](.+?)\[/h2\]%si' => "<h2>\\1</h2>",
             '%\[h3\](.+?)\[/h3\]%si' => "<h3>\\1</h3>",
             '%\[h4\](.+?)\[/h4\]%si' => "<h4>\\1</h4>",
             '%\[h5\](.+?)\[/h5\]%si' => "<h5>\\1</h5>",
             '%\[h6\](.+?)\[/h6\]%si' => "<h6>\\1</h6>",

		);
		
		$result = str_replace(array_keys($replace), array_values($replace), $result);			
		$result = preg_replace_callback("#<<code::(.*?)::(.*?)::code>>#s", array(&$this, 'highlight_code'), $result);			
		$this->htmlArr = array();		
		return stripslashes($result);
	}
	
	function bbSite($text, $pubId)
	{
	global $core, $config;		
		$text = preg_replace_callback( "%\[hide\](.+?)\[\/hide\]%ius", array(&$this, 'hide'), $text);		
		if(strpos($text, "[attach=") !== false && $pubId)
		{
			$text = $this->parseAttach($text, $pubId);
		}		
		if($core->html_editor == 1)
		{
			$text = preg_replace_callback( "%\[video\](.+?)\[\/video\]%ius", array( &$this, 'formatBBVideo'), $text);	
			$text = preg_replace_callback( "%\[audio\](.+?)\[\/audio\]%ius", array( &$this, 'formatBBVideo'), $text);	

		}
		
		if(eregStrt("--ThumbNail--", $text))
		{
			if(empty($core->tpl->headerIncludes['thumbNail']))
			{
				require(ROOT . 'media/imageEffects/' . $config['imageEffect'] . '/init.php');
				$core->tpl->headerIncludes['thumbNail'] = $js;
			}
		}		
		
		if(eregStrt("!--code:", $text))
		{
			if(empty($core->tpl->headerIncludes['hightlightCode']))
			{
				$core->tpl->endJs= '
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shCore.js"></script>
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shBrushCss.js"></script>
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shBrushJScript.js"></script>
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shBrushPhp.js"></script>
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shBrushSql.js"></script>
					<script type="text/javascript" src="usr/plugins/highlight_code/scripts/shBrushPlain.js"></script>
					<link type="text/css" rel="stylesheet" href="usr/plugins/highlight_code/styles/shCoreDefault.css"/>
					<script type="text/javascript">SyntaxHighlighter.all();</script>';
			}
		}
		if(eregStrt("!--audio:", $text)||"!--video:player")
		{		
		$core->tpl->players = "
		<link rel=\"stylesheet\" href=\"usr/plugins/player/skin/mediaelementplayer.css\" />
		<script src='usr/plugins/player/lib/mediaelement.js'></script>
		<script src='usr/plugins/player/lib/mediaelementplayer.js'></script>
		<script>
		/* <![CDATA[ */
			jQuery(document).ready(function($) {
				$('audio,video').mediaelementplayer({
					videoWidth: '100%',
					videoHeight: '100%',
					audioWidth: '100%',
					features: ['playpause','progress','tracks','volume','fullscreen'],
					videoVolume: 'horizontal'
				});
			});
		/* ]]> */
		</script>";
		}
		return $text;
	}
	
	function imageParse($matches = array())
	{
		global $config;
		if (isset($matches[3]))
		{
			$img = $matches[3];
			$align = $matches[1];
			$alt = $matches[2];
		}
		else
		{
			$img = $matches[2];
			$align =  $matches[1];
			$alt = '';
		}
		$align = str_replace('=', '', $align);
		require ROOT . 'etc/files.config.php';
		$linked = eregStrt('http://', $img)||eregStrt('https://', $img) ? true : false;
		
		if($linked == false) 
		{
			list($width, $height, $type, $attr) = @getimagesize($img);
		}
		else
		{
			$width = '';
			$height = '';
			$type = '';
		}
		
		if(($width && $height && $type) OR $linked == true)
		{
			if(isset($width) && $width > $files_conf['thumb_width'])
			{
				return stripslashes('<!--IMG--><img src="' . $img . '" width="' . $files_conf['thumb_width']. '" border="0" alt="' . (empty($alt) ? '' : ' title="'.stripslashes($alt).'"') . (empty($alt) ? '' : ' title="'.stripslashes($alt)).'"' . ' ' . (!empty($align) ? 'align="' . $align . '"' : '') . ' hspace="10" /><!--IMG:end-->');
			}
			
			return stripslashes('<!--IMG--><img src="' . $img . '" border="0" alt="' . (empty($alt) ? '' : stripslashes($alt)) . '"' . (empty($alt) ? '' : ' title="'.stripslashes($alt).'"') . ' style="max-width:' . $files_conf['thumb_width']. 'px;" ' . (!empty($align) ? 'align="' . $align . '"' : '') . ' hspace="10" /><!--IMG:end-->');
		}
	}	

	function thumbnailParse($matches = array())
	{
		if (isset($matches[3]))
		{
			$img = $matches[3];
			$align = $matches[1];
			$req = false;
			$alt = $matches[2];
		}
		else
		{
			$img = $matches[2];
			$align =  $matches[1];
			$req = false;
			$alt = '';
		}
		global $core, $config;
		static $js, $picture;
		$align = str_replace('=', '', $align);
		
		if(($img && $config['imageEffect'] && file_exists(ROOT . $img)) || $req == true)
		{
			if($req)
			{
				require ROOT . 'etc/files.config.php';
			}
			
			$full = str_replace('thumb/thumb-', '', $img);
			if(file_exists(ROOT . $full) || $req == true)
			{
				if(empty($js) && empty($picture))
					require(ROOT . 'media/imageEffects/' . $config['imageEffect'] . '/init.php');

				$repl = array(
					'{full}' => $full,
					'{thumb}' => $img,
					'{img}' => 'alt="' . (empty($alt) ? '' : stripslashes($alt)) . '"' . (empty($alt) ? '' : ' title="'.stripslashes($alt).'"') . ($req ? ' width="' . $files_conf['thumb_width']. '"' : '') . (!empty($align) ? ' align="' . $align . '"' : ''),
					'{href}' => ''
				);
				
				return stripslashes('<!--ThumbNail-->'.img_preview(str_replace(array_keys($repl), array_values($repl), $picture), 'box').'<!--ThumbNail:end-->');
			}
		}
	}
	function LOADTPL($file)
	{
		global $core, $config;
		$loadDefault = 'usr/tpl/default/' . $file . $core->tpl->ext;
		$loadTheme = 'usr/tpl/'.$config['tpl'].'/' . $file . $core->tpl->ext;
		if (isset($loadTheme)) 
		{
			$text = file_get_contents(ROOT . $loadDefault);		
		}
		else
		{
			$text = file_get_contents(ROOT . $loadTheme);		
		}
		return $text;
	}

	function parseAttach($text, $pubId)
	{
	global $core, $db, $lang;
		$module = $core->getMod(true);
		$pubId = intval($pubId);
		$q = $db->query("SELECT * FROM `" . DB_PREFIX . "_attach` WHERE `pub_id`='" . $pubId . "' AND `mod`='" . $module . "'");		
		if($db->numRows($q) > 0) 
		{
			$first = $this->LOADTPL('attach');
			$stat = $first;
			$position=strpos($stat,'[static]');
			$stat=substr($stat,$position);
			$position=strpos($stat,'[/static]');
			$stat=substr($stat,0,$position);
			$stat = preg_replace( "#\\[static]#is", '', $stat);				
			$first = preg_replace( "#\\[static](.*?)\\[/static]#is", '', $first);			
			echo $stat;			
			while($rows = $db->getRow($q))
			{				
				if($core->auth->group_info['showAttach'] == 1)
				{	
					$replace = $first;
					$replace = str_replace('{%NUMB%}', $rows['downloads'], $replace);
					$replace = str_replace('{%SIZE%}', formatfilesize(@filesize($rows['url']), true), $replace);
					$replace = str_replace('{%ID%}', $rows['id'], $replace);
					$replace = str_replace('{%NAME%}', $rows['name'], $replace);						
				}
				else
				{
					$replace = $lang['attach_deny'];
				}				
				$text = str_replace('[attach=' . $rows['id'] . ']', $replace, $text);
			}
		}		
		return stripslashes($text);
	}


	function spoiler($matches = array())
	{
		global $lang;
		$title = '';
		if (isset($matches[1]))
		{
			$title=$matches[1];
		}		
		$code = gencode(5);
		return '<!--spoiler--><div class="spoiler"><a href="javascript:void(0)" onclick="showhide(\'sp' . $code . '\')">' . (!empty($title) ? '<span class="_spoilertitle">'.stripslashes($title).'</span>' : $lang['spoiler_expand']) . '</a><div id="sp' . $code . '" style="display:none;"><br />';	
	}
	
	function hide($content)
	{
		global $core, $lang;
		if($core->auth->group_info['showHide'] == 1)
		{
			return stripslashes($content[1]);
		}
		else
		{
			return '<div class="hide"><strong>' . str_replace('[group]', $core->auth->group_info['gname'], $lang['gr_genide']) . '</strong></div>';
		}
	}
	
	function highlight_code($count, $lang = 'plain')
	{
	global $user, $lang;
			if(isset($this->codeArr[$count]))
			{				
				$mainCodeName = $lang;
				$code = htmlspecialchars_decode($this->codeArr[$count]);
				return '<!--code:' . $lang . '--><div class="codeBox"><div class="codeTitle">' . $lang['code'] . ' - ' . strtoupper($mainCodeName) . '</div><div class="codeContent" style="overflow-x:auto;"><pre class="brush: ' . ($lang == 'html' || $lang == 'text' ? 'plain' : $lang) . ';">' . wordwrap(str_replace('&amp;#123;', '&#123;', htmlspecialchars($code)), 110, "\n", true) . '</pre></div></div><!--code:end-->';
			}
	}
	
	private function prepareHTML($matches = array())
	{
		$content = $matches[1];
		if(empty($this->htmlArr))
		{
			$count = -1;
		}
		else
		{
			$count = (count($this->htmlArr)-1);
		}
		
		$count++;
		$this->htmlArr[] = stripslashes($content);
		
		return '<<html::' . $count . '::html>>';
	}	
	
	
	private function prepareCode($matches = array())
	{
		$content = $matches[2];
		$php = $matches[1];
		if(empty($this->codeArr))
		{
			$count = -1;
		}
		else
		{
			$count = (count($this->codeArr)-1);
		}
		
		$count++;
		$this->codeArr[] = stripslashes($content);
		
		return '<<code::' . $count . '::' . $php . '::code>>';
	}
	
	
	private function doHtml($count, $fromParse = false)
	{
		if(isset($this->htmlArr[$count]) && $fromParse == false)
		{
			return '<!--html_text-->'.$this->htmlArr[$count].'<!--html_text:end-->';
		}
		elseif(isset($this->htmlArr[$count]) && $fromParse)
		{
			return '[html]'.$this->htmlArr[$count].'[/html]';
		}
	}	
	
	private function doCode($count, $php)
	{
		return '[code=' . $php . ']'.stripslashes(str_replace("\n", '', $this->codeArr[$count])).'[/code]';
	}
	
	private function covertNl2Br($content)
	{
		return nl2br(stripslashes($content));
	}

	private function formatBBUrl($matches = array())
	{
		global $lang;
		$url = $matches[1];
		$content = $matches[2];
		if(!empty($url) && !empty($content))
		{
			if(eregStrt('://', $url))
			{
				$arr = explode('://', $url);
				return '<!--url--><a href="go.php?url=' . base64_encode($url) . '" title="' . $lang['link'] . '" target="_blank" onclick="javascript:this.href=\'' . $arr[0] . '://' . htmlspecialchars($arr[1], ENT_QUOTES) . '\'" onmouseover="javascript:this.href=\'' . $arr[0] . '://' . htmlspecialchars($arr[1], ENT_QUOTES) . '\'">' . stripslashes($content) . '</a><!--url:end-->';
			}
			else
			{
				return '<a href="' . $url . '" title="' . $lang['link'] . '">' . stripslashes($content) . '</a>';
			}
		}
	}
	
	function smileDecode($matches = array())
	{
		$url = $matches[1];
	global $smiles;
			

		foreach($smiles as $smile => $info)
		{
			$decode[$info['url']] = $smile;
		}
		
		return !empty($decode[$url]) ? $decode[$url] : '';
	}
	
	function imgDecode($matches = array())
	{
		$url = $matches[1];
		$alt = $matches[2];
		$align = (isset($matches[3]) ? $matches[3] : '');
		$type = 'img';
		global $config;
		return stripslashes('[' . $type . '' . (!empty($align) ? '='.$align : '') . (!empty($alt) ? ' alt='.$alt : '') . ']' . str_replace($config['url'].'/', '', $url) . '[/' . $type . ']');
	}
	
	function thumbDecode($matches = array())
	{
		$url = $matches[1];
		$alt = $matches[2];
		$align = (isset($matches[3]) ? $matches[3] : '');
		$type = 'thumb';
		global $config;
		return stripslashes('[' . $type . '' . (!empty($align) ? '='.$align : '') . (!empty($alt) ? ' alt='.$alt : '') . ']' . str_replace($config['url'].'/', '', $url) . '[/' . $type . ']');
	}
		

	function htmltobb($text)
	{
	global $smileRepl, $smileRepl2, $smiles, $core, $lang;
	
		foreach($smiles as $smile => $info)
		{
			$smileRepl .= $info['url'].'|';
		}
		$text = preg_replace_callback('%<!--code:(.*?)-->.*?class="brush: .*?">(.*?)</pre></div></div><!--code:end-->%ius',
			array(&$this, 'prepareCode'), $text);		
		$text = preg_replace_callback('%<!--ThumbNail-->.*?src="(.*?)".*?alt="(.*?)".*?align="(.*?)".*?<!--ThumbNail:end-->%ius',
			array(&$this, 'thumbDecode'), $text);	
		$text = preg_replace_callback('%<!--ThumbNail-->.*?src="(.*?)".*?alt="(.*?)".*?<!--ThumbNail:end-->%ius',
			array(&$this, 'thumbDecode'), $text);	
		$text = preg_replace_callback('%<img src="(' . $smileRepl . ')".*?alt="" border="0" style="vertical-align:middle" />%ius',
			array(&$this, 'smileDecode'), $text);
		
        $array_html = array(
            '%<!--quote--><div class="quote"><strong>' . $lang['quote'] . ':</strong>(.*?)</div><!--quote:end-->%ius',           
            '%<!--flash-->.*?src="(.*?)".*?<!--flash:end-->%ius',
            '%<!--flash:([0-9]*)x([0-9]*)-->.*?src="(.*?)".*?<!--flash:end-->%ius',
            '%<!-- video:(.*?):(.*?) -->.*?value="(.*?)".*?<!-- video:(.*?):end -->%ius',
			'%<!-- video:youtube:(.*?) -->.*?src="(.*?)".*?<!-- video:youtube:end -->%ius',
			'%<!-- video:rutube:(.*?) -->.*?src="(.*?)".*?<!-- video:rutube:end -->%ius',
			'%<!-- video:twitch:(.*?) -->.*?src="(.*?)".*?<!-- video:twitch:end -->%ius',
            '%<!-- video:player:(.*?) -->.*?src="(.*?)".*?<!-- video:end -->%ius',
            '%<!-- audio:(.*?) -->.*?src="(.*?)".*?<!-- audio:end -->%ius',
            '%<!--spoiler--><div class="spoiler">.*?<span class="_spoilertitle">(.*?)</span>.*?style="display:none;">%ius',
			'%<!--spoiler--><div class="spoiler">.*?style="display:none;">%ius',
            '%</div></div><!--spoiler:end-->%ius',  
		);
		
    
        $array_bb = array(
			"[quote]\\1[/quote]",
			"[flash]\\1[/flash]",
			"[flash=\\1x\\2]\\3[/flash]",			
			"[video]\\3[/video]",			
			"[video]\\2[/video]",
			"[video]https://rutube.ru/video/\\1[/video]",
			"[video]https://www.twitch.tv/\\1[/video]",
			"[video]\\2[/video]",
			"[audio]\\2[/audio]",
			"[spoiler=\\1]",
			"[spoiler]",
			"[/spoiler]",
        );
	
		$text = preg_replace($array_html, $array_bb, $text);		
		$text = stripslashes($text);		
		$text = preg_replace_callback("#<<code::(.*?)::(.*?)::code>>#s", array(&$this, 'highlight_code'), $text);	
		return $text;
	}
	
	private function formatBBEmail($matches = array())
	{
		global $lang;
		$mail = $matches[1];
		$content = $matches[2];
		if(!empty($mail) && !empty($content))
		{
			if(eregStrt('@', $mail))
			{
				$arr = explode('@', $mail);
				return '<a href="javascript:void(0)" title="' . $lang['link'] . '" target="_blank" onclick="javascript:this.href=\'mailto:' . $arr[0] . '\'+\'@' . htmlspecialchars($arr[1], ENT_QUOTES) . '\'" onmouseover="javascript:this.href=\'mailto:' . $arr[0] . '\'+\'@' . htmlspecialchars($arr[1], ENT_QUOTES) . '\'">' . $content . '</a>';
			}
			else
			{
				return '<a href="javascript:void(0)" onclick="javascript:this.href=\'mailto:' . $mail . '\'" onmouseover="javascript:this.href=\'mailto:' . $mail . '\'" title="' . $lang['link'] . '" target="_blank">' . $content . '</a>';
			}
		}
	}

	private function formatBBVideo($matches=array())
	{
	global $core, $config;
		$url = $matches[1]; 
		$parseUrl = parse_url(htmlspecialchars_decode($url, ENT_QUOTES));
		$query = array();
		if(isset($parseUrl['query']))
		{
			parse_str($parseUrl['query'], $query);
		}
		$host = getHost($url);
		$type = getExt($url);
		if($host == 'youtube.com')
		{	
			if (eregStrt('/v/', $url))
			{
				$id = str_replace('http://www.youtube.com/v/', '', $url);
				$id = str_replace('https://www.youtube.com/v/', '', $url);
			}
			elseif (eregStrt('/embed/', $url))
			{
				$id = str_replace('http://www.youtube.com/embed/', '', $url);
				$id = str_replace('https://www.youtube.com/embed/', '', $url);
			}			
			else
			{
				$id = $query['v'];
			}	
			if($id)
			{			
				return '<!-- video:youtube:' . $id . ' --><iframe width="640" height="385" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe><!-- video:youtube:end -->';
			}
		}
		
		elseif($host == 'rutube.ru')
		{			
			if (eregStrt('/video/', $url))
			{
				$id = str_replace('http://rutube.ru/video/', '', $url);
				$id = str_replace('https://rutube.ru/video/', '', $url);
				$position = strpos($id,'/');
				if (!isset($position))
				{
					$id = substr($id,0,$position);
				}
			}
			elseif (eregStrt('/play/embed/', $url))
			{
				$id = str_replace('http://rutube.ru/play/embed/', '', $url);
				$id = str_replace('https://rutube.ru/play/embed/', '', $url);
				$position = strpos($id,'?');
				if (!isset($position))
				{
					$id = substr($id,0,$position);
				}
			}			
			else
			{
				$id = $query['v'];
			}
			if($id)
			{			
				return '<!-- video:rutube:' . $id . ' --><iframe width="640" height="385" src="https://rutube.ru/play/embed/'.$id.'?autoStart=false" frameborder="0" allowfullscreen></iframe><!-- video:rutube:end -->';
			}
			
		}
		elseif($host == 'twitch.tv')
		{			
			if (eregStrt('twitch.tv/', $url))
			{
				$id = str_replace('http://www.twitch.tv/', '', $url);	
				$id = str_replace('https://www.twitch.tv/', '', $url);				
				$position = strpos($id,'/');
				if (!isset($position))
				{
					$id = substr($id,0,$position);
				}
				
			}		
			else
			{
				$id = $query['v'];
			}
			if($id)
			{			
				return '<!-- video:twitch:' . $id . ' --><iframe width="640" height="385" src="https://www.twitch.tv/'.$id.'/embed?autoplay=false" frameborder="0" allowfullscreen></iframe><!-- video:twitch:end -->';
			}
		}	
		elseif($host == 'smotri.com')
		{
			$id = $query['id'];
			if($id)
			{
				return '<!-- video:smotri:' . $id . ' --><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="400" height="330"><param name="movie" value="https://pics.smotri.com/scrubber_custom8.swf?file=' . $id . '&amp;bufferTime=3&autoStart=false&str_lang=eng&amp;xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_lightaqua.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed src="https://pics.smotri.com/scrubber_custom8.swf?file=' . $id . '&amp;bufferTime=3&amp;autoStart=false&str_lang=eng&amp;xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_lightaqua.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="400" height="330" type="application/x-shockwave-flash"></embed></object><!-- video:smotri:end -->';
			}
		}
		elseif($type == 'flv' || $type == 'mp4' || $type == '3gp' || $type == 'webm' || $type == 'm4v')
		{
			$code = rand(1, 100000);
			
			return '<!-- video:player:'.$code.' --><div id="video-container"><video controls="controls" width="640" height="360" poster=""><source src="'. ($host == 'files' ? $config['url'].'/'.$url : $url) .'" type="video/'.$type.'" /><object width="640" height="360" type="application/x-shockwave-flash" data="usr/plugins/player/lib/flashmediaelement.swf" />
				<param name="movie" value="usr/plugins/player/lib/flashmediaelement.swf" /><param name="flashvars" value="controls=true&amp;file='. ($host == 'files' ? $config['url'].'/'.$url : $url) .'" /></object></video></div><!-- video:end -->';
		}
		elseif($type == 'mp3')
		{
			$code = rand(1, 100000);
			$arr = explode('/', $url);
			return '<!-- audio:'.$code.' --><div id="audio-container"><audio controls="" preload="none" width="640" height="30" src="' . ($host == 'files' ? $config['url'].'/'.$url : $url) . '"></audio></div><!-- audio:end -->';
		}
	}	

	private function formatSmile($matches=array())
	{
	global $smiles;
		$smile = $matches[1];
		if(is_array($smiles[$smile]))
		{
			return '<img src="' . $smiles[$smile]['url'] . '" title="' . $smiles[$smile]['title'] . '" alt="" border="0" style="vertical-align:middle" />';
		}
	}
}