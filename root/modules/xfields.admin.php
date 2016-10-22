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

function main() 
{
global $adminTpl, $config, $core, $admin_conf, $db;
	$adminTpl->admin_head(_DOP_DOPS);
	echo '<div id="content" class="animated fadeIn">';	
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_xfields ORDER BY id ASC");
	if($db->numRows($query) > 0) 
	{		
	echo '<div class="panel panel-dark panel-border top">
			<div class="panel-heading">
				<span class="panel-title">' . _DOP_LIST . '</span>	
				<div class="widget-menu pull-right" >
					<div style="padding-top: 12px !important;" class="btn-group">
						<button onclick="modal_o(\'#modal-form-help\')" type="button" class="btn btn-xs btn-alert btn-block">'._BASE_HELP.'</button>
					</div>
					<div id="modal-form-help" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-info-circle"></i></span><span class="panel-title">'._BASE_HELP.'</span></div>
						  <div class="panel-body">	
							<h3 class="mt5">'._DOP_DOPS.'</h3>	
							<hr class="short alt">
							<p>'._DOP_HELP_1.'</p>
							<hr class="short alt">
							<p>'._DOP_HELP_2.'</p>
						  </div>
						</div>
					</div>
			</div>
		</div>
		<div class="panel-body pn">
			<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/xfields/action">
				 <table class="table table-striped">
					<thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-2">' . _TITLE . '</th>
							<th class="col-md-2">' . _DESCRIPTION . '</th>
							<th class="col-md-4">' . _DOP_TEMP . '</th>
							<th class="col-md-2">' . _MODULE .'</th>
							<th class="col-md-3">' . _ACTIONS . '</th>
							<th class="col-md-1">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
					</thead>
					<tbody>';							
		while($xfield = $db->getRow($query)) 
		{
			echo '
			<tr>
				<td><span class="pd-l-sm"></span> '. $xfield['id'] . '</td>
				<td>' . $xfield['title'] . '</td>
				<td>' . $xfield['description'] . '</td>
				<td id="copy">[xfield:' . $xfield['id'] . '][xfield_value:' . $xfield['id'] . '][/xfield:' . $xfield['id'] . ']</td>
				<td>' . _mName($xfield['module']) . '</td>
				<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/xfields/edit/' . $xfield['id'] . '\'" class="btn btn-xs btn-primary">'._EDIT_SHORT.'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-dro btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . _ACTIONS . '</span></button>
						<ul role="menu" class="dropdown-menu">
							<li><a id="copy-button" data-clipboard-target="#copy" href="'.$core->fullURL().'#">'._DOP_COPY.'</a></li>							
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$xfield['id'].'\')">' . _DELETE .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$xfield['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'._BASE_CONFIRM.'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[title]', $xfield['title'], _DOP_DELETE_TITLE) .  '</h3>							
							<hr class="short alt">
							<p>' . str_replace('[title]', $xfield['title'], _DOP_DELETE_TEXT) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/xfields/delete/' . $xfield['id'] . '\'" class="btn btn-danger">' . _DELETE .'</button>
						  </div>
						</div>
					  </div> 
					  
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $xfield['id'] . '" type="checkbox" name="checks[]" value="' . $xfield['id'] . '">
						<label for="checkbox' . $xfield['id'] . '"></label>
					</div>
				</td>
			</tr>';	
		}
		echo '</tbody></table>
	<div align="right">
	<table>
	<tr>		
	<td valign="top">
	<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . _DELETE . '" /><span class="pd-l-sm"></span>
	</td>
	</tr>
	</table>
	<br>	
	</div>
	</form></div>';
		echo'</section></div></div>';	
	} 
	
	else 
	{
		$adminTpl->info(_DOP_EMPTY, 'empty', null, _DOP_LIST, _DOP_ADD_DOP, ADMIN.'/xfields/add');	
	}
	echo'</div>';
	$adminTpl->admin_foot();
}

