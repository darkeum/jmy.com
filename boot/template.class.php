<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) 
{
	header('Location: /');
	exit;
}
 
class template
{

	var $ext = '.tpl';
	var $sep = '%';
	var $sources = false;
	var $vars = array();
	var $vars_block = array();
	var $vars_tpl = array();
	var $file_dir = '';
	var $file = false;
	var $tplDir = false;
	var $filesTpl = false;
	var $cahceTpl = array();
	var $open_file = 'table';	
	var $headerIncludes = array();
	var $bodyIncludes = '';
	var $keywords = false;
	var $title = false;
	var $admin_title = false;
	var $feed_link = '';
	var $uniqTag = array();
	var $fullAjaxBody = '';
	var $adminTheme = false;	
	var $time_compile = false;
	var $startCompile = false;
	var $starWidth = 19;
	var $blocks = array();
	var $modules = array();
	var $moduleArray = '';
	var $adminBar = '';
	var $endJs = '';
	var $players = '';
	
	
	function __construct()
	{
		global $db, $config, $url;
        if($url[0] == ADMIN)
        {
            $this->adminTheme = true;
            $this->file_dir = 'usr/tpl/admin/';
            $this->sep = '';
        }       	
		
		if(!isset($_COOKIE['smartphone']))
		{
			setcookie('smartphone', 1);		
		}
		
		if(empty($this->file_dir))
		{
			if (($config['smartphone']=='1')&&($this->check_phone()==true)&&(($_COOKIE['smartphone'] == 1)||(!isset($_COOKIE['smartphone']))))
			{
				$this->file_dir = 'usr/tpl/smartphone/';
			}
			else
			{
				$this->file_dir = 'usr/tpl/'.$config['tpl'].'/';
			}
		}
		
		$this->filesTpl = getcache('tplFiles');
		
		if(empty($this->filesTpl) || $this->filesTpl['theme'] != $this->file_dir || ($config['tpl_change'] == '0' && 'usr/tpl/'.$config['tpl'].'/' != $this->file_dir))
		{
			$this->filesTpl = listFiles($this->file_dir, $this->ext);
			$this->filesTpl['theme'] = $this->file_dir;
			setcache('tplFiles', $this->filesTpl);
		}


		$modArr = getcache('plugins');
		
		if (empty($modArr)) 
		{
			$query = $db->query("SELECT * FROM `".DB_PREFIX."_plugins` WHERE `active` = '1' ORDER BY priority ASC");
			while ($result = $db->getRow($query)) 
			{
				if($result['service'] == 'blocks')
				{
					$this->blocks[$result['id']] = $result;
				}
				else
				{
					$this->modules[$result['title']] = $result;
				}
			}
			setcache('plugins', array($this->blocks, $this->modules));
		}
		else
		{
			$this->blocks = $modArr[0];
			$this->modules = $modArr[1];
		}
	}
    
    function __destruct()
    {
        die();
    }	

