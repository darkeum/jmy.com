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

loadConfig('sitemap');
global $lang;
switch(isset($url[3]) ? $url[3] : null) 
{
	default:
		$adminTpl->admin_head($lang['modules'] .' | '. $lang['sitemap']);
		echo '<div id="content" class="animated fadeIn">';
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_sitemap ORDER BY id ASC");
		if($db->numRows($query) > 0) 
		{
			echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">' . $lang['sitemap'] . ':</span>                
              </div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/cats/action">
                  <table class="table table-striped ">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th>' . $lang['title'] . '</th>
							<th>' . $lang['sitemap_update'] . '</th>
							<th>' . $lang['sitemap_rang'] .'</th>
							<th>' . $lang['url'] . '</th>						
						</tr>
                    </thead>
                    <tbody>';		
					while ($result = $db->getRow($query)) 
					{
						echo '<tr>				
						<td><span class="pd-l-sm"></span>' . $result['id'] . '</td>
						<td>' . $result['name'] . '</td>				
						<td>' . $sitemap_conf['change'] . '</td>
						<td>'. $sitemap_conf['priority'] . '</td>	
						<td>'. $result['url'] . '</td>				
						</tr>';
					}		
			echo '	</tbody>				
					</table> 
				</form>				
			  </div>
			</div>';
		}
		else
		{
			$adminTpl->info($lang['sitemap_empty'], 'empty', null, $lang['sitemap'], $lang['sitemap_gen'], ADMIN.'/module/sitemap/create');	
		}
		echo' </div>';	
		$adminTpl->admin_foot();
		break;

	case 'create':
		global $core, $config;
		$adminTpl->admin_head($lang['modules'] .' | '. $lang['sitemap'].' | '. $lang['sitemap_gens']);
		echo '<div id="content" class="animated fadeIn">';
		$db->query("TRUNCATE TABLE " . DB_PREFIX . "_sitemap");
		$db->query("INSERT INTO `" . DB_PREFIX . "_sitemap` ( `name` , `url`) VALUES ('". $lang['sitemap_main'] . "', '".$config['url']."/');");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_plugins WHERE service='modules' ORDER BY title ASC");
		$exceMods = array('feed', 'pm', 'profile', 'search', 'poll', 'mainpage');
		if($db->numRows($query) > 0) 
		{
			while($mod = $db->getRow($query)) 
			{
				if(!in_array($mod['title'], $exceMods))
				{				
					if ($mod['active']==1) 
					{
						$file = ROOT.'usr/modules/'.$mod['title'].'/sitemap.php';
						$db->query("INSERT INTO `" . DB_PREFIX . "_sitemap` ( `name` , `url`) VALUES ('". $mod['content']. "', '".$config['url']."/".$mod['title']."');");
						if (file_exists($file))
						{							
							include($file);	
						}
					}
				}				
			}
		}
		$sitemapXML='<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">
		<!-- Last update of sitemap '.date("Y-m-d H:i:s+06:00").' -->';
		$sitemapTXT=NULL;
		$query_sm = $db->query("SELECT * FROM ".DB_PREFIX."_sitemap ORDER BY id ASC");	
		$ic=$db->numRows($query_sm);
		if($db->numRows($query_sm) > 0) 
		{	
			while($sm = $db->getRow($query_sm)) 
			{
					$sitemapXML.="\r\n<url><loc>{$sm['url']}</loc><changefreq>{$sitemap_conf['change']}</changefreq><priority>{$sitemap_conf['priority']}</priority></url>";
					$sitemapTXT.="\r\n".$sm['url'].' '.$sitemap_conf['change'].' '.$sitemap_conf['priority'];
			}
			$flag=true;
		}		
		$sitemapXML.="\r\n</urlset>";		
		$fp=fopen('files/sitemap.txt','w+');if(!fwrite($fp,$sitemapTXT)){$flag=false;}fclose($fp);
		$fp=fopen('files/sitemap.xml','w+');if(!fwrite($fp,$sitemapXML)){$flag=false;}fclose($fp);	
		if ($flag==true)
		{
			$adminTpl->info(str_replace('[numb]', $ic, $lang['sitemap_gen_ok']), 'info', null, $lang['info'], $lang['sitemap'], ADMIN.'/module/sitemap');	
		}
		else
		{
			$adminTpl->info($lang['sitemap_gen_error'], 'error', null, $lang['error'], $lang['sitemap'], ADMIN.'/module/sitemap');	
		}
		echo' </div>';	
		$adminTpl->admin_foot();		
		break;
		
	case 'update':
		global $core, $config;
		$adminTpl->admin_head($lang['modules'] .' | '. $lang['sitemap'] .' | '. $lang['sitemap_searchs']);	
		echo '<div id="content" class="animated fadeIn">';		
		$url_map=$config['url'].'/sitemap.xml';
		$content_map = '';			
		if (strpos ( send_url("http://google.com/webmasters/sitemaps/ping?sitemap=", $url_map), "successfully added" ) !== false) 
		{
			$content_map .='Google: '.$lang['sitemap_send_ok'].'<br />';
		} 
		else
		{
			$content_map .='Google: <a href="http://google.com/webmasters/sitemaps/ping?sitemap='.urlencode($url_map).'">'.$lang['sitemap_send_error'].'</a><br />';
		}
		if (strpos ( send_url("http://ping.blogs.yandex.ru/ping?sitemap=", $url_map), "OK" ) !== false) 
		{
			$content_map .='Яндекс: '.$lang['sitemap_send_ok'].'<br />';
		} 
		else
		{
			$content_map .='Яндекс: <a href="http://ping.blogs.yandex.ru/ping?sitemap='.urlencode($url_map).'">'.$lang['sitemap_send_error'].'</a><br />';
		}
		if (strpos ( send_url("http://rpc.weblogs.com/pingSiteForm?name=InfraBlog&url=", $url_map), "Thanks for the ping" ) !== false) 
		{
			$content_map .='Weblogs: '.$lang['sitemap_send_ok'].'<br />';
		} 
		else
		{
			$content_map .='Weblogs: <a href="http://rpc.weblogs.com/pingSiteForm?name=InfraBlog&url='.urlencode($url_map).'">'.$lang['sitemap_send_error'].'</a><br />';
		}		
		if (strpos ( send_url("http://www.bing.com/webmaster/ping.aspx?siteMap=", $url_map), "http://www.bing.com/ping?sitemap=" ) == false) 
		{
			$content_map .='Bing: '.$lang['sitemap_send_ok'].'<br />';
		} 
		else
		{
			$content_map .='Bing: <a href="http://rpc.weblogs.com/pingSiteForm?name=InfraBlog&url='.urlencode($url_map).'">'.$lang['sitemap_send_error'].'</a><br />';
		}
		$adminTpl->info($content_map, 'info', null, $lang['info'], $lang['sitemap'], ADMIN.'/module/sitemap');
		echo' </div>';			
		$adminTpl->admin_foot();
		break;			
		
	case 'config':		
		$configBox = array(
			'sitemap' => array(
				'varName' => 'sitemap_conf',
				'title' => $lang['sitemap_config'],
				'groups' => array(
					'main' => array(
						'title' => $lang['config_main'],
						'vars' => array(											
							'priority' => array(
								'title' => $lang['sitemap_config_rang'],
								'description' => $lang['sitemap_config_rang_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'change' => array(
								'title' => $lang['sitemap_config_update'],
								'description' => $lang['sitemap_config_update_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
						)
					),
					'seo' => array(
						'title' => $lang['seo'],
						'vars' => array(		
							'keywords' => array(
								'title' => $lang['seo_keywords'],
								'description' => $lang['seo_settings'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),		
							'description' => array(
								'title' => $lang['seo_description'],
								'description' => $lang['seo_settings'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
						)
					),
				),
			),
		);
		$ok = false;		
		if(isset($_POST['conf_file']))
		{
			$ok = true;
		}		
		generateConfig($configBox, 'sitemap', '{MOD_LINK}/config', $ok);
		break;
		
}

	function send_url($url, $sitemap)
	{		
		$data = false;
		$file = $url.urlencode($sitemap);		
		if(function_exists('curl_init'))
		{			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $file);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6 );			
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		} 
		else 
		{
			return @file_get_contents($file);
		}	
	}

?>