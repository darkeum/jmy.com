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

function main()
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$adminTpl->admin_head($lang['xfields']);
	echo '<div id="content" class="animated fadeIn">';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_xfields ORDER BY id ASC");



	if($db->numRows($query) > 0)
	{
	echo '<div class="panel panel-dark panel-border top">
			<div class="panel-heading">
				<span class="panel-title">' . $lang['xfields_list'] . '</span>
				<div class="widget-menu pull-right" >
					<div class="btn-group">
						<button onclick="modal_o(\'#modal-form-help\')" type="button" class="btn btn-xs btn-alert btn-block">'.$lang['help'].'</button>
					</div>
					<div id="modal-form-help" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-info-circle"></i></span><span class="panel-title">'.$lang['help'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">'.$lang['xfields'].'</h3>
							<hr class="short alt">
							<p>'.$lang['xfields_help_1'].'</p>
							<hr class="short alt">
							<p>'.$lang['xfields_help_2'].'</p>
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
							<th class="col-md-2">' . $lang['title'] . '</th>
							<th class="col-md-2">' . $lang['description'] . '</th>
							<th class="col-md-4">' . $lang['xfields_temp'] . '</th>
							<th class="col-md-2">' . $lang['module'] .'</th>
							<th class="col-md-3">' . $lang['action'] . '</th>
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
				<td><input id="copy_'.$xfield['id'].'" type="text"  class="form-control input-sm" value="[xfield:' . $xfield['id'] . '][xfield_value:' . $xfield['id'] . '][/xfield:' . $xfield['id'] . ']" /></td>
				<td>' . _mName($xfield['module']) . '</td>
				<td>
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/xfields/edit/' . $xfield['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">';
						$command = 'document.querySelector(\'#copy_'.$xfield['id'].'\').select(); document.execCommand(\'copy\'); notif(\'primary\', \''. $lang['info'].'\', \''. $lang['copy_success'].'\');';
						echo '<li ><a href="'.$core->fullURL().'#copy" onclick="'.$command.'">'.$lang['xfields_copy'].'</a></li>
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$xfield['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$xfield['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[title]', $xfield['title'], $lang['xfields_delete_title']) .  '</h3>
							<hr class="short alt">
							<p>' . str_replace('[title]', $xfield['title'], $lang['xfields_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/xfields/delete/' . $xfield['id'] . '\'" class="btn btn-danger">' . $lang['delete'] .'</button>
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
	</div></div>';
	}

	else
	{
		$adminTpl->info($lang['xfields_empty'], 'empty', null, $lang['xfields_list'], $lang['xfields_add_dop'], ADMIN.'/xfields/add');
	}
	echo'</div>';
	$adminTpl->admin_foot();
}