	private function getThemeFile($file)
	{
	global $url;
		$file = $this->file_dir.$file;
		$loadFile = '';
		if(!empty($this->uniqTag))
		{
			if(!is_array($this->uniqTag))
			{
				$this->uniqTag = array($this->uniqTag);
			}
			
			if(INDEX == true) $this->uniqTag[] = 'index';

			foreach($this->uniqTag as $uniq)
			{
				if(in_array($file . '-' . $url[0]  . '-' . $uniq . $this->ext, $this->filesTpl))
				{
					$loadFile = $file . '-' . $url[0]  . '-' . $uniq . $this->ext;
					break;
				}
				elseif(in_array($file . '_' . $uniq . $this->ext, $this->filesTpl))
				{
					$loadFile = $file . '_' . $uniq . $this->ext;
					break;
				}	
				elseif(in_array($file . '--' . $uniq . $this->ext, $this->filesTpl))
				{
					$loadFile = $file . '--' . $uniq . $this->ext;
					break;
				}			
			}
		}
		
		if(empty($loadFile))
		{
			if(in_array($file . '-' . $url[0] . $this->ext, $this->filesTpl))
			{
				$loadFile = $file . '-' . $url[0] . $this->ext;
			}
			elseif(in_array($file . $this->ext, $this->filesTpl))
			{
				$loadFile = $file . $this->ext;
			}	
		}
		
	
		return $loadFile;
	}

	
	public function loadFileADM($file, $check = false) 
	{
		$loadDefault = 'usr/tpl/admin/' . $file . $this->ext;
	
		$loadUrl = $loadDefault;
	
		if(!isset($this->cacheTpl[$loadUrl]))
		{
			if(is_file(ROOT . $loadUrl))
			{
				$this->cacheTpl[$loadUrl] = file_get_contents(ROOT . $loadUrl);
			}
			elseif(is_file(ROOT . $loadDefault))
			{
				$this->cacheTpl[$loadDefault] = file_get_contents(ROOT . $loadDefault);
				$loadUrl = $loadDefault;
			}
			else
			{
				if($check == false)
				{
					delcache('tplFiles');
					$this->filesTpl = listFiles($this->file_dir, $this->ext);
					$this->loadFile($file, true);
				}
				else
				{
					fatal_error(_ERROR, str_replace('[file]', $file . $this->ext, _FILE_NOT_FOUND));
				}
			}
		}
		
		$this->file = $loadUrl;		
		$lo = explode('/', $loadUrl);
		$this->tplDir = $lo[2];
		$this->startCompile = microtime(1);
		$this->sources = $this->cacheTpl[$loadUrl];
	}
	
