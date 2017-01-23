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
	$adminTpl->admin_head($lang['cats']);
	echo '<div id="content" class="animated fadeIn">';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories ORDER BY id ASC, parent_id ASC");
	if($db->numRows($query) > 0)
	{
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">' . $lang['cats_list'] . ':</span>
              </div>
              <div class="panel-body pn">
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/cats/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-2">' . $lang['title'] . '</th>
							<th class="col-md-1">' . $lang['module'] . '</th>
							<th class="col-md-3">' . $lang['description'] .'</th>
							<th class="col-md-2">' . $lang['url'] . '</th>
							<th class="col-md-1">' . $lang['position'] . '</th>
							<th class="col-md-1">' . $lang['icon'] . '</th>
							<th class="col-md-4">' . $lang['action'] . '</th>
							<th class="col-md-1">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>
							</th>
						</tr>
                    </thead>
                    <tbody>';
		while($cat = $db->getRow($query))
		{
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $cat['id'] . '</td>
				<td>' . $core->getCat($cat['module'], $cat['id'], 'short', 1) . '</td>
				<td>' . _mName($cat['module']) . '</td>
				<td>' . (empty($cat['description']) ? $lang['no'] : str($cat['description'], 17)) . '</td>
				<td>' . $cat['altname'] . '</td>
				<td>' . $cat['position'] . '</td>
				<td>' . (empty($cat['icon']) ? $lang['no'] : $lang['yes']) . '</td>
				<td>
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/cats/edit/'.$cat['id'].'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">
							<li><a target="_blank" href="'.$core->getCat($cat['module'], $cat['id'], 'url', 1).'">'.$lang['cats_view'].'</a></li>
							<li><a href="'.ADMIN.'/module/'.$cat['module'].'/cat/'.$cat['altname'].'">'.$lang['cats_view_content'].'</a></li>
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$cat['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$cat['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[cat]', $cat['name'], $lang['cats_delete_title']) .  '</h3>
							<hr class="short alt">
							<p>' . str_replace('[cat]', $cat['name'], $lang['cats_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/cats/delete/'.$cat['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $cat['id'] . '" type="checkbox" name="checks[]" value="' . $cat['id'] . '">
						<label for="checkbox' . $cat['id'] . '"></label>
					</div>
				</td>
			</tr>';
		}
		echo '	</tbody>
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
		$adminTpl->info($lang['cats_empty'], 'empty', null, $lang['cats_list'], $lang['cats_add'], ADMIN.'/cats/add');
	}
	echo'</div>';
	$adminTpl->admin_foot();
}