function xfields_add($id = null) 
{
global $adminTpl, $config, $core, $admin_conf, $db;
	if(isset($id)) 
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE id = '" . $id . "'");
		$xfield = $db->getRow($query);
		$title = prepareTitle($xfield['title']);
		$description = $xfield['description'];
		$default = $xfield['content'];
		$type = $xfield['type'];
		$to_user = $xfield['to_user'];
		$mod = $xfield['module'];
		$lang = _DOP_EDIT_DOP;
		$compl = _DOP_EDIT;
	} 
	else 
	{
		$title = '';
		$description = '';
		$default = '';
		$to_user = 1;
		$mod = 'news';
		$type = 0;
		$lang = _DOP_ADD_DOP;
		$compl = _DOP_ADD;
	}
	$adminTpl->admin_head($lang);
	$validation_array = array(		
		'title' => array(
			'required' =>  array('true', _DOP_ADD_TITLE_ERR)			
		),		
		'description' => array(
			'required' =>  array('true', _DOP_ADD_DESC_ERR_1),	
			'maxlength' =>  array(200,  _DOP_ADD_DESC_ERR_2)				
		),		
		'type' => array(
			'required' =>  array('true', _DOP_ADD_TYPE_ERR)				
		)		
	);
	validationInit($validation_array);	
	echo '<div id="content" class="animated fadeIn"><div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lang .'</span>					
			</div>
	<script type="text/javascript">
	function xfieldType(val)
	{
		if(val == \'\')
		{
			gid(\'typeExp\').innerHTML = \'\';
		}
		else if(val == 1)
		{
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><input id="default" type="text" name="default" value="' . $default . '" placeholder="'._DOP_TYPE_1_PRE.'" class="gui-input"></label></div></div>\';
		}
		else if(val == 2)
		{
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'._DOP_TYPE_2_PRE.'" class="gui-textarea">' . $default . '</textarea></label></div></div>\';
		}
		else if(val == 3)
		{
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'._DOP_TYPE_3_PRE.'" class="gui-textarea">' . $default . '</textarea><span class="input-footer">'._DOP_TYPE_3_TT.'</span></label></div></div>\';
		}
	}	
	</script>
	<div class="panel-body admin-form">
		<form id="admin-form" action="{ADMIN}/xfields/save" method="post" name="xfields" role="form" class="form-horizontal parsley-form" data-parsley-validate>
			<div class="form-group">
				<label for="title"  class="col-lg-3 control-label">'. _DOP_ADD_TITLE .'</label>
				<div class="col-lg-4">
					<label for="title" class="field prepend-icon">
						<input id="title" type="text" name="title" value="'. $title .'" placeholder="'._DOP_ADD_TITLE_PRE.'" class="gui-input">
						<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
					</label>						
				</div>
			</div>
			<div class="form-group">
				<label for="description"  class="col-lg-3 control-label">'. _DOP_ADD_DESC .'</label>
				<div class="col-lg-4">
					<label for="description" class="field prepend-icon">
						<input id="description" type="text" name="description" value="'. $description .'" placeholder="'._DOP_ADD_DESC_PRE.'" class="gui-input">
						<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
					</label>						
				</div>
			</div>
			<div class="form-group">
				<label for="category"  class="col-lg-3 control-label">'. _DOP_ADD_MODULE .'</label>
				<div class="col-lg-3">							
					<label class="field select">
						<select name="module" id="module" onchange="updateCatList(this.value, \'category\');" >';							
							foreach ($core->getModList() as $module) 
							{
								if(in_array($module, exceMods('xfields')))
								{
									$selected = ($module == $mod) ? "selected" : "";
									echo '<option value="' . $module . '" ' . $selected . '>' . _mName($module) . '</option>';
								}
							}
							echo '</select><i class="arrow double"></i>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="category"  class="col-lg-3 control-label">'. _DOP_ADD_CAT .'</label>
				<div class="col-lg-3">							
					<label class="field select">
						<select name="category" id="category" >
							<option value="">' . _DOP_ADD_CAT_ALL . '</option>';		
							$cats_arr = $core->aCatList();
							foreach ($cats_arr as $cid => $name) 
							{
								$selected = ($pid == $cid) ? "selected" : "";
								echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
							}		
							echo '</select><i class="arrow double"></i>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">'. _DOP_ADD_TYPE .'</label>
				<div class="col-lg-3">							
					<label class="field select">
						<select name="type" id="type" onchange="xfieldType(this.value); caa(this);">
							<option value="">'._DOP_TYPE_0.'</option>
							<option value="1" '. ($type == 1 ? 'selected' : '') .'>'._DOP_TYPE_1.'</option>
							<option value="2" '. ($type == 2 ? 'selected' : '') .'>'._DOP_TYPE_2.'</option>
							<option value="3" '. ($type == 3 ? 'selected' : '') .'>'._DOP_TYPE_3.'</option>
						</select><i class="arrow double"></i>
					</label>
				</div>
			</div>';	
			if(empty($id))
			{
				echo "<div id=\"typeExp\"></div>";
			}
			else
			{
				switch ($type) 
				{
					case 1:
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><input id="default" type="text" name="default" value="' . $default . '" placeholder="'._DOP_TYPE_1_PRE.'" class="gui-input"></label></div></div></div>';
					break;
					case 2:
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'._DOP_TYPE_2_PRE.'" class="gui-textarea">' . $default . '</textarea></label></div></div></div>';
					break;
					case 3:
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. _DOP_ADD_DEF .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'._DOP_TYPE_3_PRE.'" class="gui-textarea">' . $default . '</textarea><span class="input-footer">'._DOP_TYPE_3_TT.'</span></label></div></div></div>';
					break;
				}		
			}	
			echo '<div class="form-group">
					<label for="to_user"  class="col-lg-3 control-label">'. _DOP_ADD_USER .'</label>
					<div class="col-lg-3">					
						'.checkbox('to_user', $to_user).'
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="'.$compl.'" />						
					</div>
				</div>';
			if(isset($id)) {
				echo '<input type="hidden" name="edit" value="1">';
				echo '<input type="hidden" name="fid" value="' . $id . '">';
			}
	echo '</form></div>';
	$adminTpl->admin_foot();
}

function xfields_save() {
	global $adminTpl, $db, $core;
	if(isset($_POST['edit'])) 
	{
		$adminTpl->admin_head(_DOP_DOPS.' | '._DOP_EDITS);
	} 
	else 
	{
		$adminTpl->admin_head(_DOP_DOPS.' | '._DOP_ADDS);
	}
	$fid = isset($_POST['fid']) ? intval($_POST['fid']) : 0;
	$title = filter($_POST['title'], 'title');
	$description = filter($_POST['description'], 'a');
	$type = intval($_POST['type']);
	$to_user = isset($_POST['to_user']) ? 1 : 0;
	$default = filter($_POST['default']);
	$module = filter($_POST['module'], 'module');
	$back = '{ADMIN}/xfields/';
	echo '<div id="content" class="animated fadeIn">';
	if(!empty($title) && !empty($description) && !empty($type)) 
	{
		if(isset($_POST['edit'])) 
		{
			$db->query("UPDATE `" . DB_PREFIX . "_xfields` SET `title` = '" . $title . "',`description` = '" . $description . "',`type` = '" . $type . "',`content` = '" . $default . "',`to_user` = '" . $to_user . "',`module` = '" . $module . "' WHERE `id` = " . $fid . ";");
			$adminTpl->info(_DOP_INFO_1, 'info', null, _BASE_INFO, _DOP_LIST, ADMIN.'/xfields');
		} 
		else 
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_xfields` (`title` ,`description` ,`type` ,`content` ,`to_user` ,`module` ) VALUES ('" . $db->safesql(processText($title)) . "', '" . $db->safesql(processText($description)) . "', '" . $type . "', '" . $default . "', '" . $to_user . "', '" . $module . "');");
			$adminTpl->info(_DOP_INFO_2, 'info', null, _BASE_INFO, _DOP_LIST, ADMIN.'/xfields');
		}
	} 
	else 
	{
		$adminTpl->info(_BASE_ERROR_0, 'error', null, _BASE_ERROR, _BASE_BACK, 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
		main();
	break;	
	
	case "add":
		xfields_add();
	break;
	
	case "save":
		xfields_save();
	break;
	
	case "delete":
		$id = intval($url[3]);
		$db->query("DELETE FROM `" . DB_PREFIX . "_xfields` WHERE `id` = " . $id . " LIMIT 1");
		location(ADMIN.'/xfields');
	break;
	
	case "edit":
		$id = intval($url[3]);
		xfields_add($id);
	break;
	
	case "action":
	$type = $_POST['submit'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case _DELETE:
				foreach($_POST['checks'] as $id) 
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_xfields` WHERE `id` = " . intval($id) . " LIMIT 1");
				}
			break;
		}
	}
	location(ADMIN.'/xfields');
	break;
}