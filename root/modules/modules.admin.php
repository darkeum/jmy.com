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

$modArr[] = '';

$query = $db->query("SELECT * FROM ".DB_PREFIX."_plugins WHERE service='modules'");
while($_mod = $db->getRow($query))
{
	$modArr[] = $_mod['title'];

	if(!file_exists(ROOT.'usr/modules/'.$_mod['title'].'/index.php'))
	{
		delete($_mod['id']);
	}
}

$path = ROOT.'usr/modules/';
$dh = opendir($path);
while ($file = readdir($dh))
{
	if(!in_array($file, $modArr) && file_exists($path.$file.'/index.php'))
	{
		$db->query("INSERT INTO `" . DB_PREFIX . "_plugins` (`title` , `content` , `service`  , `active` ) VALUES ('" . $file . "', '" . ucfirst($file) . "', 'modules', '1');");
	}
	delcache('plugins');
}
closedir($dh);

function delete($id, $path = '') {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_plugins` WHERE `id` = " . $id . " LIMIT 1");
	if($path != '') full_rmdir(ROOT . 'usr/modules/'.$path.'/');
}

function retivate($id) {
global $adminTpl, $db;
	$db->query("UPDATE `" . DB_PREFIX . "_plugins` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
}

$server_domain = 'https://server.jmy.su/';
switch(isset($url[2]) ? $url[2] : null) {
	default:
		$adminTpl->admin_head(_MODULE_MODULE);
		echo '<div id="content" class="animated fadeIn">';
		if(isset($url[2]) && $url[2] == 'ok')
		{
			$adminTpl->info(_MODULE_INFO, 'info', null, _BASE_INFO);
		}
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_plugins WHERE service='modules' ORDER BY title ASC");
		if($db->numRows($query) > 0)
		{
			echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">' . _MODULE_LIST_INSTALL . ':</span>
              </div>
              <div class="panel-body pn">
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/modules/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-2">' . _TITLE . '</th>
							<th class="col-md-4">' . _DESCRIPTION .'</th>
							<th class="col-md-1">' . _MODULE_AP . '</th>
							<th class="col-md-2">' . _GROUP . '</th>
							<th class="col-md-4">' . _ACTIONS . '</th>
							<th class="col-md-1">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>
							</th>
						</tr>
                    </thead>
                    <tbody>';
			while($mod = $db->getRow($query))
			{
				echo '
				<tr>
					<td><span class="pd-l-sm"></span>' . $mod['id'] . '</td>
					<td>' . $mod['title'] . '</td>
					<td>' . $mod['content'] . '</td>
					<td>' . (file_exists(ROOT.'usr/modules/'.$mod['title'].'/admin/index.php') ? '<font color="green">Да</font>' : '<font color="red">Нет</font>') . '</td>
					<td>' . ($mod['groups'] == '' ? '<i>Все</i>' : $mod['groups']) . '</td>
					<td>
						<div class="btn-group">
							<button type="button" onclick="modal_o(\'#modal-retivate-'.$mod['id'].'\')" class="btn btn-xs btn-primary">'.(($mod['active'] == 0) ? _ACTIVATE : _DEACTIVATE).'</button>
							<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . _ACTIONS . '</span></button>
							<ul role="menu" class="dropdown-menu">
								<li><a href="{ADMIN}/modules/edit/'.$mod['id'].'">'._EDIT.'</a></li>
								<li><a href="">'._CAT_VIEW_CONTENT.'</a></li>
								<li class="divider"></li>
								<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-del-'.$mod['id'].'\')">' . _DELETE .'</a></li>
							</ul>
						</div>
						<div id="modal-retivate-'.$mod['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'._BASE_CONFIRM.'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . str_replace('[mod]', $mod['content'], ($mod['active'] == 0) ? _MODULE_ACTIVATE_TITLE : _MODULE_DEACTIVATE_TITLE) .'</h3>
								<hr class="short alt">
								<p>' . str_replace('[mod]', $mod['content'], ($mod['active'] == 0) ? _MODULE_ACTIVATE_TEXT : _MODULE_DEACTIVATE_TEXT) .  '</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" onclick="location.href = \'{ADMIN}/modules/retivate/'.$mod['id'].'\'" class="btn btn-warning">' . (($mod['active'] == 0) ? _MODULE_ACTIVATE : _MODULE_DEACTIVATE) .'</button>
							  </div>
							</div>
						</div>
						<div id="modal-del-'.$mod['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'._BASE_CONFIRM.'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . str_replace('[mod]', $mod['content'], _MODULE_DELETE_TITLE) .'</h3>
								<hr class="short alt">
								<p>' . str_replace('[mod]', $mod['content'], _MODULE_DELETE_TEXT) .  '</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" onclick="location.href = \'{ADMIN}/modules/delete/'.$mod['id'].'/'. $mod['title'].'\'" class="btn btn-danger">' . _DELETE .'</button>
							  </div>
							</div>
						</div>
					</td>
					<td>
						<div class="checkbox-custom mb15">
							<input id="checkbox' . $mod['id'] . '" type="checkbox" name="checks[]" value="' . $mod['id'] . '">
							<label for="checkbox' . $mod['id'] . '"></label>
						</div>
					</td>
				</tr>';
			}
			echo '</tbody>
				<tfoot class="footer-menu">
						<tr>
						  <td colspan="7">
							<nav class="text-right">
								<input name="submit" type="submit" class="btn btn btn-success" id="sub" value="' . _MODULE_REIVATE . '" />
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
			$adminTpl->info(_MODULE_LIST_EMPTY, 'error', null, _MODULE_LIST_INSTALL, _MODULE_LIST_EMPTY_HELP, 'http://jmy.su/');
		}
		echo'</div>';
		$adminTpl->admin_foot();
	break;

	case 'server':
		$adminTpl->admin_head(_MODULE_MODULE.' | '._MODULE_SERVER);
		$chmod_files = array('usr/modules', 'usr/blocks', 'usr/plugins');
		echo '<div id="content" class="animated fadeIn">';
		foreach($chmod_files as $dir)
		{
			if(!is_writable(ROOT.$dir))
			{
				$write_error[] = str_replace('[dir]', $dir, _MODULE_DIRW);
			}
		}
		if(!empty($write_error))
		{
			$adminTpl->alert('danger', _ATTETION, implode($write_error, '<br />'));
		}
		$query_search = '';
		if ($file_content = @file_get_contents($server_domain.'modules.php?cat_list'))
		{
			$cat_list = explode('|', $file_content);
			if(!empty($cat_list))
			{
				echo '<div class="panel panel-info panel-border top">
						<div class="panel-heading"><span class="panel-title">'._NAVIGATION.'</span>
					  </div>
					  <div class="panel-body">
						 <div class="row">
							<div class="col-md-4 col-lg-6">
								<div class="btn-group">';
									foreach($cat_list as $li)
									{
										$cl = '';
										$cat_li = explode('-', $li);
										switch ($cat_li[1]) {
										case "block":
											$cl = "primary";
											break;
										case "modules":
											$cl = "success";
											break;
										case "plagin":
											$cl = "warning";
											break;
										}
										echo '<button id="' . $cat_li[1] . '" type="button" onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/cat/' . $cat_li[1] . '\', \'_div\'); setbold(\'' . $cat_li[1] . '\');" class="btn btn-'.$cl.'">' . $cat_li[0] . '</button> ';
									}
								echo '</div>
								</div>
								<div class="col-md-4 col-lg-6">
										<form class="form-inline" role="form" align="right"  onsubmit="ajaxGet(\'' . ADMIN . '/modules/ajax/search/\'+gid(\'querys\').value, \'_div\'); return false;">
											<div class="form-group">
												<label class="sr-only" for="exampleInputEmail2">'._SEARCH.':</label>
												 <input type="text" name="query" id="querys" value="' . $query_search . '" class="form-control" >
											</div>
												<button type="submit" class="btn btn-default">'._SEARCH.'</button>
										 </form>
										 </div>
										 </div>
										</div>
									</div>
									<div id="_divi"></div>
									<div class="panel panel-dark panel-border top">
										<div class="panel-heading"><span class="panel-title">' . _MODULE_CATALOG . ':</span>
									</div>
									<div class="panel-body pn">
										<div id="_div">
											<div class="panel-heading" >'._AJAX_LOAD.'</div>
											<script type="text/javascript">ajaxGet(\'' . ADMIN . '/modules/ajax/main\', \'_div\');</script>
										</div>
									</div>';
			}
		}
		else
		{
			$adminTpl->info(_MODULE_NO_CONNECTION, 'error', null, _MODULE_CATALOG, _MODULE_LIST_EMPTY_HELP, 'http://jmy.su/');
		}
		echo '</div>';
		$adminTpl->admin_foot();
		break;

	case 'upload':
		$adminTpl->admin_head(_MODULE_MODULE.' | '._MODULE_UPLOAD);
		$chmod_files = array('usr/modules', 'usr/blocks', 'usr/plugins');
		echo '<div id="content" class="animated fadeIn">';
		foreach($chmod_files as $dir)
		{
			if(!is_writable(ROOT.$dir))
			{
				$write_error[] = str_replace('[dir]', $dir, _MODULE_DIRW);
			}
		}
		if(!empty($write_error))
		{
			$adminTpl->alert('danger', _ATTETION, implode($write_error, '<br />'));
		}
		echo '
		<div id="_div"></div>
		<div class="panel panel-dark panel-border top">
			<div class="panel-heading"><span class="panel-title">'._MODULE_UPLOAD.'</span>
		</div>
		<div class="panel-body">
			<form onsubmit="ajaxGet(\'' . ADMIN . '/modules/ajax/install/&in=\'+fixedEncodeURIComponent(gid(\'_url\').value), \'_div\'); return false;">
				<b>'._MODULE_UPLOAD_URL.'</b><br><br>
				<div class="form-group">
                    <input type="text" id="_url" name="_url" class="form-control" size=40" value="http://">
					<br>
					<font color="red">'._MODULE_UPLOAD_ATTATION.'</font>
                </div>
				<button type="submit" class="btn btn-info">'._MODULE_INSTALL.'</button>
            </form>
		</div>
		</div>';
		$adminTpl->admin_foot();
		break;


	case 'ajax':
		$versions[1] = VERSION_ID;
		switch(isset($url[3]) ? $url[3] : '')
		{
			default:
				$response = gzinflate(@file_get_contents($server_domain.'modules.php?main'));
				$cat_list = explode('|', @file_get_contents($server_domain.'modules.php?cat_list'));
				if(!empty($cat_list))
				{
					foreach($cat_list as $li)
					{
						$cat_li = explode('-', $li);
						$min[$cat_li[1]] = $cat_li[0];
					}
				}
				echo '
				<table class="table table-striped">
                    <thead>
						<tr>
							<th class="col-md-2"><span class="pd-l-sm"></span>'._TITLE.'</th>
							<th class="col-md-4">'._DESCRIPTION.'</th>
							<th class="col-md-2">'._AUTHOR.'</th>
							<th class="col-md-1">'._TYPE.'</th>
							<th class="col-md-1">'._BASE_VERSION.'</th>
							<th class="col-md-3">'._BASE_VERSION.' JMY CMS</th>
							<th class="col-md-1">' . _ACTIONS . '</th>
						</tr>
					</thead>
					<tbody>';
				$modules = unserialize($response);
				foreach($modules as $mod => $info)
				{
					$ajax_url = "'" . ADMIN . "/modules/ajax/install/&in='+fixedEncodeURIComponent('".$info['url']."'), '_divi'";
					echo '<tr>
								<td><span class="pd-l-sm"></span>'.$info['title'].'</td>
								<td>'.$info['description'].'</td>
								<td>'.$info['author'].'</td>
								<td>'.$info['cat'].'</td>
								<td>'.$info['version'].'</td>
								<td>'.$info['version_jmy'].'</td>
								<td>'.
									(isset($core->tpl->modules[$mod]) ? '<button onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/delete/' . $mod . '/' . $core->tpl->modules[$mod]['id'] . '\', \'_divi\');" type="button" class="btn btn-xs btn-danger ">'._DELETE.'</button>' : '<button onclick="ajaxGet('.$ajax_url.');" type="button" class="btn btn-xs btn-success ">'._BASE_INSTALL.'</button>').'
								</td>
							</tr>';
				}
				echo '</tbody></table>';
				break;

			case 'cat':
				if(isset($url[4]))
				{
					$response ='';
					if (file_get_contents($server_domain.'modules.php?cat_'.$url[4]) != 'empty')
					{
					$response = gzinflate(@file_get_contents($server_domain.'modules.php?cat_'.$url[4]));
					}
					if(empty($response))
					{
						echo '<div class="panel-heading" >Раздел не сушествует или он пуст!</div>';
					}
					else
					{
						$cat_list = explode('|', @file_get_contents($server_domain.'modules.php?cat_list'));
						if(!empty($cat_list))
						{
							foreach($cat_list as $li)
							{
								$cat_li = explode('-', $li);
								$min[$cat_li[1]] = $cat_li[0];
							}
						}
						echo '<table class="table no-margin">
										<thead>
											<tr>
												<th class="col-md-2"><span class="pd-l-sm"></span>Название</th>
												<th class="col-md-4">Описание</th>
												<th class="col-md-2">Автор</th>
												<th class="col-md-1">Тип</th>
												<th class="col-md-1">Версия</th>
												<th class="col-md-3">Версия JMY CMS</th>
												<th class="col-md-1">' . _ACTIONS . '</th>
											</tr>
										</thead>
										<tbody>';
						$modules = unserialize($response);
						foreach($modules as $mod => $info)
						{
							echo '<tr>
								<td><span class="pd-l-sm"></span>'.$info['title'].'</td>
								<td>'.$info['description'].'</td>
								<td>'.$info['author'].'</td>
								<td>'.$info['cat'].'</td>
								<td>'.$info['version'].'</td>
								<td>'.$info['version_jmy'].'</td>
								<td><a href="javascript:;" class="btn btn-success btn-xs">Установить</a></td>
							</tr>';
							/*
							echo '<div class="_module_boxes"><div class="_buts">' . (isset($core->tpl->modules[$mod]) ? '<div class="_but _butdel"><a href="javascript:void(0)" onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/delete/' . $mod . '/' . $core->tpl->modules[$mod]['id'] . '\', \'_div\');">Удалить</a></div>' : '<div class="_but"><a href="javascript:void(0)" onclick="gid(\'_div\').innerHTML = \'Идёт установка модуля...\'; ajaxGet(\'' . ADMIN . '/modules/ajax/install/' . $mod . '\', \'_div\');">Установить</a></div>') . '<div class="_version ' . (!empty($info['for_toogle']) && $info['for_toogle'] != VERSION_ID ? (VERSION_ID > $info['for_toogle'] ? '_version2' : '_butdel') : (!empty($info['for_toogle']) ? '' : '_version3')) . '">' . (isset($info['for_toogle']) && isset($versions[$info['for_toogle']]) ? $versions[$info['for_toogle']] : 'N/A') . '</div></div><div class="_module_title">' . $info['title'] . ' (' . $mod . ')</div><p>' . $info['description'] . '</p><b>Версия модуля:</b> ' . $info['version'] . ($url[4] == 'all' ? '<br /><b>Раздел:</b> <a href="javascript:void(0)" onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/cat/' . $info['cat'] . '\', \'_div\'); setbold(\'' . $info['cat'] . '\'); bold = \'' . $info['cat'] . '\';">'.$min[$info['cat']].'</a>' : '') . '</div>';*/
						}
						echo '<script type="text/javascript">var bold = \'\'; function setbold(id) { if(bold != \'\') {gid(bold).style.fontWeight=\'normal\'; }  gid(id).style.fontWeight=\'bold\'; } </script>';
					}
				}
				else
				{
						echo '<div class="panel-heading" >Раздел не сушествует или он пуст!</div>';
				}
				break;

			case 'search':
				if(isset($url[4]))
				{
					if(!empty($url[4]))
					{
						$response = gzinflate(@file_get_contents($server_domain.'server.php?search='.urlencode($url[4])));
						$search = unserialize($response);
						if(empty($search))
						{
							echo '<div class="_module_cat">Поиск по запросу "' . $url[4] . '"</div>';
							echo '<div class="_inff _redinf" style="margin:0;"><span style="font-size:14px; font-weight:bold;">Поиск завершён</span><br />К сожалению ни одной записи не найдено.</div>';
						}
						else
						{
							$cat_list = explode('|', @file_get_contents($server_domain.'module_categories.text'));
							if(!empty($cat_list))
							{
								foreach($cat_list as $li)
								{
									$cat_li = explode('-', $li);
									$min[$cat_li[1]] = $cat_li[0];
								}
							}
							echo '<div class="_module_cat">Поиск по запросу "' . $url[4] . '", ' . count($search) . ' результ.</div>';
							foreach($search as $mod => $info)
							{
								echo '<div class="_module_boxes"><div class="_buts">' . (isset($core->tpl->modules[$mod]) ? '<div class="_but _butdel"><a href="javascript:void(0)" onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/delete/' . $mod . '/' . $core->tpl->modules[$mod]['id'] . '\', \'_div\');">Удалить</a></div>' : '<div class="_but"><a href="javascript:void(0)" onclick="gid(\'_div\').innerHTML = \'Идёт установка модуля...\'; ajaxGet(\'' . ADMIN . '/modules/ajax/install/' . $mod . '\', \'_div\');">Установить</a></div>') . '<div class="_version ' . (!empty($info['for_toogle']) && $info['for_toogle'] != VERSION_ID ? (VERSION_ID > $info['for_toogle'] ? '_version2' : '_butdel') : (!empty($info['for_toogle']) ? '' : '_version3')) . '">' . (isset($info['for_toogle']) && isset($versions[$info['for_toogle']]) ? $versions[$info['for_toogle']] : 'N/A') . '</div></div><div class="_module_title">' . $info['title'] . ' (' . $mod . ')</div><p>' . $info['description'] . '</p><b>Версия модуля:</b> ' . $info['version'] . ($url[4] == 'all' ? '<br /><b>Раздел:</b> <a href="javascript:void(0)" onclick="ajaxGet(\'' . ADMIN . '/modules/ajax/cat/' . $info['cat'] . '\', \'_div\'); setbold(\'' . $info['cat'] . '\'); bold = \'' . $info['cat'] . '\';">'.$min[$info['cat']].'</a>' : '') . '</div>';
							}
							echo '<script type="text/javascript">var bold = \'\'; function setbold(id) { if(bold != \'\') {gid(bold).style.fontWeight=\'normal\'; }  gid(id).style.fontWeight=\'bold\'; } </script>';
						}
					}
					else
					{
						echo '<div class="_module_cat">Поиск по модулям</div>';
						echo '<div class="_inff _redinf" style="margin:0;"><span style="font-size:14px; font-weight:bold;">Произошла ошибка</span><br />Поисковый запрос не может быть пустым</div>';
					}
				}
				break;

			case 'install':
				global $config, $adminTpl;
				if(!empty($_REQUEST['in']))
				{
					$load_url=$_REQUEST['in'];
					$load_url = str_replace('|', '/', $load_url);
					if(eregStrt('.zip', $load_url))
					{
						$zip = $load_url;
						$arr[1] = basename($zip);
						$arr[2] = basename($zip, ".zip");
					}
					if(!empty($arr[1]) && !isset($core->tpl->modules[$arr[2]]) && ($file_content = @file_get_contents($zip)))
					{
						$file = fopen (ROOT."tmp/temp_install.zip", "w");
						fputs ($file, $file_content);
						fclose ($file);
						require_once(ROOT.'boot/sub_classes/pclzip.lib.php');
						$archive = new PclZip(ROOT."tmp/temp_install.zip");
						if($v_result_list = $archive->extract(PCLZIP_OPT_PATH, ROOT) == 0)
						{
							$adminTpl->admin_head();
							$adminTpl->alert('warning', _ERROR, _MODULE_NO_ARCHIVE);
						}
						else
						{
							if(file_exists(ROOT.'usr/modules/' . $arr[2] . '/sql.sql'))
							{
								$sql = @file_get_contents(ROOT.'usr/modules/' . $arr[2] . '/sql.sql');
								if(!empty($sql))
								{
									$sql_create_massiv = explode(";", $sql);
									foreach($sql_create_massiv as $query)
									{
										preg_match('#`\[prefix\](.*)`#i', $query, $name);
										if(preg_match('#CREATE#i', $query))
										{
											if(!eregStrt('[prefix]_users', $sql))
											{
												$db->query(str_replace('[prefix]', DB_PREFIX, $query));
											}
											$sql = true;
										}
									}
								}
							}
							if (file_exists(ROOT.'usr/modules/' . $arr[2] . '/'))
							{
								$file = fopen (ROOT."usr/modules/" . $arr[2] . "/report.txt", "w");
								fputs ($file, serialize($v_result_list));
								fclose ($file);
							}
							if(file_exists(ROOT.'usr/tpl/temp_tpl'))
							{
								copydir(ROOT.'usr/tpl/temp_tpl', ROOT.'usr/tpl/'.$config['tpl']);
								rmdir_rf(ROOT.'usr/tpl/temp_tpl');
							}
							$db->query("INSERT INTO `" . DB_PREFIX . "_plugins` (`title` , `content` , `service`  , `menu` , `active`) VALUES ('" . $db->safesql($arr[2]) . "', '" . $db->safesql($arr[2]) . "', 'modules', '1', '1');");
							delcache('plugins');
							$adminTpl->admin_head();
							$adminTpl->alert('success', _BASE_INFO, _MODULE_INSTALL_COMPL.(isset($sql) ? '<br>'._MODULE_INSTALL_BD : ''));
						}
						@unlink(ROOT."tmp/temp_install.zip");
					}
					else
					{
						$adminTpl->admin_head();
						$adminTpl->alert('warning', _ERROR, _MODULE_INSTALL_ERROR);
					}
				}
				break;

			case 'delete':
				if(isset($url[4]) && isset($url[5]))
				{
					delete(intval($url[5]), $url[4]);
					$adminTpl->admin_head();
					$adminTpl->alert('success', _MODULE_DELETE, _MODULE_DELETE_COMPL);
				}
				break;
		}
		break;


	case 'edit':
		if(isset($url[3]))
		{
			$modId = $url[3];
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_plugins WHERE id = '" . $modId . "'");
			$module = $db->getRow($query);
			$title = $module['content'];
			$groups = explode(',', $module['groups']);
			$unshow = explode(',', $module['unshow']);
		}
		else
		{
			location();
		}

		$adminTpl->admin_head('Модули | Редактировать модуль');
		echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>Редактирование модуля: '.$title.'</b></div><div class="panel-body"><div class="switcher-content"><form action="{ADMIN}/modules/save" method="post" name="news" role="form" class="form-horizontal parsley-form" data-parsley-validate="" novalidate="">

		<div class="form-group">
					<label class="col-sm-3 control-label">Описание модуля:</label>
					<div class="col-sm-4">
					<input type="text" size="20" name="title" class="textinput" value="'.$title.'" maxlength="100" maxsize="100" />
					</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">'._GROUP_ACCESS.'</label>
			<div class="col-sm-4">
			<select name="groups[]" class="cat_select" multiple ><option value="" ' . (empty($groups) ? 'selected' : '') . '>Все группы</option>';
			$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
			while($rows = $db->getRow($query))
			{
				$selected = in_array($rows['id'], $groups) ? "selected" : "";
				echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
			}
		echo '</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><b>НЕ</b>отображать блоки:</label>
			<div class="col-sm-4">
			<select name="type[]" class="cat_select" multiple>';
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_blocks_types ORDER BY type");
			while($rows = $db->getRow($query))
			{
				$selected = in_array($rows['type'], $unshow) ? "selected" : "";
				echo '<option value="' . $rows['type'] . '" ' . $selected . '>' . $rows['title'] . ' [' . $rows['type'] . ']</option>';
			}
		echo '</select>
			</div>
		</div>';
		if(isset($modId))
		{
			echo '<input type="hidden" name="id" value="' . $modId . '">';
		}
		echo '<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. _UPDATE .'">
					</div>
		</div></form></div></div></section></div></div>';

		$adminTpl->admin_foot();
		break;


	case 'save':
		$id = isset($_POST['id']) ? intval($_POST['id']) : '';
		$title = isset($_POST['title']) ? filter($_POST['title'], 'title') : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$groups = isset($_POST['groups']) ? $_POST['groups'] : false;

		$g = 0;
		$groupList = '';
		if(!empty($groups))
		{
			foreach($groups as $group)
			{
				if(trim($group) !== '')
				{
					$g++;
					if($g == 1)
					{
						$groupList = $group;
					}
					else
					{
						$groupList .= ',' . $group;
					}
				}
			}
		}

		$d = 0;
		$deList = '';
		if(!empty($type))
		{
			foreach($type as $typ)
			{
				if(trim($typ) !== '')
				{
					$d++;
					if($d == 1)
					{
						$deList = $typ;
					}
					else
					{
						$deList .= ',' . $typ;
					}
				}
			}
		}

		$adminTpl->admin_head('Модули системы | Редактирование');
		if(!empty($title))
		{
			$db->query("UPDATE `" . DB_PREFIX . "_plugins` SET `content` = '" . $title . "' , `unshow` = '" . $deList . "', `groups` = '" . $groupList . "' WHERE `id` =" . $id . " LIMIT 1 ;");
			delcache('plugins');
			$adminTpl->info('Модуль успешно обновлён. <a href="{ADMIN}/modules">Просмотреть список модулей</a>');
		}
		else
		{

		}

		$adminTpl->admin_foot();

		break;


	case "delete":
		$id = intval($url[3]);
		$path = filter($url[4]);
		delete($id, $path);
		delcache('plugins');
		location(ADMIN.'/modules/ok');
	break;

	case "retivate":
		$id = intval($url[3]);
		retivate($id);
		delcache('plugins');
		location(ADMIN.'/modules/ok');
	break;

	case 'action':
		foreach($_POST['checks'] as $id)
		{
			retivate(intval($id));
		}
		delcache('plugins');
		location(ADMIN.'/modules/ok');
		break;

}