function add($cat = null)
{
global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($cat))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE id = '" . $cat . "'");
		$ccid = $cat;
		$cat = $db->getRow($query);
		$name = prepareTitle($cat['name']);
		$altname = $cat['altname'];
		$description = $cat['description'];
		$keywords = $cat['keywords'];
		$icon = !isset($cat['icon']) ? $cat['icon'] : 'no.png';
		$pid = $cat['parent_id'];
		$mod = $cat['module'];
		$lnn = $lang['cats_edit'];
		$remote = ADMIN.'/cats/ajax/iscat/update';
	}
	else
	{
		$name = isset($_POST['name']) ? filter(trim($_POST['name'])) : '';
		$altname = isset($_POST['altname']) ? ($_POST['altname'] == '') ? translit($name) : translit($_POST['altname']) : '';
		$description = isset($_POST['description']) ? filter($_POST['description']) : '';
		$keywords = isset($_POST['keywords']) ? filter($_POST['keywords'], 'a') : '';
		$icon = isset($_POST['icon']) ? filter($_POST['icon'], 'a') : 'no.png';
		$pid = false;
		$mod = 'news';
		$lnn = $lang['cats_add'];
		$remote = ADMIN.'/cats/ajax/iscat';
	}
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		'name' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'altname' => array(
			'required' =>  array('true',  $lang['cats_url_err_1']),
			'remote' =>  array($remote,  $lang['cats_url_err_2'])
		),
		'description' => array(
			'maxlength' =>  array(200,  $lang['cats_descr_err'])
		),
		'keywords' => array(
			'maxlength' =>  array(255, $lang['cats_keys_err'])
		)
	);
	validationInit($validation_array);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/cats/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="name"  class="col-lg-3 control-label">'. $lang['cats_name'] .'</label>
						<div class="col-lg-4">
							<label for="name" class="field prepend-icon">
								<input id="name" type="text" name="name" value="'. $name .'" placeholder="'.$lang['cats_name_pre'].'" class="gui-input" '. (!isset($cat) ? "onchange=\"getTranslit(gid('name').value, 'altname');\"" : "" ) .'>
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="altname"  class="col-lg-3 control-label">'. $lang['cats_url'] .'</label>
						<div class="col-lg-4">
							<label for="altname" class="field prepend-icon">
								<input id="altname" type="text" name="altname" value="'. $altname .'" placeholder="'.$lang['cats_url_pre'].'" class="gui-input">
								<label for="altname" class="field-icon"><i class="fa fa-link"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="description"  class="col-lg-3 control-label">'. $lang['cats_descr'] .'</label>
						<div class="col-lg-4">
							<label for="description" class="field prepend-icon">
								<input id="description" type="text" name="description" value="'. $description .'" placeholder="'.$lang['cats_descr_pre'].'" class="gui-input">
								<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="keywords"  class="col-lg-3 control-label">'. $lang['cats_keys'] .'</label>
						<div class="col-lg-4">
							<label for="keywords" class="field prepend-icon">
								<input id="keywords" type="text" name="keywords" value="'. $keywords .'" placeholder="'.$lang['cats_keys_pre'].'" class="gui-input">
								<label for="keywords" class="field-icon"><i class="fa fa-star-o"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="icon"  class="col-lg-3 control-label">'. $lang['cats_icon'] .'</label>
						<div class="col-lg-3">
								<label class="field select">';
									$path = 'media/cats/';
									$dh = opendir(ROOT . $path);
									echo '<select name="icon" id="icon" onchange="changeIcon(\'' . $path . '\' + this.value, \'iconImg\')" ><option value="">' . $lang['cats_icon_no'] . '</option>';
									while ($file = readdir($dh))
									{
										if(is_file(ROOT . $path.$file) && $file != '.' && $file != '..' && $file != 'no.png')
										{
											$selected = ($icon == $file) ? "selected" : "";
											echo '<option value="' . $file . '" ' . $selected . '>' . $file . '</option>';
										}
									}
									echo '</select><i class="arrow double"></i>
								</label>
						</div>
						<div class="col-lg-3">
							<img width="32" height="32" src="' . $path . $icon . '" border="0" id="iconImg" />
						</div>
					</div>
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'. $lang['cats_incat'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select name="category" id="category" >
										<option value="">' .  $lang['cats_incat_no'] . '</option>';
											$cats_arr = $core->aCatList();
											foreach ($cats_arr as $cid => $name) {
												$selected = ($pid == $cid) ? "selected" : "";
												echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
											}
								echo '</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'.  $lang['cats_module'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select class="form-control" name="module" id="module" onchange="updateCatList(this.value, \'category\');" >';
										foreach ($core->getModList() as $module)
										{
											if(in_array($module, exceMods('categories')))
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
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. (isset($cat) ?  $lang['update'] :  $lang['add']) .'" />
						</div>
					</div>';
					if(isset($ccid))
					{
						echo '
							<script>
								$(\'#admin-form\').submit(function (e) {
									var form = this;
									e.preventDefault();
									setTimeout(function () {
										form.submit();
									}, 1000); // in milliseconds
								});
								</script>
							<input type="hidden" name="edit" value="1">
							<input type="hidden" name="cid" value="' . $ccid . '">';
					}
	echo '</form></div></div>';
	$adminTpl->admin_foot();
}

