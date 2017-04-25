<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
 
if (!defined('ACCESS') && !$core->auth->isAdmin && $url[0] !== ADMIN) {
    header('Location: /');
	exit;
}

if (MODE != 'default' && file_exists(ROOT . 'root/index.'.MODE.'.php')) 
{
	include(ROOT . 'root/index.'.MODE.'.php');
}
else
{
	define('ADMIN_ACCESS', true);
	define('COOKIE_VISIT', md5(getenv("REMOTE_ADDR")) . '-admin_visit');
	define('SESS_AUTH', md5(getenv("REMOTE_ADDR")) . '-auth');
	define('SESS_COUNT', md5(getenv("REMOTE_ADDR")) . '-counter');
	session_start();
	require ROOT . 'etc/admin.config.php';
	require ROOT . 'root/functions.php';
	require ROOT . 'root/ajax_funcs.php';
	require ROOT . 'root/admin_tpl.class.php';
	$core->loadLangFile('root/langs/{lang}.navigation.php');
	if(!empty($admin_conf['ipaccess']))
	{
		$IPs_arr = explode("\n", $admin_conf['ipaccess']);
		$parse_ip = @ip2long(getRealIpAddr()); 
		foreach($IPs_arr as $IPs) 
		{ 
			$IPs = explode('|', $IPs);
			if(count($IPs) == 2)
			{
				if($parse_ip <= @ip2long($IPs[0]) && $parse_ip <= @ip2long($IPs[1]))
				{
					$_SESSION[SESS_AUTH] = null;
					$_SESSION[SESS_COUNT] = 0;
					setcookie(COOKIE_AUTH, '', time(), '/');
					setcookie(COOKIE_PAUSE, '', time(), '/');
					location();
				}
			}
		}
	}
	function admin_main() 
	{
		global $adminTpl, $db, $core, $config, $lang;
		$last_visit = time();
		$last_ip = $_SERVER['REMOTE_ADDR'];
		$query = '';
		file_get_contents('https://server.jmy.su/stats.php?url='.$config['url'].'&v='.VERSION_ID.'&email='.$config['support_mail']);
		if(!isset($_COOKIE[COOKIE_VISIT]) && !isset($_SESSION[SESS_AUTH])) 
		{
			if($db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR']) . "', '" . $core->auth->user_id . "', '" . str_replace('[nick]', $core->auth->user_info['nick'], $lang['logs_good_login']) . "', '1')")) 
			{
				setcookie(COOKIE_VISIT, time(), time() + 86400, '/');
			}		
			$last_visit = time();
			$last_ip = $_SERVER['REMOTE_ADDR'];
		}
		$adminTpl->admin_head($lang['control_panel']);  
		echo '
		<section id="content" class="table-layout animated fadeIn">
		<div class="tray tray-center">
				<div class="tray-inner">
				  <div class="row flex-column-reverse-before-md">
					<div class="col-sm-12">
					  <div class="p30">
						<!-- dashboard tiles-->
						<h2 class="ib mn mr20">'.$lang['stats'].'</h2>
						<div class="row text-center mt35">
						  <div class="col-sm-6 col-md-3 br-lg-r">
							<h2 class="mn fs47 ib pr20 monserrat">'.$core->sum_row('news').'</h2>
							<div class="reveal-xlg-inline-block text-center text-xlg-left">
							  <p class="fs15 text-shady-lady mb2">'.$lang['stats_news'].'</p>
							</div>
						  </div>
						  <div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-sm-0">
							<h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('users').'</h2>
							<div class="reveal-xlg-inline-block text-center text-xlg-left">
							  <p class="fs15 text-shady-lady mb2">'.$lang['stats_user'].'</p>
							</div>
						  </div>
						  <div class="clearfix visible-sm-block"></div>
						  <div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-md-0">
							<h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('content').'</h2>
							<div class="reveal-xlg-inline-block text-center text-xlg-left">
							  <p class="fs15 text-shady-lady mb2">'.$lang['stats_page'].'</p>
							</div>
						  </div>
						  <div class="col-sm-6 col-md-3 mt-30 mt-md-0">
							<h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('board_threads').'</h2>
							<div class="reveal-xlg-inline-block text-center text-xlg-left">
							  <p class="fs15 text-shady-lady mb2">'.$lang['stats_forum'].'</p>
							</div>
						  </div>
						</div>
					  </div>
					</div>
					<div class="col-sm-12">
					  <hr class="mt-35">
					</div>                
				  </div>
				  <div class="row text-center text-sm-left">
					<div class="col-lg-6 admin-grid">
					  <div class="pl30 pr30">
						<h3 class="mtn">'.$lang['main_last_comment'].':</h3>				
							<div id="last_comm">
								<div class="panel-heading" >'.$lang['ajax_load'].'</div>
								<script type="text/javascript">ajaxGet(\'' . ADMIN . '/ajax/last_comm\', \'last_comm\');</script>	
							</div> 				 
					  </div>
					</div>
					<div class="p-md-11 col-lg-6 admin-grid mt-60 mt-lg-0">
					  <div class="pl30 pr30">
						<h3 class="mtn">'.$lang['main_last_user'].':</h3>
							<div id="last_user">
								 <div class="panel-heading" >'.$lang['ajax_load'].'</div>
									<script type="text/javascript">ajaxGet(\'' . ADMIN . '/ajax/last_user\', \'last_user\');</script>	
								</div>   
							</div>
						</div>
					</div>
				  <hr>              
				  <div class="row">
					<div class="col-md-12 admin-grid">
					  <div class="panel">
						<div class="panel-body">
						  <div class="row">
							<div class="col-md-6">
							  <div class="pl30 pr50">
								<h3 class="ib mn mr20">'.$lang['main_server'].':</h3>
								<div class="pull-right">
								  <div class="btn-group">';
								   $notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
								   echo '<button onclick="ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init(); '.$notif.'\', \'server_stats\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>'.$lang['refresh'].'</button>
								  </div>
								</div>
								<div id="server_stats" class="mt50 row text-center">
								 <div class="panel-heading" >'.$lang['ajax_load'].'</div>
									<script type="text/javascript">ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init();\', \'server_stats\');</script>	
								</div>
							  </div>
							</div>
							<div class="col-md-6">
							  <div class="pl30 pr30">
								<h2 class="ib mn mr20">'.$lang['main_last_action'].':</h2>
								<div id="last_action" class="panel-scroller scroller-sm scroller-thick scroller-blue scroller-pn pn mt30">
								 <div class="panel-heading" >'.$lang['ajax_load'].'</div>
									<script type="text/javascript">ajaxGet(\'' . ADMIN . '/ajax/last_action\', \'last_action\');</script>	
								</div>
							  </div>
							</div>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  <hr class="mt10">            
				</div>
			  </div>   
		 </section>';	
		foreach(glob(ROOT.'usr/modules/*/admin/moderation.php') as $listed) require_once($listed);	
		$adminTpl->admin_foot($last_visit, $last_ip);
		
	}
	function init_login() 
	{
	global $adminTpl, $admin_conf, $core;
		if($core->auth->isUser && $core->auth->isAdmin)
		{
			if(isset($_SESSION[SESS_AUTH]) && $_SESSION[SESS_AUTH] == 'ok' OR $admin_conf['sessions'] == 0)
			{
				return false;
			} 
			else 
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	function login() 
	{
	global $adminTpl, $core, $config, $db, $admin_conf, $lang;
		require ROOT . 'etc/social.config.php';			
		if ($social['admin'] != '0')
		{
			$s_list = social_list_admin();
		}
		else
		{
			$s_list = '';
		}
		$adminTpl->sep = '';
		if(isset($_POST['nick']))
		{
			$nick = filter($_POST['nick'], 'nick');
			$password = md5(md5($_POST['password']));
			if(!empty($nick) && !empty($_POST['password']))
			{
				$access = $db->getRow($db->query("SELECT id, password, tail FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE `nick` = '" . $db->safesql($nick) . "' AND `group`='1'"));
				$no_head = true;
				
				if (md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']) == $access['password']) 
				{
					if($core->auth->isUser && $core->auth->isAdmin)
					{
						$_SESSION[SESS_AUTH] = 'ok';
					}
					else
					{
						$_SESSION[SESS_AUTH] = 'ok';					
						if(isset($_POST['remember']))
						{
							$time = COOKIE_TIME;
						}
						else
						{
							$time = '3600';
						}
						$newHash = md5(@$_SERVER['HTTP_USER_AGENT'].$config['uniqKey']);
						setcookie(COOKIE_AUTH, engine_encode(serialize(array('id' => $access['id'], 'nick' => $nick, 'password' => md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']), 'hash' => $newHash))), time() + $time, '/');
					}
					
					if(isset($_SESSION[SESS_AUTH])) {
						$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace('[nick]', $nick, $lang['logs_good_login']) . "', '1')");
						if(eregStrt(ADMIN, $_SERVER['HTTP_REFERER']))
						{
							location($_SERVER['HTTP_REFERER']);
						}
						else
						{
							location(ADMIN);
						}
					}
				}
				else
				{
					if (!isset($_SESSION[SESS_COUNT])) 
					{
						$_SESSION[SESS_COUNT] = 0;
					}
					
					$counter = $_SESSION[SESS_COUNT]++;
					$turns = 5-$counter;
					$adminTpl->loadFile('login');
					
					if($counter == 3) 
					{
						$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace(array('[nick]', '[pass]'), array($nick, str($_POST['password'], 4)), $lang['logs_bad_login']) . "', '2')");
					}
					
					if($turns <= 0) 
					{
						$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.$lang['no_turns'].'
								</div>';
					} 
					else 
					{
						$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.str_replace('{turns}', $turns, $lang['false_turn']).'
								</div>';
						
					}
				}
			}
			else
			{
					$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.$lang['empty_login'].'
								</div>';
			}
		}
		else
		{
			$stop= '';
			
		}
		$adminTpl->loadFile('login');
		$adminTpl->setVar('STOP', $stop);
		$adminTpl->setVar('LANG', $core->InitLang());
		$adminTpl->setVar('URL', $config['url']);
		$adminTpl->setVar('ADMIN', $config['url'].'/'.ADMIN);	
		$adminTpl->setVar('ADM_THEME', 'usr/tpl/admin');			
		$adminTpl->setVar('SOCIAL', $s_list);	
		$adminTpl->setVar('LICENSE', 'Powered by <a href="https://jmy.su" target="_blank" title="JMY CORE">JMY CORE</a>');	
		$adminTpl->end();
		
	}

	global $lang;
	if(init_login()) 
	{
		login();
	} 
	else 
	{
		require ROOT . 'root/list.php';	
		switch(isset($url[1]) ? $url[1] : null) {
			default:
				if(isset($url[1]))
				{
					if(isset($component_array[$url[1]]) OR isset($services_array[$url[1]]))
					{
						if(checkAdmControl($url[1]))
						{
							require ROOT . 'root/modules/' . $url[1] . '.admin.php';
						}
						else
						{
							noadmAccess();
						}
					}
					else
					{
						if(checkAdmControl('index'))
						{
							admin_main();
						}
						else
						{
							noadmAccess();
						}
					}
				}
				else
				{
					if(checkAdmControl('index'))
					{
						admin_main();
					}
					else
					{
						noadmAccess();
					}
				}
			break;
			
			case 'do':
				$switch = filter($url[2]);
				switch($switch) {
					case 'logout':
						$_SESSION[SESS_AUTH] = null;
						$_SESSION[SESS_COUNT] = 0;
						$core->auth->logout();
						header('Location: /');
						break;
					
					case 'tic':
						echo yandex_tic($_SERVER['HTTP_HOST']);
						break;
					
					case 'pr':
						echo getPageRank($_SERVER['HTTP_HOST']);
						break;					
						
					case 'clearCache':
						if(checkAdmControl('index'))
						{
							ajaxInit();
							full_rmdir(ROOT . 'tmp/mysql');
							full_rmdir(ROOT . 'tmp/cache');
							@mkdir(ROOT . 'tmp/mysql', 0777);
							@mkdir(ROOT . 'tmp/cache', 0777);
							header('Location: /' . ADMIN);
						}
						break;
					
					
				}
			break;
			
			case 'module':
				define('ADMIN_SWITCH', true);
				$mod = $url[2];
				if(file_exists(ROOT . 'usr/modules/' . $mod . '/admin/index.php')) 
				{
					if($core->checkModule($mod)=='1') 
					{
						if(checkAdmControl($mod))
						{
							require ROOT . 'usr/modules/' . $mod . '/admin/index.php';
						}
						else
						{
							noadmAccess();
						}					
					}
					else
					{
						nomodActive();
					}				
				} 
				else 
				{
					header('Location: /' . ADMIN);
				}
				break;
			
			case 'logs':
			global $adminTpl,  $db;
				ajaxInit();
				$type = $url[2];
				$num = isset($url[3]) ? intval($url[3]) : '';			
				switch($type) 
				{
					case "clear":
						$db->query("TRUNCATE TABLE " . DB_PREFIX . "_logs");
						header('Location: /' . ADMIN);
						break;
				}
				break;
				
			case 'ajax':
			global $adminTpl, $db, $lang;;
				ajaxInit();
				$type = $url[2];
				$num = isset($url[3]) ? intval($url[3]) : '';			
				switch($type) 
				{
					case "server_stats":
						$free = disk_free_space('/');
						$full = disk_total_space('/');
						$space = round(($full-$free)/($full/100));
						echo '<div class="col-sm-4 mt-30 mt-sm-0">
								  <div id="c1" value="'.$space.'" data-circle-color="system" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_space'].'</p>
								  </div>
								  <div class="col-sm-4 mt-30 mt-sm-0">
									<div id="c2" value="'.getServerCPULoad().'" data-circle-color="success" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_cpu'].'</p>
								  </div>
								  <div class="col-sm-4 mt-30 mt-sm-0">
									<div id="c3" value="'.getServerRAM().'" data-circle-color="danger" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_ram'].'</p>
							  </div>';
					break;
					
					case "last_comm":
					$query = $db->query("SELECT c.*, u.nick, u.group, u.last_visit FROM ".DB_PREFIX."_comments as c LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u on (c.uid=u.id) ORDER BY date DESC LIMIT 5");
						if($db->numRows($query) > 0) 
						{							
							while($commment = $db->getRow($query)) 
							{
								$tt = str(htmlspecialchars(strip_tags($commment['text'])), 30);							
								echo '
								<div class="box-sm box-offset-20 mt-30">
									<div class="box__left"><img height="60" width="60" src="'.avatar($commment['uid']).'" class="br4"></div>
									<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">
										<div class="fs18 ib text-black"><a href="profile/' . $commment['nick'] . '" title="' . $commment['nick'] . '">' . $commment['nick'] . '</a></div>
										<div class="float-sm-right text-mischka text-bold monserrat fs12 mt7">' . formatDate($commment['date'], true) . '</div>
										<p class="mt7">' . (($tt != '') ? $tt : '<font color="red">'.$lang['no_text'].'</font>') . '</p>
									</div>
								</div>
								<div class="mt15 clearfix">
									<div class="float-right list-inline">
										<li><a style="cursor: pointer;" onclick="modal_o(\'#modal-form-com-'.$commment['id'].'\')" class="text-primary text-uppercase fs12 text-bold monserrat">'.$lang['edit_fast'].'</a></li>
										<li><a href="'.commentLink($commment['module'], $commment['post_id'], false).'" target="_blank" class="text-primary text-uppercase fs12 text-bold monserrat">'.$lang['view'].'</a></li>
										<li><a href="'.ADMIN.'/comments/delete/'.$commment['id'].'" class="text-danger text-uppercase fs12 text-bold monserrat">'.$lang['delete'].'</a></li>
									</div>				  
									<div id="modal-form-com-'.$commment['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">
										<div class="panel">
											<div class="panel-heading">
												<span class="panel-title"><i class="fa fa-rocket"></i>'.$lang['main_com_edit'].'</span>
											</div>
											<form id="text" method="post" action="'.ADMIN.'/ajax/save_comm">
												<div class="panel-body p25">
													<div class="section">
														<label for="text" class="field prepend-icon">
														  <textarea id="text" name="text" class="gui-textarea">'.$commment['text'].'</textarea>
														  <label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
														</label>
													</div>
												</div>
												<input type="hidden" name="cid" value="' . $commment['id'] . '">
												<div class="panel-footer">
												  <button type="submit" class="button btn-primary">'.$lang['update'].'</button>
												</div>
											</form>
										</div>
									</div>
								</div>';
									
							}
						} 
						else 
						{
							echo '<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">'.$lang['main_last_comment_empty'].'</div>';
						}
					break;
				
				
					case 'last_user':		
						if ($core->auth->isAdmin)
						{						
							$query = $db->query('SELECT u.*, g.name FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` as u LEFT JOIN `' . USER_DB . '`.`' . USER_PREFIX . '_groups` as g on(u.group = g.id) ORDER BY regdate DESC LIMIT 5');
								if($db->numRows($query) > 0) 
								{				
									while($user = $db->getRow($query)) 
									{
										echo '
										<div class="mt-60 mt-sm-30 d-sm-flex">
											<div><img height="60" width="60" src="'.avatar($user['id']).'" class="br4"></div>
											<div class="pl30 pr30">
												<div>
												  <div>
													<div class="fs18 ib text-black">' . $user['nick'] .'</div>
													<p class="mt7">'.(empty($user['signature']) ? $lang['users_sign_empty'] : $user['signature']).'</p>
												  </div>
												</div>
											</div>
											<div class="text-mischka text-uppercase text-bold monserrat fs12 d-sm-flex flex-direction-column mla min-w145">
												<div><span class="bull bg-'.($core->isOnline($user['id']) ? 'success' : 'info') .'"></span><span>' . ($core->isOnline($user['id']) ? $lang['online'] : formatDate($user['last_visit'])) . '</span></div>
												<div class="btn-group btn-group-xs mt15 d-sm-flex">
													<a href="'.ADMIN.'/user/edit/'.$user['id'].'" class="btn btn-info mw70">'.$lang['edit_short'].'</a>
													<a href="'.ADMIN.'/user/delete/'.$user['id'].'" class="btn btn-danger mw70">'.$lang['delete'].'</a>
												</div>
											</div>
										</div>';
									}
								}
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;		

					case 'last_action':		
						if ($core->auth->isAdmin)
						{	
							$query = $db->query("SELECT * FROM " . DB_PREFIX . "_logs ORDER BY time DESC");
							if($db->numRows($query) > 0) 
							{
								echo '<table class="table list-table">
									<tbody>';
								while($log = $db->getRow($query)) 
								{
									echo '
										  <tr>
											<td><span class="icon '.(($log['level']==2) ? 'text-warning fa fa-bell' : 'text-info fa fa-bullhorn').'"></span>'.$log['history'].'</td>
											<td class="text-right">'.formatDate($log['time'], true).'</td>
										  </tr>';	
								}
								echo  '</tbody>
									  </table>';		
							}
							else
							{
								echo '<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">'.$lang['main_last_action_empty'].'</div>';
							}

						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;							
				
					case 'hide_admin':
						global $adminTpl, $db, $config;
						ajaxInit();
						if ($core->auth->isAdmin)
						{
							$type = $url[2];
							echo $type; 
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;
					
					case 'save_comm':
						global $adminTpl, $db, $config;
						$cid = isset($_POST['cid']) ? intval($_POST['cid']) : '';
						$text = isset($_POST['text']) ? filter($_POST['text']) : '';
						ajaxInit();
						if ($core->auth->isAdmin)
						{
							$bb = new bb;
							$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `text` = '" . $db->safesql($bb->parse(processText($text))) . "' WHERE `id` =" . $cid . ";");
							header('Location: /' . ADMIN);
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;
					
					case 'addition':
						$type = $url[2];
						switch($type) 
						{
							case "tic":
								echo yandex_tic('http://'.$_SERVER['HTTP_HOST']);
								break;
						}
					break;	
				}
					break;
		}
	}
}