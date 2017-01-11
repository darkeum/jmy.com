<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

global $lang; 

function yesorno($val = 0)
{
	if ($val)
	{
		return '<span class="fa fa-check-circle text-success fa-md"></span>';
	}
	else
	{
		return '<span class="fa fa-circle text-danger fa-md"></span>';
	}
		
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
		$adminTpl->admin_head($lang['group_users']);
		echo '<div id="content" class="animated fadeIn">';			
		$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY name ASC");
		if($db->numRows($query) > 0) 
		{
			echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">' . $lang['group_users_list'] . ':</span>                
              </div>
              <div class="panel-body pn"> 
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/module/news/action&moderate">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-6">'.$lang['title'].'</th>
							<th class="col-md-1 text-center">'.$lang['group_users_special'].'</th>
							<th class="col-md-1 text-center">'.$lang['panel'].'</th>
							<th class="col-md-2 text-center">'.$lang['group_users_protect'].'</th>
							<th class="col-md-3">'.$lang['action'].'</th>									
						</tr>						
                    </thead>
                    <tbody>';		
			while($group = $db->getRow($query)) 
			{
				echo '
				<tr>
					<td><span class="pd-l-sm"></span>' . $group['id'] . '</td>
					<td><div id="editTitle_' . $group['id'] . '" onclick="EditTitle(\'editTitle_' . $group['id'] . '\', \'group\', \'' . $group['id'] . '\')">' . $group['name'] . '</div></td>
					<td class="text-center">' . yesorno($group['special'])  . '</td>
					<td class="text-center">' . yesorno($group['admin']) . '</td>
					<td class="text-center">' . yesorno($group['protect']) . '</td>
					<td>
						<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/groups/edit/' . $group['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">							
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$group['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>';
					if ($group['protect'])
					{
						echo '<div id="modal-form-'.$group['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['info'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[group]', $group['name'], $lang['group_users_protect_title']) .  '</h3>							
							<hr class="short alt">
							<p>' . str_replace('[group]', $group['name'], $lang['group_users_protect_text']) .  '</p>
						  </div>						  
						</div>
					  </div>';
					}
					else 
					{
					echo '<div id="modal-form-'.$group['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[group]', $group['name'], $lang['group_users_del_title']) .  '</h3>							
							<hr class="short alt">
							<p>' . str_replace('[group]', $group['name'], $lang['group_users_del_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/groups/delete/' . $group['id'] .'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>';
					}
					echo '</td>
				</tr>';	
			}
			echo '</tbody></table>
			</form></div></div>';
		} 
		else
		{
			$adminTpl->info($lang['group_users_empty'], 'empty', null, $lang['group_users_list'], $lang['group_users_add'], ADMIN.'/groups/add');				
		}
		echo'</div>';
		$adminTpl->admin_foot();
		break;	
		
	case 'edit':
		$gid = intval($url[3]);
		$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` WHERE id='" . $gid . "'");
		if($db->numRows($query) == 1) 
		{
			$group = $db->getRow($query);
			$control = unserialize($group['control']);
		}
		else
		{
			location(ADMIN);
		}
		$tit = $lang['group_users_edit'];
	case 'add':
		if(!isset($gid))
		{
			$group['name'] = '';
			$group['guest'] = 1;
			$group['user'] = 1;
			$group['moderator'] = 0;
			$group['admin'] = 0;
			$group['banned'] = 0;
			$group['showHide'] = 1;
			$group['showAttach'] = 1;
			$group['loadAttach'] = 0;
			$group['addPost'] = 0;
			$group['addComment'] = 1;
			$group['allowRating'] = 1;
			$group['maxWidth'] = $user['avatar_width'];
			$group['maxPms'] = 50;
			$group['icon'] = 'media/groups/';
			$group['color'] = '';
			$group['points'] = 0;
			$group['special'] = 0;
			$tit = $lang['group_users_add'];
		}
		$adminTpl->admin_head($tit);
		$validation_array = array(		
			'title' => array(
				'required' =>  array('true', $lang['group_users_add_title_err'])			
			),
			'points' => array(
				'required' =>  array('true', $lang['group_users_add_points_err_1']),
				'number' =>  array('true', $lang['group_users_add_points_err_2']),
				'min' =>  array('0', $lang['group_users_add_points_err_3'])				
			),
			'maxWidth' => array(
				'required' =>  array('true', $lang['group_users_add_avatar_width_err_1']),
				'number' =>  array('true', $lang['group_users_add_avatar_width_err_2']),
				'min' =>  array('0', $lang['group_users_add_avatar_width_err_3'])				
			),
			'maxPms' => array(
				'required' =>  array('true', $lang['group_users_add_pm_err_1']),
				'number' =>  array('true', $lang['group_users_add_pm_err_2']),
				'min' =>  array('0', $lang['group_users_add_pm_err_3'])				
			),	
			'icon' => array(
				'required' =>  array('true', $lang['group_users_add_icon_err'])							
			),	
			'color' => array(
				'required' =>  array('true', $lang['group_users_add_color_err'])		
			)	
		);
		validationInit($validation_array);	
		fancyboxInit();
		colorpickerInit('color_p',$group['color']);
		$adminTpl->js_code[] = '$("#points").spinner();';
		$_SESSION["RF"]["fff"] ="media/groups/";	
		echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $tit .'</span>					
			</div>
			<div class="panel-body admin-form">		
				<form id="admin-form" class="form-horizontal parsley-form" role="form" action="{ADMIN}/groups/save" method="post">			
					<div class="form-group">
						<label for="title"  class="col-sm-3 control-label">'. $lang['group_users_add_title'] .'</label>
						<div class="col-sm-4">
							<label for="name" class="field prepend-icon">
								<input value="' . $group['name'] . '" type="text" name="title" class="form-control" data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['group_users_add_title_pre'].'">
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>	
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_special'] .'</label>
						<div class="col-sm-4">
							<div class="switch switch-info round switch-inline">
								<input type="hidden" name="special" value="0">
								<input id="radio_special" name="special" type="checkbox"  value="1" onclick="showhide(\'points_gr\');">
								<label for="radio_special"></label>		
							</div>
							<p class="help-block">'. $lang['group_users_add_special_desc'] .'</p>
						</div>
					</div>
					<div class="form-group" id="points_gr" style="display:none;">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_points'] .'</label>
						<div class="col-sm-4">
							<div class="input-group">	
								<input id="points" value="' . $group['points'] . '" type="text" name="points" class="form-control ui-spinner-input">
							</div>
							<p class="help-block">'. $lang['group_users_add_points_desc'] .'</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_guest'] .'</label>
						<div class="col-sm-4">
							'.radio("guest", $group['guest']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_user'] .'</label>
						<div class="col-sm-4">
							'.radio("user", $group['user']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_moder'] .'</label>
						<div class="col-sm-4">
							'.radio("moderator", $group['moderator']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_admin'] .'</label>
						<div class="col-sm-4">
							'.radio("admin", $group['admin']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_ban'] .'</label>
						<div class="col-sm-4">
							'.radio("banned", $group['banned']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_right_full'] .'</label>
						<div class="col-sm-4">
							<div class="switch switch-info round switch-inline">
								<input type="hidden" name="aFullPerm" value="0">
								<input id="aFullPerm" name="aFullPerm" type="checkbox" checked value="1" onclick="showhide(\'aPerm\');">
								<label for="aFullPerm"></label>		
							</div>
						</div>
					</div>
					<div class="form-group" '.(!empty($group['control']) ? '' : 'style="display:none;"') . ' id="aPerm">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-8">
							<div class="option-group field section">';
								require ROOT . 'root/list.php';		
								foreach(glob(ROOT.'usr/modules/*/admin/list.php') as $listed) 
								{
									include($listed);
								}
								$mods = '';
								$comp = '';
								$serv = '';
								foreach($module_array as $module => $params) 
								{
									$mods .= ' <label class="option block mt10"><input type="checkbox" name="adminAccess[]" value="' . $module . '" ' . ((!isset($gid) || empty($group['control'])) ? 'checked' : (in_array($module, $control) ? 'checked' : '')) . ' /><span class="checkbox"></span>'.$params['name'] . '</label><br />';
								}
								foreach($component_array as $component => $params) 
								{
									if($component == '') $component = 'index';
									$comp .= '<label class="option block mt10"><input type="checkbox" name="adminAccess[]" value="' . $component . '"  ' . ((!isset($gid) || empty($group['control'])) ? 'checked' : (in_array($component, $control) ? 'checked' : '')) . ' /><span class="checkbox"></span>'.$params['name'] . '</label> <br />';
								}
								foreach($services_array as $sevices => $params) 
								{
									$serv .= '<label class="option block mt10"><input type="checkbox" name="adminAccess[]" value="' . $sevices . '"  ' . ((!isset($gid) || empty($group['control'])) ? 'checked' : (in_array($sevices, $control) ? 'checked' : '')) . ' /><span class="checkbox"></span> '.$params['name'] . '</label> <br />';
								}
								echo '<div style="float:left; width:200px;"><strong>'.$lang['components'].'</strong><br />'. $comp .'</div>
								<div style="float:left; width:200px;"><strong>'.$lang['services'].'</strong><br />'. $serv .'</div>
								<div style="float:left; width:200px;"><strong>'.$lang['modules'].'</strong><br />'. $mods .'</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_hide'] .'</label>
						<div class="col-sm-4">
							'.radio("showHide", $group['showHide']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_view'] .'</label>
						<div class="col-sm-4">
							'.radio("showAttach", $group['showAttach']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_load'] .'</label>
						<div class="col-sm-4">
							'.radio("loadAttach", $group['loadAttach']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_addnews'] .'</label>
						<div class="col-sm-4">
							'.radio("addPost", $group['addPost']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_comment'] .'</label>
						<div class="col-sm-4">
							'.radio("addComment", $group['addComment']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_rating'] .'</label>
						<div class="col-sm-4">
							'.radio("allowRating", $group['allowRating']).'														
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_avatar_width'] .'</label>
						<div class="col-sm-4">
							<input value="' . $group['maxWidth'] . '" type="text" name="maxWidth" class="form-control" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_pm'] .'</label>
						<div class="col-sm-4">
							<input value="' . $group['maxPms'] . '" type="text" name="maxPms" class="form-control" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_icon'] .'</label>
						<div class="col-sm-4">
                            <div class="smart-widget sm-right smr-50">
								<label class="field">
									<input id="fieldID4" value="' .  $group['icon'] . '" type="text" name="icon" class="gui-input">
								</label>
								<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=images&field_id=fieldID4">
									<button class="button"><i class="fa fa-folder-open-o"></i></button>
								</a>                            
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">'. $lang['group_users_add_color'] .'</label>
						<div class="col-sm-4">
							<div id="color_p" class="input-group pull-right">
							  <input type="text" name="color" class="form-control" value="'.$group['color'].'"><span class="input-group-addon colorpicker-component cursor"><i></i></span>
							</div>	
						</div>
					</div>';
					$do_name= $lang['add'];
					if(isset($gid)) 
					{			
						echo "<input name=\"edit\" type=\"hidden\" class=\"buttons\" id=\"sub\" value=\"" . $gid . "\" />";
						$do_name= $lang['update'];
					}
					echo'<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-4">
								<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="'.$do_name.'">
							</div>
						</div>
					</form>
				</div>
			</div>';	
		$adminTpl->admin_foot();
	break;
		
	case 'save':
		$tit = $lang['group_users_add'];
		if(!isset($_POST['edit']))
		{
			$tit = $lang['group_users_edit'];	
		}
		$adminTpl->admin_head($tit);
		echo '<div id="content" class="animated fadeIn">';
		$title = filter(htmlspecialchars_decode($_POST['title']), 'title');
		$guest =  isset($_POST['guest']) ? intval($_POST['guest']) : '0';
		$user = isset($_POST['user']) ? intval($_POST['user']) : '0';
		$moderator = isset($_POST['moderator']) ? intval($_POST['moderator']) : '0';
		$admin = isset($_POST['admin']) ? intval($_POST['admin']) : '0';
		$aFullPerm = isset($_POST['aFullPerm']) ? intval($_POST['aFullPerm']) : '0';
		$banned = isset($_POST['banned']) ? intval($_POST['banned']) : '0';
		$showHide = isset($_POST['showHide']) ? intval($_POST['showHide']) : '0';
		$showAttach = isset($_POST['showAttach']) ? intval($_POST['showAttach']) : '0';
		$loadAttach = isset($_POST['loadAttach']) ? intval($_POST['loadAttach']) : '0';
		$addPost = isset($_POST['addPost']) ? intval($_POST['addPost']) : '0';
		$addComment = isset($_POST['addComment']) ? intval($_POST['addComment']) : '0';
		$allowRating = isset($_POST['allowRating']) ? intval($_POST['allowRating']) : '0';
		$maxWidth = isset($_POST['maxWidth']) ? intval($_POST['maxWidth']) : '0';
		$special = isset($_POST['special']) ? intval($_POST['special']) : '0';
		$points = isset($_POST['points']) ? intval($_POST['points']) : '0';
		$maxPms = isset($_POST['maxPms']) ? intval($_POST['maxPms']) : '0';
		$icon = filter($_POST['icon']);
		$color = filter($_POST['color']);
		$control = '';		
		if($aFullPerm == 0)
		{
			if(!empty($_POST['adminAccess']))
			{
				$control = serialize($_POST['adminAccess']);
			}
		}		
		if($title)
		{
			if(!isset($_POST['edit']))
			{
				$db->query("INSERT INTO `" . USER_DB . "`.`" . USER_PREFIX . "_groups` (`name` , `guest` , `user` , `moderator` , `admin` , `banned` , `showHide` , `showAttach` , `loadAttach` , `addPost` , `addComment` , `allowRating` , `maxWidth` , `maxPms` , `control` , `icon` , `color` , `points` , `special` ) VALUES ('" . $title . "', '" . $guest . "', '" . $user . "', '" . $moderator . "', '" . $admin . "', '" . $banned . "', '" . $showHide . "', '" . $showAttach . "', '" . $loadAttach . "', '" . $addPost . "', '" . $addComment . "', '" . $allowRating . "', '" . $maxWidth . "', '" . $maxPms . "', '" . $control . "', '" . $icon . "', '" . $color . "', '" . $points . "', '" . $special . "');");
				$adminTpl->info($lang['group_users_add_ok'], 'info', null, $lang['info'], $lang['group_users_list'], ADMIN.'/groups');
			}
			else
			{
				
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_groups` SET `name` = '" . $title . "', `guest` = '" . $guest . "', `user` = '" . $user . "', `moderator` = '" . $moderator . "', `admin` = '" . $admin . "', `banned` = '" . $banned . "', `showHide` = '" . $showHide . "', `showAttach` = '" . $showAttach . "', `loadAttach` = '" . $loadAttach . "', `addPost` = '" . $addPost . "', `addComment` = '" . $addComment . "', `allowRating` = '" . $allowRating . "', `maxWidth` = '" . $maxWidth . "', `maxPms` = '" . $maxPms . "', `control` = '" . $control . "', `icon` = '" . $icon . "', `color` = '" . $color . "', `points` = '" . $points . "', `special` = '" . $special . "' WHERE `id` =" . intval($_POST['edit']) . " LIMIT 1 ;");
				$adminTpl->info($lang['group_users_edit_ok'], 'info', null, $lang['info'], $lang['group_users_list'], ADMIN.'/groups');
			}
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';		
		$adminTpl->admin_foot();
		break;
	
	case 'delete':
		$id = intval($url[3]);
		$db->query("DELETE FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` WHERE `id` = '" . $id . "'");
		location(ADMIN.'/groups');
		break;
		
	case 'points':
		require (ROOT.'etc/points.config.php');		
		$configBox = array(
			'points' => array(
				'varName' => 'points_conf',
				'title' => $lang['group_users_config_name'],
				'groups' => array(
					'main' => array(
						'title' => $lang['group_users_config_name'],
						'vars' => array(
							'add_news' => array(
								'title' => $lang['group_users_config_addnews'],
								'description' => $lang['group_users_config_addnews_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'add_comment' => array(
								'title' => $lang['group_users_config_addcom'],
								'description' => $lang['group_users_config_addcom_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'register' => array(
								'title' => $lang['group_users_config_reg'],
								'description' => $lang['group_users_config_reg_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'carma' => array(
								'title' => $lang['group_users_config_carma'],
								'description' => $lang['group_users_config_carma_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'add_friend' => array(
								'title' => $lang['group_users_config_friends'],
								'description' => $lang['group_users_config_friends_desc'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'rating' => array(
								'title' => $lang['group_users_config_vote'],
								'description' => $lang['group_users_config_vote_desc'],
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
		generateConfig($configBox, 'points', '{ADMIN}/groups/points', $ok);
		break;
}