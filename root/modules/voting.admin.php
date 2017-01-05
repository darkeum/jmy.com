<?php

/**
* @name        JMY CORE
* @link        http://jmy.su/
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
switch(isset($url[2]) ? $url[2] : null) {
	default:
		$adminTpl->admin_head($lang['polls']);
		echo '<div id="content" class="animated fadeIn">';
		$query = $db->query("SELECT id as ppid, title, votes, max, active, (SELECT COUNT(id) FROM ".DB_PREFIX."_poll_questions WHERE ppid = pid) as variants FROM ".DB_PREFIX."_polls ORDER BY title");			
		if($db->numRows($query) > 0) 
		{
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'.$lang['polls_list'].':</span>                
              </div>
			  <div class="panel-body pn"> 
				<form id="tablesForm"  style="margin:0; padding:0" method="POST" action="{ADMIN}/voting/action">
					<table class="table table-striped">
						<thead>
							<tr>
								<th><span class="pd-l-sm"></span>#</th>
								<th class="col-md-4">' . $lang['polls_name'] . '</th>
								<th class="col-md-1">' . $lang['polls_list_variant'] . '</th>
								<th class="col-md-1">' . $lang['polls_list_answer'] . '</th>
								<th class="col-md-1">' . $lang['polls_list_max'] . '</th>
								<th class="col-md-3 text-center">' . $lang['status'] . '</th>
								<th class="col-md-2">' . $lang['action'] . '</th>								
								<th class="col-md-1">
									<div class="checkbox-custom mb15">
										<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
										<label for="all"></label>
									</div>	
								</th>
							</tr>
						</thead>
						<tbody>';				
						while($poll = $db->getRow($query)) 
						{
							if ($poll['active'])
							{
								if ($poll['votes'] == $poll['max'] )
								{
									$status_icon = '<span class="fa fa-check-circle text-warning fa-md"></span>';
								}
								else
								{
									$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
								}
							}
							else
							{
								$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
							}
							echo '
							<tr>
								<td><span class="pd-l-sm"></span>' . $poll['ppid'] . '</td>
								<td>' . $poll['title'] . '</td>
								<td>' . $poll['variants'] . '</td>
								<td>' . $poll['votes'] . '</td>
								<td>' . $poll['max'] . '</td>
								<td class="text-center">' . $status_icon . '</td>
								<td>
									<div class="btn-group">
										<button type="button" onclick="location.href = \'{ADMIN}/voting/edit/'. $poll['ppid'] .'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
										<button type="button" data-toggle="dropdown" class="btn btn-dro btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
										<ul role="menu" class="dropdown-menu">											
											<li><a href="'.ADMIN.'/voting/retivate/'.$poll['ppid'].'">'.(($poll['active'] == 0) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>   
											<li class="divider"></li>
											<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$poll['ppid'].'\')">' . $lang['delete'] .'</a></li>
										</ul>
									</div>
									<div id="modal-form-'.$poll['ppid'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<div class="panel">
										  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
										  <div class="panel-body">
											<h3 class="mt5">' . str_replace('[poll]', $poll['title'], $lang['polls_del_title']) .  '</h3>							
											<hr class="short alt">
											<p>' . str_replace('[poll]', $poll['title'], $lang['polls_del_text']) .  '</p>
										  </div>
										  <div class="panel-footer text-right">
											<button type="button" onclick="location.href = \'{ADMIN}/voting/delete/'.$poll['ppid'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
										  </div>
										</div>
									</div>
								</td>
								<td>
									<div class="checkbox-custom mb15">
										<input id="checkbox' . $poll['ppid'] . '" type="checkbox" name="checks[]" value="' . $poll['ppid'] . '"><label for="checkbox' . $poll['ppid'] . '"></label>
									</div>
								</td>
							</tr>';	
						}			
				echo '</tbody>
					  <tfoot class="footer-menu">
						<tr>                    
						  <td colspan="9">
							<nav class="text-right">
								<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . _DELETE . '" />
							 </nav>
						  </td>
						</tr>
					  </tfoot>
				  </table>
				 </form>
				</div>
			</div>';	
		} 
		else 
		{
			$adminTpl->info($lang['polls_empty'], 'empty', null, $lang['polls_list'], $lang['polls_add'], ADMIN.'/voting/add');	
		}
		echo'</div>';		
		$adminTpl->admin_foot();
		break;	
		
	case 'add':
		add();
		break;
		
	case 'save':
		$adminTpl->admin_head(_POLL_ADD);
		$title = filter($_POST['title'], 'title');
		$vars = filter($_POST['vars'], 'html');
		$max = intval($_POST['max']);
		$variants = explode("\n", $vars);
		if($title && $vars)
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_polls` ( `id` , `title` , `votes` , `max` ) VALUES (NULL, '" . $db->safesql(processText($title)) . "', '0', '" . $max . "');");
			list($id) = $db->fetchRow($db->query("SELECT id FROM `" . DB_PREFIX . "_polls` WHERE title = '" . $db->safesql(processText($title)) . "' AND max = '" . $max . "'"));
		
			foreach($variants as $var)
			{
				if($var !== '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_poll_questions` ( `id` , `pid` , `variant` , `position` , `vote` ) VALUES (NULL, '" . $id . "', '" . str_replace(',', '||', trim($db->safesql($var))) . "', '', '0');");
				}
			}
			
			$adminTpl->info(_POLL_INFO_0);
		}
		else
		{
			$adminTpl->info(_BASE_ERROR_0, 'error');
		}
		$adminTpl->admin_foot();
		break;
	
	case 'edit':
		$id = intval($url[3]);
		add($id);		
		break;
		
	case 'save_edit':
		$adminTpl->admin_head(_POLL_EDIT);
		$id = intval($_POST['id']);
		$title = filter($_POST['title']);
		$vars = filter($_POST['vars']);
		$max = intval($_POST['max']);
		$votes = intval($_POST['votes']);
		$variants = explode("\n", $vars);
		if($title && $vars)
		{
			$db->query("DELETE FROM `" . DB_PREFIX . "_poll_questions` WHERE `pid` = '" . $id . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "_poll_voting` WHERE `pid` = '" . $id . "'");
			$allVote = 0;
			foreach($variants as $var)
			{
				if($var !== '')
				{
					$lo = explode('|', $var);
					$db->query("INSERT INTO `" . DB_PREFIX . "_poll_questions` ( `id` , `pid` , `variant` , `position` , `vote` ) VALUES (NULL, '" . $id . "', '" . str_replace(',', '||', trim($db->safesql($lo[0]))) . "', '', '" . $lo[1] . "');");
					$allVote = $allVote+$lo[1];
				}
			}
			$db->query("UPDATE `" . DB_PREFIX . "_polls` SET `title` = '" . $title . "', `votes` = '0', `max` = '" . $max . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");

			
			$adminTpl->info(_POLL_INFO_1);
		}
		else
		{
			$adminTpl->info(_BASE_ERROR_0, 'error');
		}
		$adminTpl->admin_foot();
		break;
		
	case 'delete':
		$id = intval($url[3]);
		deleteVot($id);
		location(ADMIN.'/voting');
		break;
		
	case 'action':
		$checks = $_POST['checks'];
		foreach($checks as $check)
		{
			deleteVot(intval($check));
		}
		location(ADMIN . '/voting/del');
		break;
}

function add($id = null) 
{	
		global $adminTpl, $config, $core, $lang, $db;
		if(isset($id)) 
		{
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_poll_questions` WHERE pid = '" . $id . "'");
			$title = prepareTitle($rows['title']);
			$max = $rows['max'];
			$admhead = $lang['polls_edit'];
			$btn = $lang['update'];
			$variant = '';
			$action = ADMIN.'/voting/save_edit';
			while($rowsq = $db->getRow($query))
			{	
				$text = $rowsq['variant'];															
				$text=rtrim($text,"\n\r");
				$variant .= $text . "|" . $rowsq['vote'] . "\n";		
			}
			
		} 
		else 
		{
			$title = isset($_POST['title']) ? filter($_POST['title']) : '';	
			$variant = isset($_POST['vars']) ? filter($_POST['vars']) : '';	
			$max = isset($_POST['max']) ? filter($_POST['max']) : '0';
			$admhead = $lang['polls_add'];
			$btn = $lang['add'];
			$action = ADMIN.'/voting/save';
		}
		$adminTpl->admin_head($admhead);	
		$validation_array = array(		
			'title' => array(
				'required' =>  array('true', $lang['polls_name_err'])			
			),
			'vars' => array(
				'required' =>  array('true',  $lang['polls_variant_err'])					
			),
			'max' => array(
				'required' =>  array('true',  $lang['polls_max_err'])			
			)
		);
		$adminTpl->js_code[] = '$("#max").spinner();';
		validationInit($validation_array);	
		echo '<div id="content" class="animated fadeIn">
				<div class="panel panel-dark panel-border top">
					<div class="panel-heading"><span class="panel-title">'. $admhead .'</span>					
				</div>
				<div class="panel-body admin-form">
					<form id="admin-form" class="form-horizontal parsley-form" role="form" action="'.$action.'" method="post">
						<div class="form-group">
							<label for="title"  class="col-lg-3 control-label">'. $lang['polls_name'] .'</label>
							<div class="col-lg-4">
								<label for="title" class="field prepend-icon">
									<input id="title" type="text" name="title" value="'. $title .'" placeholder="'.$lang['polls_name_pre'].'" class="gui-input">
									<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
								</label>						
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">'. $lang['polls_variant'] .'</label>
							<div class="col-sm-4">
								<label for="vars" class="field prepend-icon">
									<textarea name="vars" id="vars" placeholder="'.$lang['polls_variant_pre'].'" class="gui-textarea">'.$variant.'</textarea>
									<label for="vars" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">'.$lang['polls_variant_tt'].'</span>
								</label>
							</div>
						</div>	
						<div class="form-group">
							<label for="max"  class="col-lg-3 control-label">'. $lang['polls_max'] .'</label>
							<div class="col-lg-4">
								<div class="input-group">									
									<input id="max" name="max" value="'.$max.'" class="form-control ui-spinner-input">									
								</div>
								<span class="help-block mt5"><i class="fa fa-bell"></i> '.$lang['polls_max_tt'].'</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-4">
								<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="'.$btn.'">						
							</div>
						</div>';
						if(isset($id))
						{
							echo '<input type="hidden" name="edit" value="1">';
							echo '<input type="hidden" name="id" value="' . $id . '">';
						}
				echo '</form>
				</div>';		
		$adminTpl->admin_foot();
}

function deleteVot($id)
{
	global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_poll_questions` WHERE `pid` = '" . $id . "'");
	$db->query("DELETE FROM `" . DB_PREFIX . "_poll_voting` WHERE `pid` = '" . $id . "'");
	$db->query("DELETE FROM `" . DB_PREFIX . "_polls` WHERE `id` = '" . $id . "'");
}
