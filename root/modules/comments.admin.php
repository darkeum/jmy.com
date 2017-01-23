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
switch(isset($url[2]) ? $url[2] : null)
{
	default:
		$page = init_page();
		$cut = ($page-1)*$admin_conf['num'];
		$where = '';
		$textup =_COM_LIST;
		$adminTpl->admin_head($lang['comments']);
		$where = ' WHERE c.status=\'1\'';
		echo '<div id="content" class="animated fadeIn">';
		if(isset($url[2]) && $url[2] == 'ok')
		{
			$adminTpl->alert('success', $lang['info'], $lang['action_success']);
		}
		$query = $db->query("SELECT c.*, u.nick, u.group, u.last_visit FROM ".DB_PREFIX."_comments as c LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u on (c.uid=u.id) " . $where . " ORDER BY date DESC LIMIT $cut,".$admin_conf['num']);
		if($db->numRows($query) > 0)
		{
			echo '<div class="panel panel-dark panel-border top">
					<div class="panel-heading">
							<span class="panel-title">' . $lang['comments_list'] . ':</span>
					</div>
					 <div class="panel-body pn table-responsive">
						<form id="tablesForm" method="POST" action="{ADMIN}/comments/action">
							<table class="table table-striped">
								<thead>
									<tr>
										<th><span class="pd-l-sm"></span>#</th>
										<th class="w250">' . $lang['comment'] . '</th>
										<th>' . $lang['module'] . '</th>
										<th>' . $lang['date'] .'</th>
										<th>' . $lang['links'] .'</th>
										<th class="w150">' . $lang['author'] . '</th>
										<th class="text-center">' . $lang['status'] .'</th>
										<th>' . $lang['actions'] . '</th>
										<th class="text-right">
											<div class="checkbox-custom mb15">
												<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
												<label for="all"></label>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>';
			while($comment = $db->getRow($query))
			{
				$tt = str(htmlspecialchars(strip_tags($comment['text'])), 30);
				if ($comment['status'] == 1)
				{
					$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
				}
				else
				{
					$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
				}
				echo '
				<tr>
					<td><span class="pd-l-sm"></span>' . $comment['id'] . '</td>
					<td>' . (($tt != '') ? $tt : '<font color="red">'.$lang['no_text'].'</font>') . '</td>
					<td>' . commentLink($comment['module'], $comment['post_id']) . '</td>
					<td>' . formatDate($comment['date'], true) . '</td>
					<td>' .  (eregStrt('href', $comment['text']) ? '<font color="red">'.$lang['yes'].'</font>' : '<font color="green">'.$lang['no'].'</font>') . '</td>
					<td>' . (($comment['uid'] != 0) ? '<a href="profile/' . $comment['nick'] . '" title="' . $comment['nick'] . '">' . $comment['nick'] . '</a>' : $comment['gname']) . '</td>
					<td class="text-center">' . $status_icon . '</td>
						<td>
						<div class="btn-group">
							<button type="button" onclick="location.href = \'{ADMIN}/comments/edit/'.$comment['id'].'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
							<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
							<ul role="menu" class="dropdown-menu">
								<li><a href="{ADMIN}/comments/reactivate/'.$comment['id'].'">' . $lang['do_moderation'] . '</a></li>
								<li class="divider"></li>
								<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$comment['id'].'\')">' . $lang['delete'] .'</a></li>
							</ul>
						</div>
						<div id="modal-form-'.$comment['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . str_replace('[comment]', $tt, $lang['comments_delete_title']) .'</h3>
								<hr class="short alt">
								<p>' . str_replace('[comment]', $tt, $lang['comments_delete_text']) .  '</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" onclick="location.href = \'{ADMIN}/comments/delete/'.$comment['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
							  </div>
							</div>
						  </div>
					</td>
					<td class="text-right">
						<div class="checkbox-custom mb15">
							<input id="checkbox' . $comment['id'] . '" type="checkbox" name="checks[]" value="' . $comment['id'] . '">
							<label for="checkbox' . $comment['id'] . '"></label>
						</div>
					</td>
				</tr>';

			}
			echo '</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">
					  <td align="right" colspan="10">
                        <nav align="right" class="text-right">
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">
								<option value="deActivate">' . $lang['do_moderation'] . '</option>
								<option value="delete">' . $lang['delete'] . '</option>
							</select>
							<input name="submit" type="submit" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>
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
			$adminTpl->info($lang['comments_empty'], 'empty', null, $lang['comments_list']);
		}
		echo'</section></div></div>';
		$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_comments " . str_replace('c.', '', $where));
		$all = $db->numRows($all_query);
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/comments/{page}');
		echo'</div>';
		$adminTpl->admin_foot();
		break;

	case 'edit':
		$commId = intval($url[3]);
		if($commId != 0)
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_comments WHERE id = '" . $commId . "'");
			$comment = $db->getRow($query);

			if($comment['uid'] != 0)
			{
				list($nick) = $db->fetchRow($db->query("SELECT nick FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE id = " . $comment['uid'] . " LIMIT 1"));
			}
		}
		else
		{
			location(ADMIN);
		}
		$bb = new bb;
		$adminTpl->admin_head(_COM_COM . ' | ' ._COM_EDIT);
		echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>'. _COM_EDIT .'</b></div><div class="panel-body"><div class="switcher-content">
		<form action="{ADMIN}/comments/save" method="post" name="news" role="form" class="form-horizontal parsley-form" data-parsley-validate>';
		if($comment['uid'] == 0)
		{
			echo '<div class="form-group">
					<label class="col-sm-3 control-label">'. _BASE_GUEST_NAME .'</label>
					<div class="col-sm-4">
						<input type="text" name="gname"  value="'. $comment['gname'] .'" class="form-control" id="gname"  data-parsley-required="true" data-parsley-trigger="change">
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-3 control-label">'. _BASE_GUEST_MAIL .'</label>
					<div class="col-sm-4">
						<input type="text" name="gemail"  value="'. $comment['gemail'] .'" class="form-control" id="exampleInputEmail2"  data-parsley-required="true" data-parsley-trigger="change">
					</div>
				  </div>';
		}
		else
		{
			echo '<div class="form-group">
					<label class="col-sm-3 control-label">'. _BASE_USER_NAME .'</label>
					<div class="col-sm-4">
						<a target="_blank" href="profile/' . $nick . '"><p class="form-control-static">' . $nick .'</p></a>
					</div>
				  </div>';
		}
		echo '<div class="form-group">
					<label class="col-sm-3 control-label">'. _COM_ID .'</label>
					<div class="col-sm-4">
						<p class="form-control-static">' . $comment['post_id'] .'</p>
					</div>
			  </div>
			  <div class="form-group">
					<label class="col-sm-3 control-label">'. _MODULE .'</label>
					<div class="col-sm-4">
						<select class="form-control" name="module" id="module" onchange="updateCatList(this.value, \'category\');">';		
		foreach ($core->getModList() as $module) {
			$selected = ($module == $comment['module']) ? "selected" : "";
			echo '<option value="' . $module . '" ' . $selected . '>' . _mName($module) . '</option>';
		}
		echo '			</select>
					</div>
			  </div>
			  <div class="form-group">
					<label class="col-sm-3 control-label">'. _COM_ACT .'</label>
					<div class="col-sm-4">
						'.checkbox('active', $comment['status']).'
					</div>
			  </div>
			  </div></div>
			  </section></div></div>
			  <div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>'. _COM_TEXT .'</b></div><div class="panel-body"><div class="switcher-content">'.adminArea('text', $bb->htmltobb($comment['text']), 10, 'textarea', false, true).'
			  <br><input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="' . _UPDATE . '" />';
		echo '<input type="hidden" name="cid" value="' . $commId . '">
			  <input type="hidden" name="userid" value="' . $comment['uid'] . '">';
		echo '</form>';
		echo '</div></div>
		</section></div></div>';
		$adminTpl->admin_foot();

		break;

	case 'save':
		$cid = isset($_POST['cid']) ? intval($_POST['cid']) : '';
		$userid = isset($_POST['userid']) ? intval($_POST['userid']) : '';
		$module = isset($_POST['module']) ? filter($_POST['module'], 'module') : '';
		$text = isset($_POST['text']) ? filter($_POST['text']) : '';
		$gname = isset($_POST['gname']) ?  filter($_POST['gname']) : '';
		$gemail = isset($_POST['gemail']) ?  filter($_POST['gemail']) : '';
		$active = isset($_POST['active']) ? 1 : false;
		if($cid != 0)
		{
			if((!empty($text) && $userid!=0) || (!empty($text) && !empty($gname) && !empty($gemail)))
			{
				$bb = new bb;
				$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `module` = '" . $module . "', `text` = '" . $db->safesql($bb->parse(processText($text))) . "', `gemail` = '" . $gemail . "', `gname` = '" . $db->safesql(processText($gname)) . "', `status` = '" . $active . "' WHERE `id` =" . $cid . ";");
				$adminTpl->admin_head(_COM_COM . ' | ' ._COM_EDIT);
				$adminTpl->info(_COM_UPDATE);
				$adminTpl->admin_foot();
			}
			else
			{
				$bb = new bb;
				$adminTpl->admin_head(_COM_COM . ' | ' ._COM_EDIT);
				$adminTpl->info(_BASE_ERROR_0, 'error');
				$adminTpl->admin_foot();
			}
		}
		else
		{
			location(ADMIN);
		}
		break;

	case 'delete':
		$commId = intval($url[3]);
		list($mod) = $db->fetchRow($db->query("SELECT module FROM `" . DB_PREFIX . "_comments` WHERE id = " . $commId . " LIMIT 1"));
		if($commId != 0 && $mod)
		{
			deleteComment($commId, $mod);
			if(isset($_GET['moderate']))
			{
				location(ADMIN.'/moderation/com/comments');
			}
			else
			{
				location(ADMIN.'/comments/ok');
			}
		}
		else
		{
			location(ADMIN);
		}
		break;

	case "activate":
		$id = intval($url[3]);
		$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		header('Location: /'.ADMIN.'/comments');
	break;

	case "deactivate":
		$id = intval($url[3]);
		$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		header('Location: /'.ADMIN.'/comments');
	break;

	case "reactivate":
		$id = intval($url[3]);
		$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = NOT `status` WHERE `id` =" . $id . " LIMIT 1 ;");
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/com/comments');
		}
		else
		{
			location(ADMIN.'/comments');
		}
	break;

	case "action":
		$type = $_POST['act'];
		$checks = isset($_POST['checks']) ? $_POST['checks']: '';
		if(is_array($checks)) {
			switch($type) {
				case "activate":
					foreach($_POST['checks'] as $id)
					{
						$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = '1' WHERE `id` =" . $id . " LIMIT 1 ;");
					}
					break;

				case "deActivate":
					foreach($_POST['checks'] as $id)
					{
						$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = '0' WHERE `id` =" . $id . " LIMIT 1 ;");
					}
					break;

				case "reActivate":
					foreach($_POST['checks'] as $id)
					{
						$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `status` = NOT `status` WHERE `id` =" . $id . " LIMIT 1 ;");
					}
					break;

				case "delete":
					foreach($_POST['checks'] as $id)
					{
						deleteComment($id);
					}
					break;
			}
		}
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/com/comments');
		}
		else
		{
			location(ADMIN.'/comments/ok');
		}
		break;
}

function deleteComment($id, $mod ='')
{
global $db;
	//add_point($mod, $id, '-');
	$db->query("DELETE FROM `" . DB_PREFIX . "_comments` WHERE `id` = ".$id);
}