function xfields_add($id = null)
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
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
		$title_lang = $lang['xfields_edit_dop'];
		$compl = $lang['edit'];
	}
	else
	{
		$title = '';
		$description = '';
		$default = '';
		$to_user = 1;
		$mod = 'news';
		$type = 0;
		$title_lang = $lang['xfields_add_dop'];
		$compl = $lang['add'];
	}
	$adminTpl->admin_head($title_lang);
	$validation_array = array(
		'title' => array(
			'required' =>  array('true', $lang['xfields_add_title_err'])
		),
		'description' => array(
			'required' =>  array('true',$lang['xfields_add_desc_err_1']),
			'maxlength' =>  array(200,  $lang['xfields_add_desc_err_2'])
		),
		'type' => array(
			'required' =>  array('true', $lang['xfields_add_type_err'])
		)
	);
	validationInit($validation_array);
	echo '<div id="content" class="animated fadeIn"><div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $title_lang .'</span>
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
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><input id="default" type="text" name="default" value="' . $default . '" placeholder="'.$lang['xfields_type_1_pre'].'" class="gui-input"></label></div></div>\';
		}
		else if(val == 2)
		{
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'.$lang['xfields_type_2_pre'].'" class="gui-textarea">' . $default . '</textarea></label></div></div>\';
		}
		else if(val == 3)
		{
			gid(\'typeExp\').innerHTML = \'<div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'.$lang['xfields_type_3_pre'].'" class="gui-textarea">' . $default . '</textarea><span class="input-footer">'.$lang['xfields_type_3_tt'].'</span></label></div></div>\';
		}
	}
	</script>
	<div class="panel-body admin-form">
		<form id="admin-form" action="{ADMIN}/xfields/save" method="post" name="xfields" role="form" class="form-horizontal parsley-form" data-parsley-validate>
			<div class="form-group">
				<label for="title"  class="col-lg-3 control-label">'. $lang['xfields_add_title'] .'</label>
				<div class="col-lg-4">
					<label for="title" class="field prepend-icon">
						<input id="title" type="text" name="title" value="'. $title .'" placeholder="'.$lang['xfields_add_title_pre'].'" class="gui-input">
						<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="description"  class="col-lg-3 control-label">'. $lang['xfields_add_desc'] .'</label>
				<div class="col-lg-4">
					<label for="description" class="field prepend-icon">
						<input id="description" type="text" name="description" value="'. $description .'" placeholder="'.$lang['xfields_add_desc_pre'].'" class="gui-input">
						<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="category"  class="col-lg-3 control-label">'. $lang['xfields_add_module'] .'</label>
				<div class="col-lg-3">
					<label class="field select">
						<select class="form-control" name="module" id="module" onchange="updateCatList(this.value, \'category\');" >';
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
			</div>';
			/*
			<div class="form-group">
				<label for="category"  class="col-lg-3 control-label">'. $lang['xfields_add_cat'] .'</label>
				<div class="col-lg-3">
					<label class="field select">
						<select name="category" id="category" >
							<option value="">' . $lang['xfields_add_cat_all'] . '</option>';
							$cats_arr = $core->aCatList();
							foreach ($cats_arr as $cid => $name)
							{
								$selected = ($cat_sl == $cid) ? "selected" : "";
								echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
							}
							echo '</select><i class="arrow double"></i>
					</label>
				</div>
			</div>*/
			echo '<div class="form-group">
				<label for="type"  class="col-lg-3 control-label">'. $lang['xfields_add_type'] .'</label>
				<div class="col-lg-3">
					<label class="field select">
						<select class="form-control" name="type" id="type" onchange="xfieldType(this.value); caa(this);">
							<option value="">'.$lang['xfields_type_0'].'</option>
							<option value="1" '. ($type == 1 ? 'selected' : '') .'>'.$lang['xfields_type_1'].'</option>
							<option value="2" '. ($type == 2 ? 'selected' : '') .'>'.$lang['xfields_type_2'].'</option>
							<option value="3" '. ($type == 3 ? 'selected' : '') .'>'.$lang['xfields_type_3'].'</option>
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
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><input id="default" type="text" name="default" value="' . $default . '" placeholder="'.$lang['xfields_type_1_pre'].'" class="gui-input"></label></div></div></div>';
					break;
					case 2:
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'.$lang['xfields_type_2_pre'].'" class="gui-textarea">' . $default . '</textarea></label></div></div></div>';
					break;
					case 3:
						echo '<div id="typeExp"><div class="form-group"><label for="default"  class="col-lg-3 control-label">'. $lang['xfields_add_def'] .'</label><div class="col-lg-4"><label for="default" class="field"><textarea name="default" id="full" placeholder="'.$lang['xfields_type_3_pre'].'" class="gui-textarea">' . $default . '</textarea><span class="input-footer">'.$lang['xfields_type_3_tt'].'</span></label></div></div></div>';
					break;
				}
			}
			echo '<div class="form-group">
					<label for="to_user"  class="col-lg-3 control-label">'. $lang['xfields_add_user'] .'</label>
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
	global $adminTpl, $db, $core, $lang;
	if(isset($_POST['edit']))
	{
		$adminTpl->admin_head($lang['xfields'].' | '.$lang['xfields_edits']);
	}
	else
	{
		$adminTpl->admin_head($lang['xfields'].' | '.$lang['xfields_adds']);
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
			$adminTpl->info($lang['xfields_info_1'], 'info', null, $lang['info'], $lang['xfields_list'], ADMIN.'/xfields');
		}
		else
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_xfields` (`title` ,`description` ,`type` ,`content` ,`to_user` ,`module` ) VALUES ('" . $db->safesql(processText($title)) . "', '" . $db->safesql(processText($description)) . "', '" . $type . "', '" . $default . "', '" . $to_user . "', '" . $module . "');");
			$adminTpl->info($lang['xfields_info_2'], 'info', null, $lang['info'], $lang['xfields_list'], ADMIN.'/xfields');
		}
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
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
	global $lang;
	$type = $_POST['submit'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case $lang['delete']:
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