	function check_phone() 
	{ 
		$agent_now = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		$agent_array = array('opera mini', 'ipad', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'iphone', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser'); 
		foreach ($agent_array as $agent) 
		{ 
			if ( strpos($agent_now, $agent) !== false )
			{
				return true;
			}
 
		}
		return false; 
	}
	
	
	public function loadFile($file, $check = false) 
	{
		global $config, $url, $db, $core;		
		$loadDefault = 'usr/tpl/default/' . $file . $this->ext;	
		if($this->adminTheme == false)
		{
			$loadUrl = $this->getThemeFile($file);
		}
		else if ($config['tpl_change'] == '0')
		{
			$loadUrl = 'usr/tpl/'.$config['tpl'].'/' . $file . $this->ext;
			
		}
		else
		{
			$loadUrl = $this->file_dir . $file . $this->ext;
			if(isset($this->cacheTpl[$loadDefault])) $loadUrl = $loadDefault;
		}

		if(!isset($this->cacheTpl[$loadUrl]))
		{
			if(is_file(ROOT . $loadUrl))
			{
				$this->cacheTpl[$loadUrl] = file_get_contents(ROOT . $loadUrl);
			}
			elseif(is_file(ROOT . $loadDefault))
			{
				$this->cacheTpl[$loadDefault] = file_get_contents(ROOT . $loadDefault);
				$loadUrl = $loadDefault;
			}
			else
			{
				if($check == false)
				{
					delcache('tplFiles');
					$this->filesTpl = listFiles($this->file_dir, $this->ext);
					$this->loadFile($file, true);
				}
				else
				{
					fatal_error(_ERROR, str_replace('[file]', $file . $this->ext, _FILE_NOT_FOUND));
				}
			}
		}
		
		include(ROOT.'boot/vars.class.php');
		$this->file = $loadUrl;		
		$lo = explode('/', $loadUrl);
		$this->tplDir = $lo[2];
		$this->startCompile = microtime(1);
		$this->sources = $this->cacheTpl[$loadUrl];
	}

	public function setVar($k, $v) 
	{
		$this->vars[$k] = $v;
	}	
	
	
	public function setVarTPL($k, $v) 
	{
		$this->vars_tpl[$k] = $v;
	}

	public function parse() 
	{ 
		global $config, $url, $core, $lang;		
		if($url[0] != ADMIN)
		{	
			$tpl_now =  str_replace("usr/tpl/", '', $this->file_dir);
			$tpl_now = substr($tpl_now, 0, -1);
			$this->sources = preg_replace_callback( "#\\[lang_old:(.+?)]#is", create_function('$matches', 'return constant($matches[1]);'), $this->sources);	
			$this->sources = preg_replace_callback( "#\\[lang:(.+?)\\]#i", array( &$this, 'lang_include'), $this->sources );			
			$this->sources = preg_replace_callback( "#\\{%NOWDATE:(.*?)%\\}#is", create_function('$matches', 'return date($matches[1], time());'), $this->sources );
			$this->sources = preg_replace_callback( "#\\[group=(.+?)](.*?)\\[/group]#is", "Group", $this->sources);
			$this->sources = preg_replace_callback( "#\\[nogroup=(.+?)](.*?)\\[/nogroup]#is", "noGroup", $this->sources);
			$this->sources = preg_replace_callback( "#\\[index:(.+?)\\](.*?)\\[/index\\]#is", "indexShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[modules:(.+?):(.+?)](.*?)\\[/modules]#is", "modulesShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[category:(.+?):(.+?)](.*?)\\[/category]#is", "categoryShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[news:(.+?):(.+?)](.*?)\\[/news]#is", "newsShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[addnews:(.+?)\\](.*?)\\[/addnews\\]#is", "addnewsShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[full:(.+?)\\](.*?)\\[/full\\]#is", "fullnewsShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[content:(.+?):(.+?)](.*?)\\[/content]#is", "contentShow", $this->sources);
			$this->sources = preg_replace_callback( "#\\[guest](.*?)\\[/guest]#is", "checkGuest", $this->sources);
			$this->sources = preg_replace_callback( "#\\[user](.*?)\\[/user]#is", "checkUser", $this->sources);
			$this->sources = preg_replace_callback( "#\\[admin](.*?)\\[/admin]#is", "checkAdmin", $this->sources);
			$this->sources = preg_replace_callback( "#\\[social](.*?)\\[/social]#is", "checkSocial", $this->sources);
			$this->sources = preg_replace_callback( "#\\[captcha](.*?)\\[/captcha]#is", "checkCaptcha", $this->sources);
			$this->sources = preg_replace_callback( "#\\[recaptcha:(.+?)\\](.*?)\\[/recaptcha]#is", "checkReCaptcha", $this->sources);
			$this->sources = preg_replace_callback( "#\\[title:(.*?)]#is", array(&$this, 'preTitle'), $this->sources);
			$this->sources = preg_replace_callback( "#\\[open](.*?)\\[/open]#is", array(&$this, 'preOpen'), $this->sources);
			$this->sources = preg_replace_callback( "#\\[userinfo:(.*?)]#is", array(&$this, 'ustinf'), $this->sources);
			$this->sources = preg_replace_callback( "#\\[custom category=\"(.*?)\" template=\"(.*?)\" aviable=\"(.*?)\" limit=\"(.*?)\" module=\"(.*?)\" order=\"(.*?)\" short=\"(.*?)\" notin=\"(.*?)\"]#is", "buildCustom", $this->sources);
			$this->sources = preg_replace_callback( "#\\[custom category=\"(.*?)\" template=\"(.*?)\" aviable=\"(.*?)\" limit=\"(.*?)\" module=\"(.*?)\" order=\"(.*?)\" short=\"(.*?)\"]#is", "buildCustom", $this->sources);		
			if(file_exists(ROOT . 'usr/other/other.replace.php'))
			{
				include(ROOT . 'usr/other/other.replace.php');
			}	
			if(file_exists(ROOT . 'usr/tpl/'.$tpl_now.'/function.php'))
			{
				include(ROOT . 'usr/tpl/'.$tpl_now.'/function.php');
			}	
		}
		else
		{
			$this->sources = preg_replace_callback( "#\\[alang_old:(.+?)]#is", create_function('$matches', 'return constant($matches[1]);'), $this->sources);			
			$this->sources = preg_replace_callback ( "#\\[(lang)=(.+?)\\](.*?)\\[/lang\\]#is", array( &$this, 'check_lang'), $this->sources ); 
			$this->sources = preg_replace_callback( "#\\[alang:(.+?)\\]#i", array( &$this, 'lang_include'), $this->sources );
		}		
		
		foreach ($this->vars as $k => $v) 
		{
			$in['#{' . $this->sep . $k . $this->sep . '}#i'] = $v;
		}		
		
		if(!empty($in))
		{
			$this->sources = preg_replace(array_keys($in), array_values($in), $this->sources);
		}
		if($url[0] != ADMIN)
		{	
			$this->sources = preg_replace_callback( "#\\{".$this->sep."BLOCKS:(.*?):(.*?)".$this->sep."\\}#is", array(&$this, 'blockParse'), $this->sources);
			$this->sources = preg_replace_callback( "#\\{".$this->sep."TPL:(.*?)".$this->sep."\\}#is", array(&$this, 'loadTPL'), $this->sources);
		}		
	}
	
	function check_lang($matches=array()) 
	{ 
		global $config; 
		if ($config['lang']==$matches[2]) 
			return $matches[3]; 
		else 
			return false; 
	} 
	
	function lang_include($matches=array()) 
	{ 
		global $lang; 
		return $lang[$matches[1]]; 
	}
	
	function preOpen($matches=array())
	{
		$this->open();
		echo stripslashes($matches[1]);
		return $this->close(true);
	}	
	
	function preTitle($matches=array())
	{
		return $this->title(stripslashes($matches[1]), true);
	}
	
	function enUrl($urlGo, $lang)
	{
	global $config;
		if(!eregStrt('http', $urlGo) && !eregStrt('css', $urlGo) && !eregStrt('ico', $urlGo) && !eregStrt('javascript', $urlGo) && !eregStrt($lang.'/', $urlGo) && !empty($urlGo) && $urlGo != '/' && $lang != $config['lang'])
		{
			$urlGo = $lang.'/' . $urlGo;
		}

		if($config['mod_rewrite'] == 1)
		{
			return 'href="'.$urlGo.'"';
		}
		else
		{
			return 'href="index.php?url='.$urlGo.'"';
		}
	}
	
	function ustinf($matches=array())
	{
	global $core;
		if(isset($core->auth->user_info[$matches[1]]))
		{
			return stripslashes($core->auth->user_info[$matches[1]]);
		}
	}

	private function compile() 
	{
		echo $this->sources;
	}

	private function clear() 
	{
		$this->sources = false;
		$this->vars = array();
		$this->file = false;
		$this->startCompile = false;
	}
	
	public function return_end()
	{
		global $core, $lang;
		$tpl_now =  str_replace("usr/tpl/", '', $this->file_dir);
		$tpl_now = substr($tpl_now, 0, -1);
		if(file_exists(ROOT . 'langs/'.$core->lang.'/tpl/'.$core->lang.'.'.$tpl_now.'.lng'))
		{
			include(ROOT . 'langs/'.$core->lang.'/tpl/'.$core->lang.'.'.$tpl_now.'.lng');
		}	
		$this->time_compile += microtime(1)-$this->startCompile;
		$this->parse();
		return $this->sources;
		$this->clear();
	}
		
	public function end() 
	{
		global $core, $lang;
		if(DEBUG) 
		{
			$this->listTemplates[($this->file ? $this->file : 'main')] = MicroTime(1)-$this->startCompile;
		}
		$tpl_now =  str_replace("usr/tpl/", '', $this->file_dir);
		$tpl_now = substr($tpl_now, 0, -1);
		if(file_exists(ROOT . 'langs/'.$core->lang.'/tpl/'.$core->lang.'.'.$tpl_now.'.lng'))
		{
			include(ROOT . 'langs/'.$core->lang.'/tpl/'.$core->lang.'.'.$tpl_now.'.lng');
		}	
		$this->time_compile += microtime(1)-$this->startCompile;
		$this->parse();
		$this->compile();
		$this->clear();
	}
		
	public function head() 
	{
		ob_start();
	}	
		
	public function foot($subContent = false) 
	{
	global $config, $url, $db, $core, $lang;
		$content = ob_get_contents();
		ob_end_clean();		
		$cat_keyword = (isset($this->keywords) ? ', ' .$this->keywords : false);		
		$desc = (isset($this->description) ? $this->description : $config['description']);
		$title_now = html_entity_decode((!empty($this->title) && !empty($_REQUEST['url']) ? $this->title . $config['name'] : $config['name'] . $config['divider'] . $config['slogan']), ENT_QUOTES);
		$meta  =  "<title>" . $title_now . "</title>" . "\n";
		$meta .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=" . $config['charset'] . "\" />" . "\n";
		$meta .= "<meta name=\"keywords\" content=\"" . $config['keywords'] . $cat_keyword . "\" />" . "\n";
		$meta .= "<meta name=\"description\" content=\"" . $desc . "\" />" . "\n";
		$meta .= "<meta name=\"author\" content=\"JMY CMS\" />" . "\n";
		$meta .= "<base href=\"" . $config['url'] . "/\" />" . "\n";
		$meta .= "<meta name=\"robots\" content=\"index, follow\" />" . "\n";
		$meta .= "<meta name=\"generator\" content=\"JMY CMS\" />" . "\n";		
		$meta .= "<link rel=\"alternate\" href=\"" . $config['url'] . "/feed/rss/" . $this->feed_link . "\" type=\"application/rss+xml\" title=\"Rss 2.0\" />" . "\n";
		$meta .= "<link rel=\"search\" type=\"application/opensearchdescription+xml\" href=\"" . $config['url'] . "/feed/opensearch/\"  title=\"" . $config['name'] . "\" />" . "\n";	
		$meta .= "<script src=\"usr/plugins/js/JMY_Ajax.min.js\" type=\"text/javascript\"></script>" . "\n";
		$meta .= "<script src=\"usr/plugins/js/engine.min.js\" type=\"text/javascript\"></script>" . "\n";
		$meta .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\"></script>" . "\n";
		if(!empty($this->players))
		{
			$meta .= $this->players;
		}
		array_unique($this->headerIncludes);
		
		foreach($this->headerIncludes as $metas)
		{
			if($metas)
			{
				$meta .= $metas;
			}		
		}
		$this->loadFile('index');
		if (strpos($this->sources, "<body") !== false) 
		{
			$this->sources = preg_replace('#<body(.*)[^>]#i', '<body\\1<div id="loading" class="loading" style="display:none;top:0;"><img src="media/showloading.gif" alt="Загрузка..." /><br />Загрузка...</div>'.$this->bodyIncludes, $this->sources);
		}
		else
		{
			$meta .= '<div id="loading" class="loading" style="display:none;top:0;"><img src="media/showloading.gif" alt="Загрузка..." /><br />Загрузка...</div>' . "\n";
		}		
		
		if(!empty($this->endJs))
		{
			$this->sources = preg_replace('#</body(.*)[^>]#i', $this->endJs.'</body\\1', $this->sources);
		}
		
				
		if (strpos($this->sources, "{%FULL_AJAX:") !== false && $config['fullajax']) 
		{
			if(preg_match("#({%FULL_AJAX:start%}(.+){%FULL_AJAX:end%})#si", $this->sources, $fullAjax) && $subContent)
			{
				$this->sources = $fullAjax[1];
			}
		}
		
		$this->setVar('META', $meta);
		$this->setVar('MODULE', $content);			
		$this->end();
        unset($this->cacheTpl);
	}
	
	public function loadTPL($matches=array()) 
	{
		global $config, $url, $db, $core;
		$this->loadFile($matches[1]);
		include(ROOT.'boot/vars.class.php');	
		return $this->return_end();
	}
		
	public function open($file = null) 
	{
	global $config, $url;
		if($file)
		{
			$this->uniqTag[] = $file;
		}
		
		ob_start();
	}

	public function close($return = false) 
	{
		$content = ob_get_contents();
		ob_end_clean();
		$this->loadFile('table');
		$this->setVar('CONTENT', $content);
		if($return == false)
			$this->end();
		else
			return $this->return_end();
	}
	
	public function title($text, $return = false) 
	{
		$this->loadFile('title');
		$this->setVar('TITLE', $text);
		if($return == false)
			$this->end();
		else
			return $this->return_end();
	}
	
	public function info($text, $type = 'info', $redicret = null, $title = null, $url_text = null, $url = null, $url_type = 'url', $return = false) 
	{
	global $config, $urll;
	
		$GLOBALS["urll"] = $url;
		switch($url_type) 
		{			
			case 'url':
				$url = "location.href = '".$url."'";
			break;
			case 'modal':
				$url = "modal_o('#".$url."')";
			break;
			
			default:
				
			break;
		}			
		$this->loadFile($type);
		$this->setVar('TEXT', $text);
		$this->setVar('TITLE', $title);
		$this->setVar('URL', $url);
		$this->setVar('URL_TEXT', $url_text);	
		$this->sources = preg_replace_callback("#\\[URL](.*?)\\[/URL]#is", create_function('$matches', 'if(!empty($GLOBALS["urll"])) {return $matches[1];}'),$this->sources);		
		if ($return)
		{
			return $this->return_end();
		}
		else
		{
				$this->end();
		}	
	}
	
	public function alert($type = 'info', $title = null, $text = null, $url = null) 
	{
	global $config, $urll;
		$GLOBALS["urll"] = $url;
		$this->loadFile('alert');
		$this->setVar('TEXT', $text);
		$this->setVar('TYPE', $type);
		$this->setVar('TITLE', $title);
		$this->setVar('URL', $urll);		
		$this->end();
	}
	
	public function redicret($message, $url = 'news', $text = 'Спасибо') 
	{
		global $config;
		$full_url = $url;
		if ($config['redicret'])
		{
			include(ROOT . 'usr/tpl/redirect.tpl');
		}
		else
		{
			location($full_url);
		}
	}
	
	
	public function blockParse($matches=array()) 
	{
	global $db, $config, $core, $url;
		$type = mb_strtolower($matches[1]);
		$file = $matches[2];
		
		switch($type) 
		{
			case 'file':
				$block_path = ROOT . "usr/blocks/{$file}.block.php";
				if (!empty($file) && file_exists($block_path)) 
				{
					ob_start();
					include_once($block_path);
					$contetn_block = ob_get_contents();
					ob_end_clean();
					return $contetn_block;
				} 
				else 
				{
					return _BLOCK_EMPTY;
				}
				break;
				
			case 'type':
				if (empty($file)) return false;
				$block_content = null;
				

				$sideDe = !empty($this->modules[$url[0]]) ? explode(',', $this->modules[$url[0]]['unshow']) : '';				
				$sideDe = !empty($this->modules[$url[0]]) ? explode(',', $this->modules[$url[0]]['unshow']) : '';				

				foreach ($this->blocks as $array) 
				{
					if($this->blockShow($array['showin'], $array['unshow'], $array['groups']))
					{
						if ($array['type'] == $file && (empty($sideDe) || !in_array($file, $sideDe))) 
						{
							$block_path = ROOT . "usr/blocks/" . $array['file'];
							if (!empty($array['file']) && file_exists($block_path)) 
							{
								ob_start();
								require($block_path);
								$contetn_block = ob_get_contents();
								ob_end_clean();
								$this->uniqTag = array($array['id'], $array['file'], $array['type']);
							} 
							elseif (empty($array['file']) && !empty($array['content'])) 
							{
								$contetn_block = $core->bbDecode($array['content']);
								$this->uniqTag = array($array['id'], $array['type']);
							}
							else $contetn_block = '<center><b>' . _EMPTY_CONTENT . '</b></center>';						
							ob_start();
							$this->loadFile('block');
							$this->setVar('TITLE', $array['title']);
							$this->setVar('EDIT', $edit);
							$this->setVar('CONTENT', $contetn_block);
							$this->end();
							$block_content .= ob_get_contents();
							ob_end_clean();
							unset($contetn_block);
						}
					}
				}
				return $block_content;
				
				break;
				
			case 'id':
				if (empty($file)) return false;
				$block_content = null;
				
				if(isset($this->blocks[$file]))
				{
					$array = $this->blocks[$file];
					
					$block_path = ROOT . "usr/blocks/" . $array['file'];
					
					if (!empty($array['file']) && empty($array['content']) && file_exists($block_path)) 
					{
						ob_start();
						require($block_path);
						$contetn_block = ob_get_contents();
						ob_end_clean();
					} 
					elseif (empty($array['file']) && !empty($array['content'])) 
					{
						$contetn_block = $core->bbDecode($array['content']);
					} 
					else $contetn_block = '<center><b>Нет содержания!</b></center>';
					
					return $contetn_block;
					unset($contetn_block);
				}
				break;
			
			default:

			break;
		}
	}
	
	private function blockShow($mods, $unmods, $groups, $free = false)
	{
	global $url, $core;
	
		if(eregStrt('_free', $mods) && $free == false) return false;
		
		$modNow = $url[0];
		$modsArr = explode(',', $mods);
		$unModsArr = explode(',', $unmods);
		$groupsArr = explode(',', $groups);
		$groupAccess = false;
		
		if((is_array($groupsArr) && in_array($core->auth->user_info['group'], $groupsArr)) OR $groups == '' OR $core->auth->isAdmin)
		{
			$groupAccess = true;
		}
		
		if(is_array($unModsArr) && in_array($modNow, $unModsArr))
		{
			return false;
		}
		elseif((is_array($modsArr) && in_array($modNow, $modsArr)) && INDEX == false)
		{
			return $groupAccess;
		}
		elseif(eregStrt('_index', $mods))
		{
			if(INDEX == true)
			{
				return $groupAccess;
			}
			else
			{
				return false; 
			}
		}
		elseif(eregStrt('_all', $mods))
		{
			return $groupAccess;
		}
	}

	public function pages($page, $num, $all, $link, $onClick = false, $return = false) 
	{
		global $config, $nums, $url;
		if(!eregStrt('{page}', $link)) $link = $link . '/{page}';
		$numpages = ceil($all/$num);
		
		$predel = 4;
		$prevpage = $page-1;
		for ($var = 1; $var < $numpages+1; $var++) 
		{
			if ($var == $page) 
			{
				$nums .= '<span class="pages">' . $var . '</span>';
			} 
			else 
			{
				if ((($var > ($page - $predel)) && ($var < ($page + $predel))) or ($var == $numpages) || ($var == 1)) 
				{
					$nums .= ' <a href="' . str_replace('{page}', 'page/'.$var, $link) . '" ' . ($onClick ? str_replace('{num}', $var, $onClick) : '') . '><b>' . $var . '</b></a> ';
				} 
				
				if ($var < $numpages) 
				{
					if (($var > ($page - $predel-2)) && ($var < ($page + $predel))) $nums .= " ";
					if (($page > $predel+2) && ($var == 1)) $nums .= " ... ";
					if (($page < ($numpages - $predel)) && ($var == ($numpages - 2))) $nums .= "... ";
				}
			}
		}
		$nextpage = $page + 1;
		if($numpages != 1 && $numpages != 0) 
		{
		
			$this->loadFile('pages');
			$this->setVar('NUM', $nums);
			$this->sources = preg_replace( "#\\{" . $this->sep . "NEXT" . $this->sep . "\\}(.*?)\\{/" . $this->sep . "NEXT" . $this->sep . "\\}#ies", ($page < $var-1) ? "pageLink('".$link."', '\\1', $nextpage" . ($onClick ? ", '" . str_replace("'", "\'", $onClick) . "'" : '') . ")" : '', $this->sources);
			$this->sources = preg_replace( "#\\{" . $this->sep . "PREV" . $this->sep . "\\}(.*?)\\{/" . $this->sep . "PREV" . $this->sep . "\\}#ies", ($page != 1) ? "pageLink('".$link."', '\\1', $prevpage" . ($onClick ? ", '" . str_replace("'", "\'", $onClick) . "'" : '') . ")" : '', $this->sources);
			if ($return)
			{
				return $this->return_end();
			}
			else
			{
				$this->end();
			}			
			$nums = '';
		}
	}
}