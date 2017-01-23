<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

	global $lang;
	$adminTpl->admin_head($lang['comments_onmoder']);
	echo '<div id="content" class="animated fadeIn">';		
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];
	$where = ' WHERE c.status=\'0\'';
	$whereC = ' WHERE status=\'0\'';
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_comments $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/moderation/com/comments/{page}');
	$query = $db->query("SELECT c.*, u.nick, u.group, u.last_visit FROM ".DB_PREFIX."_comments as c LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u on (c.uid=u.id) " . $where . " ORDER BY date DESC LIMIT $cut,".$admin_conf['num']);
	if($db->numRows($query) > 0) 
	{
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">' . $lang['comments_onmoder_list'] . ':</span>  						
				</div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" action="{ADMIN}/comments/action&moderate">
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
							<li><a href="{ADMIN}/comments/reactivate/'.$comment['id'].'&moderate">' . $lang['do_active'] . '</a></li>
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
							<button type="button" onclick="location.href = \'{ADMIN}/comments/delete/'.$comment['id'].'&moderate\'" class="btn btn-danger">' . $lang['delete'] .'</button>
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
								<option value="activate">' . $lang['do_active'] . '</option>	
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
		$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_comments WHERE status=0");
		$all = $db->numRows($all_query);
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/moderation/com/comments/{page}');
	} 
	else
	{
		$adminTpl->info($lang['comments_onmoder_empty'], 'empty', null, $lang['comments_onmoder_list'], $lang['comments_list'], ADMIN.'/comments');	
	}	
	echo '</div>';
	$adminTpl->admin_foot();