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
								<th>' . $lang['polls_name'] . '</th>
								<th>' . $lang['polls_list_variant'] . '</th>
								<th>' . $lang['polls_list_answer'] . '</th>
								<th>' . $lang['polls_list_max'] . '</th>
								<th class="text-center">' . $lang['status'] . '</th>
								<th>' . $lang['action'] . '</th>								
								<th>
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
								$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
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
										<button type="button" onclick="location.href = \'{ADMIN}/voting/view/'. $poll['ppid'] .'\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
										<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
										<ul role="menu" class="dropdown-menu">	
											<li><a href="'.ADMIN.'/voting/edit/'.$poll['ppid'].'">'.$lang['edit'].'</a></li> 
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
								<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . $lang['delete'] . '" />
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
		$adminTpl->admin_head($lang['polls_add']);
		$title = filter($_POST['title'], 'title');
		$vars = filter($_POST['vars'], 'html');
		$max = intval($_POST['max']);
		$variants = explode("\n", $vars);
		echo '<div id="content" class="animated fadeIn">';
		if($title && $vars)
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_polls` ( `id` , `title` , `votes` , `max`, `active` ) VALUES (NULL, '" . $db->safesql(processText($title)) . "', '0', '" . $max . "', '1');");
			list($id) = $db->fetchRow($db->query("SELECT id FROM `" . DB_PREFIX . "_polls` WHERE title = '" . $db->safesql(processText($title)) . "' AND max = '" . $max . "'"));
		
			foreach($variants as $var)
			{
				if($var !== '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_poll_questions` ( `id` , `pid` , `variant` , `position` , `vote` ) VALUES (NULL, '" . $id . "', '" . str_replace(',', '||', trim($db->safesql($var))) . "', '', '0');");
				}
			}			
			$adminTpl->info($lang['polls_add_ok'], 'info', null, $lang['info'], $lang['polls_list'], ADMIN.'/voting');
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';
		$adminTpl->admin_foot();
		break;
	
	case 'edit':
		$id = intval($url[3]);
		add($id);		
		break;
		
	case 'save_edit':
		$adminTpl->admin_head($lang['polls_edit']);
		$id = intval($_POST['id']);
		$title = filter($_POST['title']);
		$vars = filter($_POST['vars']);
		$max = intval($_POST['max']);
		$votes = intval($_POST['votes']);
		$variants = explode("\n", $vars);
		echo '<div id="content" class="animated fadeIn">';
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
			$db->query("UPDATE `" . DB_PREFIX . "_polls` SET `title` = '" . $title . "', `votes` = '".$allVote."', `max` = '" . $max . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");			
			$adminTpl->info($lang['polls_edit_ok'], 'info', null, $lang['info'], $lang['polls_list'], ADMIN.'/voting');
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';
		$adminTpl->admin_foot();
		break;
		
	case 'view':
		$id = intval($url[3]);
		if(!empty($id))
		{
			global $adminTpl, $config, $core, $lang, $db;
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_poll_questions` WHERE pid = '" . $id . "'");
			$title = prepareTitle($rows['title']);
			$max = $rows['max'];
			$admhead = $lang['polls_edit'];
			$btn = $lang['update'];
			$variant = '';
			$action = ADMIN.'/voting/save_edit';
			$varinat = '';
			$numb = '';
			while($rowsq = $db->getRow($query))
			{	
				$text=rtrim($rowsq['variant'],"\n\r");
				$varinat .= '"'.$text.'", ';
				$numb .= $rowsq['vote'].', ';
					
			}
			$varinat = substr($varinat, 0, -2);
			$numb = substr($numb, 0, -2);
			$adminTpl->admin_head($lang['polls_view']);						
			echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-info panel-border top">
					<div class="panel-heading"><span class="panel-title">'.$lang['polls_view'].'</span>			
				</div>
				  <div class="panel-body">
					<p class="p2">
					<div id="result" style="min-width: 310px; max-width: 800px; height: 200px; margin: 0 auto"></div>';				
					$adminTpl->footIncludes[] = '
					<script type="text/javascript">
						var pollsHighCharts = function ()
						{				 
							var highColors = [bgWarning, bgPrimary, bgInfo, bgAlert, bgDanger, bgSuccess, bgSystem, bgDark];
							var sparkColors = {
								"primary": [bgPrimary, bgPrimaryLr, bgPrimaryDr],
								"info": [bgInfo, bgInfoLr, bgInfoDr],
								"warning": [bgWarning, bgWarningLr, bgWarningDr],
								"success": [bgSuccess, bgSuccessLr, bgSuccessDr],
								"alert": [bgAlert, bgAlertLr, bgAlertDr]
							};
							var pollsHighCharts = function()
							{ 
								var pollsHighBars = function() 
								{
									var bars1 = $("#result");
									if (bars1.length) 
									{
										$("#result").highcharts({
											colors: highColors,
											credits: false,
											legend: {
												enabled: false,
												y: -5,
												verticalAlign: "top",
												useHTML: true
											},
											chart: {
												spacingLeft: 30,
												type: "bar",
												marginBottom: 0,
												marginTop: 0
											},
											title: {
												text: null
											},
											xAxis: {
												showEmpty: false,
												tickLength: 80,
												lineColor: "#fff",
												tickColor: "#eee",
												offset: 1,
												categories: ['.$varinat.'],
												title: {
												  text: null
												},
												labels: {
												  align: "right",
												}
											},
											yAxis: {
												min: 0,
												gridLineWidth: 0,
												showEmpty: false,
												title: {
												  text: null
												},
												labels: {
												  enabled: false,
												}
											},
											tooltip: {
												valueSuffix: " '.$lang['polls_people'].'"
											},
											plotOptions: {
												bar: {},
												column: {
													colorByPoint: true,
														}
											},
											series: [{
												id: 0,
												name: "'.$lang['polls_user'].'",
												data: ['.$numb.']
												}]
										});
									}
								};	pollsHighBars();
							}
							return {
								init: function () { 
								pollsHighCharts(); 
								}	
							}
						}();					
					</script>';
					$adminTpl->js_code[] ='pollsHighCharts.init(); ';					
				echo '</p>
				  </div>
				</div></div>';
			$adminTpl->admin_foot();
		}
		else
		{
			location(ADMIN.'/voting');
		}	
		break;
		
	case 'delete':
		$id = intval($url[3]);
		deleteVot($id);
		location(ADMIN.'/voting');
		break;
	
	case 'retivate':
		$id = intval($url[3]);
		if(!empty($id))
		{
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$active = 0;
			if ($rows['active']==0)
			{
				$active = 1;
			}
			$db->query("UPDATE `" . DB_PREFIX . "_polls` SET `active` = '" . $active . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");
			location(ADMIN.'/voting');
		}
		else
		{
			location(ADMIN.'/voting');
		}		
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
