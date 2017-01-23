<?php

/**
* @name        JMY CORE
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
class admin extends template
{
	var $page_nav = '';
	
	function __construct()
	{
		$this->adminTheme = true;
		$this->file_dir = 'usr/tpl/admin/';
		$this->toConf = '';
	}
    
    public function loadFile($file, $check = false) 
    {
        $this->sources = file_get_contents(ROOT . $this->file_dir . $file . '.tpl');
    }	

	public function admin_head($title = null) 
	{
		$this->admin_title = $title;
		$this->sep = '';
		ob_start();
	}
		
	public function admin_foot($last_visit = null, $last_ip = null) 
	{
		global $config, $url, $db, $core, $errorClass;
		$content = ob_get_contents();
		ob_end_clean();
		$array = explode(' | ', $this->admin_title);
		$title_massiv = array_reverse($array);
		$admtitle = null;
		
		foreach($title_massiv as $title) 
		{
			if($title) $admtitle .= filter($title) . $config['divider'];
		}

		$module_array = array();
		require ROOT . 'root/list.php';		
		foreach(glob(ROOT.'usr/modules/*/admin/list.php') as $listed) 
		{
			include($listed);
		}
				
		if(isset($toconfig)) $this->toConf = $toconfig;	
		$subNav = '';		
		$meta = "<title>" . (!empty($admtitle) && !empty($_REQUEST['url']) ? $admtitle : $config['slogan']) . " "._APANEL."</title>" . "\n";
		$meta .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=" . $config['charset'] . "\">" . "\n";
		$meta .= "<base href=\"" . $config['url'] . "/\">" . "\n";					
		$meta .= "<script src=\"usr/plugins/js/ajax_admin.js\" type=\"text/javascript\"></script>" . "\n";	
		$meta .= "<script src=\"usr/plugins/js/JMY_Ajax.js\" type=\"text/javascript\"></script>" . "\n";	
		$meta .= "<script src=\"usr/plugins/js/engine.js\" type=\"text/javascript\"></script>" . "\n";
		$meta .= "<script src=\"usr/plugins/js/bb_editor.js\" type=\"text/javascript\"></script>" . "\n";
		$meta .= "<script src=\"langs/".$core->InitLang()."/js/system.js\" type=\"text/javascript\"></script>" . "\n";
	
		if (isset($this->headerIncludes))
		{
			$meta .=ArrayToStr($this->headerIncludes);
		}
		
		if(isset($url[1]))
		{
			if(isset($component_array[$url[1]]))			
			{
			$subNav = '<p class="lead">'.$component_array[$url[1]]['name'].'</p>';
				if(isset($component_array[$url[1]]['subAct']))
				{				
					$subNav .= '<ul class="nav nav-list nav-list-topbar">';
					          
				
					foreach($component_array[$url[1]]['subAct'] as $comAct => $comActLink)
					{
						$subNav .= ' <li class=" ' . ((isset($url[2]) && $url[2] == $comActLink OR !isset($url[2]) && $comActLink == '') ? 'active' : '') . '"><a href="' . ADMIN . '/' . $url[1] . '/' . $comActLink . '">' . $comAct . '</a> </li>';
					}
					$subNav .= '</ul>';
					
				}
				
				$noSub = '<span class="navMainActive"><a href="' . ADMIN . '/' . $url[1] . '">' . $component_array[$url[1]]['name'] . '</a></span>';		
		
			
			}
			elseif(isset($url[2]) && isset($module_array[$url[2]]))			
			{		
				$subNav = '<p class="lead">'.$module_array[$url[2]]['name'].'</p>';
				if(isset($module_array[$url[2]]['subAct']))
				{				
					$subNav .= '<ul class="nav nav-list nav-list-topbar">';
					          
				
					foreach($module_array[$url[2]]['subAct'] as $comAct => $comActLink)
					{
						$subNav .= ' <li class=" ' . ((isset($url[3]) && $url[3] == $comActLink OR !isset($url[3]) && $comActLink == '') ? 'active' : '') . '"><a href="' . ADMIN . '/module/' . $url[2] . '/' . $comActLink . '">' . $comAct . '</a> </li>';
					}
					$subNav .= '</ul>';
					
				}				
				$noSub = '<span class="navMainActive"><a href="' . ADMIN . '/module/' . $url[2] . '">' . $module_array[$url[2]]['name'] . '</a></span>';
			}
			elseif(isset($services_array[$url[1]]))			
			{
				$subNav = '<p class="lead">'.$services_array[$url[1]]['name'].'</p>';
				if(isset($services_array[$url[1]]['subAct']))
				{				
					$subNav .= '<ul class="nav nav-list nav-list-topbar">';
					          
				
					foreach($services_array[$url[1]]['subAct'] as $comAct => $comActLink)
					{
						$subNav .= ' <li class=" ' . ((isset($url[2]) && $url[2] == $comActLink OR !isset($url[2]) && $comActLink == '') ? 'active' : '') . '"><a href="' . ADMIN .'/'. $url[1] . '/' . $comActLink . '">' . $comAct . '</a> </li>';
					}
					$subNav .= '</ul>';
					
				}				
				$noSub = '<span class="navMainActive"><a href="' . ADMIN . '/' . $url[1] . '">' . $services_array[$url[1]]['name'] . '</a></span>';			
			}
		}
		
		$topbar_url = '';
		if(isset($url[1]))
		{
			if(isset($topbar_array[$url[1]]))			
			{		
				$topbar_url = $url[1];
			
			}
			elseif(isset($url[2]) && isset($topbar_array[$url[2]]))			
			{	
				$topbar_url = $url[2];			
			}	
			if (!empty($topbar_url))
			{
				if(isset($topbar_array[$topbar_url]['subAct']))
				{
					$topbar = '<div class="mt-30 hidden-xs">';
					foreach($topbar_array[$topbar_url]['subAct'] as $comAct => $comActLink)
					{
						if (is_array($comActLink))
						{
							if ($comActLink[2] == 'ajax')
							{
								$topbar .= '<a onclick="ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init(); '.$comActLink[3].'\',);" class="btn btn-default btn-sm fw600 ml10"><span class="'.$comActLink[1].' pr5"></span>'.$comAct.'</a>';
							}
							else
							{
								$topbar .= '<a href="'.$comActLink[2].'" class="btn btn-default btn-sm fw600 ml10"><span class="'.$comActLink[1].' pr5"></span>'.$comAct.'</a>';
							}
						}
						else
						{
							$topbar .= '<a href="'.$comActLink.'" class="btn btn-default btn-sm fw600 ml10"><span class="pr5"></span>'.$comAct.'</a>';
						}
					}
					echo '</div>';
				}
			}
		}
		
		$menu = '';
		
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_plugins WHERE menu = '1' AND active='1' ");
		if($db->numRows($query) > 0)
		{	
			while($module_menu = $db->getRow($query)) 
			{	
				$menu_url = $module_menu['title'];			
				if (!empty($menu_url))
				{
					
					if(isset($menu_array[$menu_url]['subAct']))
					{
						$menu .= '<li>
								<a href="#" class="accordion-toggle '.openMenu($menu_url).'">
									<span class="'.$menu_array[$menu_url]['icon'].'"></span>
									<span class="sidebar-title">'.$menu_array[$menu_url]['name'].'</span>
									<span class="caret"></span>
								</a>
								<ul class="nav sub-nav">';						
						foreach($menu_array[$menu_url]['subAct'] as $comAct => $comActLink)
						{
							if (is_array($comActLink))
							{							
								
									$menu .= '<li '.chooseModuleMenu($comActLink[0], $menu_url).'>
												<a href="/{ADMIN}/module/'.$menu_url.'/'.$comActLink[0].'"><span class="'.$comActLink[1].'"></span>'.$comAct.'</a>
											</li>';
								
							}
							else
							{
								$menu .= '<li '.chooseModuleMenu($comActLink, $menu_url).'>
												<a href="/{ADMIN}/module/'.$menu_url.'/'.$comActLink.'"><span class="fa-square-o"></span>'.$comAct.'</a>
											</li>';
							}
						}
						
					$menu .= '</ul></li>';	
					}
					else
					{
						$menu .= '<li '.chooseMenu($menu_url).'>
								<a href="'.ADMIN.'/module/'.$menu_url.'">
									<span class="'.$menu_array[$menu_url]['icon'].'"></span>
									<span class="sidebar-title">'.$menu_array[$menu_url]['name'].'</span>								
								</a>
							  </li>';
					}
				}
			}
		}
		
		$i_n=0;
		$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_news WHERE active=2'));
		
		if(file_exists(ROOT . 'install.php'))
		{
			$notifications = '<li class="list-group-item"><span class="pull-left mg-t-xs mg-r-md"><img src="usr/tpl/admin/assets/images/notifications/install.png" class="avatar avatar-sm img-circle" alt=""></span><div class="m-body show pd-t-xs"><span>' . _INSTALLEX . '</span><br></div></li>';
			$i_n++;
		}
		if($count != 0)
		{
			$notifications .=  '<li style="cursor:pointer" onclick="location.href=\'/' . ADMIN . '/publications/mod/news\';" class="list-group-item"><span class="pull-left mg-t-xs mg-r-md"><img src="usr/tpl/admin/assets/images/notifications/nn.png" class="avatar avatar-sm img-circle" alt=""></span><div class="m-body show pd-t-xs"><span>'._ONMODER.' ('.$count.') </span><br></div></li>';;
			$i_n++;
		}
		if (file_exists('tmp/update/lock.update'))
		{
			$notifications .=  '<li style="cursor:pointer" onclick="location.href=\'/' . ADMIN . '/update\';" class="list-group-item"><span class="pull-left mg-t-xs mg-r-md"><img src="usr/tpl/admin/assets/images/notifications/update.png" class="avatar avatar-sm img-circle" alt=""></span><div class="m-body show pd-t-xs"><span>'. _UPDATE_JMY .'</span><br></div></li>';;
			$i_n++;
		}
		else
		{	
			if (file_exists('tmp/update/time.dat'))
			{ 
				$file_array = file("tmp/update/time.dat");
				if(date("Ymd") <> $file_array[0])
				{
					$file = fopen ("tmp/update/time.dat","w+");
					fputs ( $file, date("Ymd"));
					fclose ($file);						
					$now = file_get_contents('http://server.jmy.su/index.php?check_version');
					$version = VERSION_ID;			
					if ($version<>$now)
					{
						fopen('tmp/update/lock.update', 'w');
					}
				}			
			}
			else
			{
				fopen('tmp/update/time.dat', 'w');
			}
		}
		
		if ($i_n==0)
		{
			$i_n='';
			$notifications='<div class="panel-footer no-border">'._NOT_NOTIF.'</div>';		
		}
		$newFriends = $db->query("SELECT u.id as uuid, u.nick, u.last_visit, u.regdate, f.* FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_user_friends` as f on(u.id = f.who_invite OR u.id = f.whom_invite) WHERE (f.who_invite = '" . $core->auth->user_info['id'] . "' OR f.whom_invite = '" . $core->auth->user_info['id'] . "') AND u.id != '" . $core->auth->user_info['id'] . "' AND f.confirmed = '0'");
		
		$newMessages = $db->query("SELECT id FROM `" . USER_PREFIX . "_pm` WHERE toid = '" . $core->auth->user_info['id'] . "' AND status = '0'");
		$config['hide'] = 1;
		$avatar = avatar($core->auth->user_info['id']);
		$this->loadFile('main');
		$this->setVar('META', $meta);
		$this->setVar('FOOT', isset($this->footIncludes) ? ArrayToStr($this->footIncludes) : '');
		$this->setVar('BODY_CLASS', isset($this->body_class) ? ArrayToStr($this->body_class) : '');
		$this->setVar('LANG', $core->InitLang());
		$this->setVar('AVATAR', $avatar);
		$this->setVar('ADM_THEME', 'usr/tpl/admin');
		$this->setVar('NOTIF', $notifications);
		$this->setVar('NOTIF_NUMB', $i_n);
		$this->setVar('MESSAGE_NUMB', $i_n);
		$this->setVar('URL', $config['url']);
		$this->setVar('URL_FULL', $core->fullURL());
		$this->setVar('MODULE', $content);
		$this->setVar('MENU_MODULES', $menu);
		$this->setVar('VERSION', VERSION_ID);
		$this->setVar('NAME', $core->auth->user_info['nick']);
		$this->setVar('IP', getenv("REMOTE_ADDR"));
		$this->setVar('ADMIN', ADMIN);		
		$this->setVar('LICENSE', 'Powered by <a href="http://jmy.su" target="_blank" title="JMY CORE">JMY CORE</a>');
		$this->setVar('SUBNAV', isset($subNav) ? $subNav : $noSub);
		$this->setVar('MOD_LINK', (isset($url[2]) && $url[1] == 'module') ? ADMIN . '/module/' . $url[2] : false);
		$this->setVar('GENERATE', mb_substr(microtime(1) - TIMER, 0, 5));
		$this->setVar('GZIP', $config['gzip'] ? 'GZIP Включён' : '');
		$this->setVar('TIMEQUERIES', mb_substr($db->timeQueries, 0, 5));
		$this->setVar('QUERIES', $db->numQueries);
		$this->setVar('JS_CODE', isset($this->js_code) ? ArrayToStr($this->js_code) : '');
		$this->setVar('pages', $this->page_nav);
		$this->setVar('HIDE_STATUS_1', ($config['hide'] == 1) ? 'selected="selected"' : '');
		$this->setVar('HIDE_STATUS_2', ($config['hide'] == 2) ? 'selected="selected"' : '');
		$this->setVar('HIDE_STATUS_3', ($config['hide'] == 3) ? 'selected="selected"' : '');		
		$this->setVar('MESSAGES_NUMB', $db->numRows($newMessages));
		$this->setVar('FRIENDS_NUMB', $db->numRows($newFriends));
		$this->setVar('TOPBAR', isset($topbar) ? $topbar : '');
		$this->sources = preg_replace_callback("#\\{MENU_OPEN:(.*?)\\}#is", "openMenu" , $this->sources);
		$this->sources = preg_replace_callback("#\\{MENU_CHOOSE:(.*?)\\}#is", "chooseMenu",$this->sources);
		$this->sources = preg_replace_callback("#\\[CHECK_ACTIVE](.*?)\\[/CHECK_ACTIVE]#is", "checkActive",$this->sources);		
		$this->sources = preg_replace_callback("#\\[ACTIVE_MODULE:(.+?)](.*?)\\[/ACTIVE_MODULE]#is", "activeModule",$this->sources);	
			
		$this->end();
		
		
	}
	
	
	
	public function blockCookie($block, $type = 'block')
	{
		if($type == 'block')
		{
			if(isset($_COOKIE['Block_'.$block]) && $_COOKIE['Block_'.$block] == true) return 'style="display:none"';
		}
		else
		{
			if(isset($_COOKIE['Block_'.$block]) && $_COOKIE['Block_'.$block] == true) return 'close'; else return 'open';
		}
	}
	
	public function a_pages($page, $num, $all, $link, $onClick = false) 
	{
		global $config, $url;
		if(!eregStrt('{page}', $link)) $link = $link . '/{page}';
		$numpages = ceil($all/$num);
		$nums = '';
		$predel = 4;
		$prevpage = $page-1;
		if($prevpage != 0) $nums .= '<li><a href="' . str_replace('{page}', 'page/'.$prevpage, $link) . '" title="' . $prevpage . '" ' . ($onClick ? str_replace('{num}', $prevpage, $onClick) : '') . '>&lt; '._BACK.'</a></li>';
		for ($var = 1; $var < $numpages+1; $var++) 
		{
			if ($var == $page) 
			{
				$nums .= '<li><a href="' . str_replace('{page}', 'page/'.$var, $link) . '" title="' . $var . '" ' . ($onClick ? str_replace('{num}', $var, $onClick) : '') . ' class="current">' . $var . '</a></li>';
			} 
			else 
			{
				if ((($var > ($page - $predel)) && ($var < ($page + $predel))) or ($var == $numpages) || ($var == 1)) 
				{
					$nums .= '<li><a href="' . str_replace('{page}', 'page/'.$var, $link) . '" title="' . $var . '" ' . ($onClick ? str_replace('{num}', $var, $onClick) : '') . '>' . $var . '</a></li>';
				} 
				
				if ($var < $numpages) 
				{
					if (($var > ($page - $predel-2)) && ($var < ($page + $predel))) $nums .= "";
					if (($page > $predel+2) && ($var == 1)) $nums .= "<li class=\"dots\">...</li>";
					if (($page < ($numpages - $predel)) && ($var == ($numpages - 2))) $nums .= "<li class=\"dots\">...</li>";
				}
			}
		}
		$nextpage = $page + 1;
		if($numpages != $page) $nums .= '<li><a href="' . str_replace('{page}', 'page/'.$nextpage, $link) . '" title="' . $nextpage . '" ' . ($onClick ? str_replace('{num}', $nextpage, $onClick) : '') . '>'._FORWARD.' &gt;</a></li>';
		
		if($numpages != 1 && $numpages != 0) 
		{			
			$nums .= '</ul>';
			$this->page_nav = $nums;
		}
	}
}

$adminTpl = new admin;