function cat_save() {
global $adminTpl, $db, $core, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
	$name = filter($_POST['name'], 'a');
	$altname = ($_POST['altname'] == '') ? translit($name) : str_replace(array('-', ' '), array('_', '_'), $_POST['altname']);
	$description = filter($_POST['description']);
	$keywords = filter($_POST['keywords'], 'a');
	$icon = filter($_POST['icon'], 'a');
	$module = filter($_POST['module'], 'module');
	$pid = intval($_POST['category']);
	echo '<div id="content" class="animated fadeIn">';
	if(!empty($name) && !empty($altname))
	{
		if(isset($_POST['edit']))
		{
			$db->query("UPDATE `" . DB_PREFIX . "_categories` SET `name` = '" . $db->safesql(processText($name)) . "', `altname` = '" . $altname . "', `description` = '" . $db->safesql(processText($description)) . "', `keywords` = '" . $db->safesql(processText($keywords)) . "', `module` = '" . $module . "', `icon` = '" . $icon . "', `parent_id` = '" . $pid . "' WHERE `id` =".$cid."  LIMIT 1");
			$adminTpl->info($lang['cats_edit_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
		}
		else
		{
			if($db->query("INSERT INTO `" . DB_PREFIX . "_categories` ( `id` , `name` , `altname` , `description` , `keywords` , `module` , `icon` , `position` , `parent_id` ) VALUES (NULL, '" . $db->safesql(processText($name)) . "', '" . $altname . "', '" . $db->safesql(processText($description)) . "', '" . $keywords . "', '" . $module . "', '" . $icon . "', '0', '" . $pid . "');"))
			$adminTpl->info($lang['cats_add_success'], 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
		}
		if(file_exists(ROOT . 'tmp/cache/categories.cache'))
		{
			@unlink(ROOT . 'tmp/cache/categories.cache');
		}
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

function scan($cat = null) {
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_fast_add']);
	$validation_array = array(
		'full' => array(
			'required' =>  array('true', $lang['cats_fast_list_err'])
		)
	);
	validationInit($validation_array);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lang['cats_fast_add'] .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/cats/save_scan" method="post" name="news" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="category"  class="col-lg-3 control-label">'. $lang['cats_module'] .'</label>
						<div class="col-lg-3">
								<label class="field select">
									<select class="form-control" name="module" id="module">';
										foreach ($core->getModList() as $module)
										{
											if(in_array($module, exceMods('categories')))
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
						<label class="col-sm-3 control-label">'. $lang['cats_fast_list'] .'</label>
						<div class="col-sm-4">
							<label for="comment" class="field prepend-icon">
								<textarea name="full" id="full" placeholder="'.$lang['cats_fast_list_pre'].'" class="gui-textarea"></textarea>
								<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">'.$lang['cats_fast_list_tt'].'</span>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input name="submit" type="submit" class="btn btn-primary btn-parsley" value="'. $lang['add'] .'"/>
						</div>
					</div>
				</form>
			</div>
		</div>';
	$adminTpl->admin_foot();
}

function save_scan() {

global $adminTpl, $db, $core, $lang;
	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	echo '<div id="content" class="animated fadeIn">';
	$module = filter($_POST['module'], 'module');
	$full = explode("\n", $_POST['full']);
	$info = '';
	if (!empty($_POST['full']))
	{
		foreach($full as $cat)
		{
			if($cat !== '' && !is_array($cat))
			{
				$name = filter($cat);
				$altname = translit($name);
				$db->query("INSERT INTO `" . DB_PREFIX . "_categories` (`name` , `altname` ,  `module` ) VALUES ('" . $db->safesql($name) . "', '" . $altname . "', '" . $module . "');");
				$info .= str_replace(array('[name]', '[altname]'), array($name, $altname), $lang['cats_fast_save']);
			}
		}
		$adminTpl->info($info, 'info', null, $lang['info'], $lang['cats_list'], ADMIN.'/cats');
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	@unlink(ROOT . 'tmp/cache/categories.cache');
	$adminTpl->admin_foot();
}

function delete($id) {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_categories` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("DELETE FROM `" . DB_PREFIX . "_news` WHERE `cat` like '%," . $id . ",%'");
	if(file_exists(ROOT . 'tmp/cache/categories.cache'))
	{
		@unlink(ROOT . 'tmp/cache/categories.cache');
	}
}

switch(isset($url[2]) ? $url[2] : null) {
	default:
		main();
	break;

	case "add":
		add();
	break;

	case "save":
		cat_save();
	break;

	case "delete":
		$id = intval($url[3]);
		delete($id);
		header('Location: /'.ADMIN.'/cats');
	break;

	case "edit":
		$id = intval($url[3]);
		add($id);
	break;

	case "action":
	$type = $_POST['submit'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case _DELETE:
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
			break;
		}
	}
		header('Location: /'.ADMIN.'/cats');
	break;

	case "scan":
		scan();
	break;

	case "save_scan":
		save_scan();
	break;

	case 'ajax':
		global $adminTpl, $db, $lang;
			ajaxInit();
			$type = $url[3];
			switch($type)
			{
				case "iscat":

					if(isset($_POST['altname']))
					{
						if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['altname']))
						{
							echo(json_encode($lang['cats_url_err_3']));
						}
						else
						{
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE altname = '" . $db->safesql($_POST['altname']) . "'");
							if(($db->numRows($query) > 0) && ($url[4] != 'update'))
							{
								echo 'false';
							}
							else
							{
								echo 'true';
							}
						}
					}
				break;
			}
		break;
}
