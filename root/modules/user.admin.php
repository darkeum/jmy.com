<?php

/**
* @name        JMY CORE
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY CORE
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

if($config['plugin'])
{
	$plugin = new plugin;
}

global $lang;

switch(isset($url[2]) ? $url[2] : null) 
{
	default:
		$adminTpl->admin_head($lang['users_manager']);		
		$where = '';
		$query = isset($_POST['query']) ? filter($_POST['query'], 'a') : '';
		$for = isset($_POST['for']) ? filter($_POST['for'], 'a') : '';
		$gr = isset($_POST['gr']) ? intval($_POST['gr']) : '';
		$banned = isset($_POST['banned']) ? true : false;
		$q = isset($_POST['q']) ? filter($_POST['q'], 'a') : '';			
			
		if(isset($url[2]) && $url[2] == 'group')
		{
			$where = "WHERE u.`group` = '" . intval($url[3]) . "' ";
		}
		elseif($query)
		{
			$where = "WHERE u.nick LIKE '%" . $db->safesql($query) . "%'";
			echo '<b>'.$lang['db_query'].'</b>: ' . $query . '<br style="clear:both" />';
		}
		elseif($for)
		{
			$where = "WHERE u." . $for . " LIKE '%" . $db->safesql($q) . "%'" . ($gr ? "AND u.`group` = '" . $gr . "'" : '');
			$s = true;
			$o = true;
		}
		else
		{
			$s = true;
		}
		$adminTpl->open();	
		echo '<section id="content" class="table-layout animated fadeIn">
          <div class="tray tray-center">
			<form enctype="multipart/form-data" role="form" method="POST" action="{ADMIN}/user/addUsr">
            <div class="panel mb25 mt5">
              <div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lang['users_add'].'</span>
                <ul class="nav panel-tabs-border panel-tabs">
                  <li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['general'].'</a></li>
                  <li><a href="#tab1_2" data-toggle="tab">'.$lang['settings'].'</a></li>
                  <li><a href="#tab1_3" data-toggle="tab">'.$lang['extra'].'</a></li>
                </ul>
              </div>
              <div class="panel-body p20 pb10">
                <div class="tab-content pn br-n admin-form">
                  <div id="tab1_1" class="tab-pane active">
                    <div class="section row mbn">
                      <div class="col-md-9 pl15">
                        <div class="section row mb15">                         
                          <div class="col-xs-6">
                            <label for="nick" class="field prepend-icon">
                              <input id="nick" type="text" name="nick" placeholder="'.$lang['login'].'" class="event-name gui-input br-light light">
                              <label for="nick" class="field-icon"><i class="fa fa-user"></i></label>
                            </label>
                          </div>
						  <div class="col-xs-6">
                            <label for="pass" class="field prepend-icon">
                              <input id="password" type="password" name="pass" placeholder="'.$lang['password'].'" class="event-name gui-input br-light light">
                              <label for="pass" class="field-icon"><i class="fa fa-lock"></i></label>
                            </label>
                          </div>
                        </div>                      
                        <div class="section mb15">
                          <label for="mail" class="field prepend-icon">
                            <input id="mail" type="text" name="mail" placeholder="'.$lang['email'].'" class="event-name gui-input br-light bg-light">
                            <label for="mail" class="field-icon"><i class="fa fa-envelope-o"></i></label>
                          </label>
                        </div>
						<div class="section mb10">
							<input name="submit" type="submit" class="btn btn  btn-success" id="sub" value="'.$lang['users_add'].'" /> 
						</div>	
                      </div>
                      <div class="col-md-3">
                        <div data-provides="fileupload" class="fileupload fileupload-new admin-form">
							<div class="fileupload-preview thumbnail mb15">
								<img data-src="holder.js/100%x147" alt="holder"></div><span class="button btn-system btn-file btn-block ph5">
								<span class="fileupload-new">'.$lang['users_add_avatar'].'</span>
								<span class="fileupload-exists">'.$lang['users_add_avatar_again'].'</span>
								<input type="file" name="avatar"></span>
							</div>
						</div>
                    </div>
                  </div>
                  <div id="tab1_2" class="tab-pane">
                    <div class="section row mbn">
                      <div class="col-xs-6 pr15">
                        <div class="section mb10">
                          <label for="phone" class="field prepend-icon">
                            <input id="phone" type="text" name="phone" placeholder="'.$lang['users_phone'].'" class="event-name gui-input bg-light br-light">
                            <label for="phone" class="field-icon"><i class="fa fa-phone"></i></label>
                          </label>
                        </div>
                        <div class="section">
                          <label for="group" class="field select">
                            <select id="group" name="group">';
                             $query2 = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`  WHERE special='0' ORDER BY user ASC");
								while($rows2 = $db->getRow($query2)) 
								{
									
									echo '<option value="' . $rows2['id'] . '" '.(($rows2['id']=='2') ? 'selected' : '').'>' . $rows2['name'] . '</option>';
								}
							echo '</select><i class="arrow double"></i>
                          </label>
                        </div>                       
                      </div>
                      <div class="col-xs-6">
                        <label class="field option">
                          <input type="checkbox" name="activate" checked><span class="checkbox mr10"></span>'.$lang['users_activate'].'</label><br>
                        <label class="field option mt15">
                          <input type="checkbox" name="mailsend"><span class="checkbox mr10"></span>'.$lang['users_mailsend'].'</label>
                        <hr class="alt short mv15">
                        <p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['users_activate_text'].'</p>
                      </div>
                    </div>
                    <hr class="short alt mtn">
                    <div class="section mb15">
                      <label class="field prepend-icon">
                        <textarea id="signature" name="signature" placeholder="'.$lang['users_signature'].'" class="gui-textarea br-light bg-light"></textarea>
                        <label for="signature" class="field-icon"><i class="fa fa-edit"></i></label>
                      </label>
                    </div>
                  </div>
                  <div id="tab1_3" class="tab-pane">                   
                    <div class="section row">
                      <div class="col-md-4">
                        <label for="name" class="field prepend-icon">
                          <input id="name" type="text" name="name" placeholder="'.$lang['name'].'" class="gui-input">
                          <label for="name" class="field-icon"><i class="fa fa-user"></i></label>
                        </label>
                      </div>
                      <div class="col-md-4">
                        <label for="surname" class="field prepend-icon">
                          <input id="surname" type="text" name="surname" placeholder="'.$lang['surname'].'" class="gui-input">
                          <label for="surname" class="field-icon"><i class="fa fa-user"></i></label>
                        </label>
                      </div>
                      <div class="col-md-4">
                        <label for="ochestvo" class="field prepend-icon">
                          <input id="ochestvo" type="text" name="ochestvo" placeholder="'.$lang['ochestvo'].'" class="gui-input">
                          <label for="ochestvo" class="field-icon"><i class="fa fa-user"></i></label>
                        </label>
                      </div>
                    </div>                    
                  </div>
                </div>
              </div>
            </div>
			</form>';
			$where .= ' ORDER BY regdate DESC';
			$adminTpl->close();
			if(isset($url[2]))
			{
				if($url[2] == 'adderr')
				{
					$adminTpl->info(_USER_ADD_INFO_1, 'error');
				}
				elseif($url[2] == 'addok')
				{
					$adminTpl->info(_USER_ADD_INFO_2);
				}
				elseif($url[2] == 'order')
				{
					switch($url[3])
					{
						case 'abc':
							$where = ' ORDER BY nick ASC';
							break;		
							
						case 'last':
							$where = ' ORDER BY last_visit DESC';
							break;					
							
						case 'uid':
							$where = ' ORDER BY id ASC';
							break;
					}
				}
			}
			$numU = 24;
			$page = init_page();
			$cut = ($page-1)*$numU;		
			$query = $db->query("SELECT u.*, g.name, (SELECT uid FROM " . DB_PREFIX . "_online WHERE u.id=uid LIMIT 1) as online FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_groups` as g on(u.group = g.id) " . $where . " LIMIT " . $cut . ", " . $numU);				
			if($db->numRows($query) > 0) 
			{
			echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'.$lang['users_list'].':</span>  
					<div class="widget-menu pull-right mr5" >						
							<select style="width: 200px; display: inline-block;" class="form-control" onchange="top.location=this.value">
								<option value="' . ADMIN . '/user/">'.$lang['choose_sort'].'</option>
								<option value="' . ADMIN . '/user/order/abc">'.$lang['users_sort_abc'].'</option>
								<option value="' . ADMIN . '/user/order/last">'.$lang['users_sort_last'].'</option>
								<option value="' . ADMIN . '/user/order/uid">'.$lang['users_sort_uid'].'</option>
							</select>
					</div>			
              </div>
              <div class="panel-body pn"> 
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/user/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="text-center">' . $lang['avatar'] . '</th>
							<th>' . $lang['nick'] . '</th>	
							<th class="text-center">' . $lang['status'] . '</th>
							<th>' . $lang['group'] . '</th>								
							<th>' . $lang['users_date_reg_short'] . '</th>	
							<th>' . $lang['users_date_last_short'] . '</th>									
							<th class="w150">' . $lang['actions'] . '</th>
							<th>
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody>';		
		while($adminUser = $db->getRow($query)) 
		{	
			if ($adminUser['online'])
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}
			else
			{
				$status_icon = '<span class="fa fa-circle text-dark fa-md"></span>';
			}	

			if ($adminUser['group']=='5')
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}	

			if ($adminUser['active']!='1')
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
			}				
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $adminUser['id'] . '</td>
				<td class="w50 text-center">
					<img title="user" src="' . avatar($adminUser['id']) . '" class="img-responsive mw30 ib mr10">
				</td>			
				<td>' . $adminUser['nick'] . '</td>
				<td class="text-center">'.$status_icon.'</td>
				<td>' . $adminUser['name']  . '</td>	
				<td>' . formatDate($adminUser['regdate'], true)  . '</td>	
				<td>' . formatDate($adminUser['last_visit'], true)   . '</td>	
				<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/user/edit/' . $adminUser['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['actions'] . '</span></button>
						<ul role="menu" class="dropdown-menu">
							<li>
								<a href="{ADMIN}/user/ban/'. $adminUser['id'].'">' . $lang['users_ban'] .'</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$adminUser['id'].'\')">' . $lang['delete'] .'</a>
							</li>
						</ul>
					</div>
					<div id="modal-form-'.$adminUser['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'. $lang['confirm'] .'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[user]', $adminUser['nick'], $lang['users_del_title']) .  '</h3>							
							<hr class="short alt">
							<p>' . str_replace('[user]', $adminUser['nick'], $lang['users_del_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/user/delete/'.$adminUser['id'].'\'" class="btn btn-danger">' . $lang['delete']  .'</button>
						  </div>
						</div>
					</div>				
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $adminUser['id'] . '" type="checkbox" name="checks[]" value="' . $adminUser['id'] . '">
						<label for="checkbox' . $adminUser['id'] . '"></label>
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
		$adminTpl->info($lang['users_empty'], 'empty', null, $lang['users_list']);	
	}
		 echo '</div>        
          <aside data-tray-height="match" class="tray tray-right tray290">
		  <form role="form" method="POST" action="{ADMIN}/user">
            <div class="admin-form">
              <h4>'.$lang['users_search'].'</h4>
              <hr class="short">
              <div class="section mb10">
                <label for="s_id" class="field prepend-icon">
                  <input id="s_id" type="text" name="s_id" placeholder="'.$lang['users_id'].'" class="gui-input">
                  <label for="s_id" class="field-icon"><i class="fa fa-user"></i></label>
                </label>
              </div>
              <div class="section mb10">
                <label for="s_nick" class="field prepend-icon">
                  <input id="s_nick" type="text" name="s_nick" placeholder="'.$lang['nick'].'" class="gui-input">
                  <label for="s_nick" class="field-icon"><i class="fa fa-user"></i></label>
                </label>
              </div>
              <div class="section mb25">
                <label for="s_email" class="field prepend-icon">
                  <input id="s_email" type="text" name="s_email" placeholder="'.$lang['email'].'" class="gui-input">
                  <label for="s_email" class="field-icon"><i class="fa fa-envelope-o"></i></label>
                </label>
              </div>
              <h5><small>'.$lang['group'].'</small></h5>
              <div class="section mb15">
                <label class="field select">
                  <select id="s_group" name="s_group">';				  
					$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` WHERE special='0' ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
					while($rows = $db->getRow($query)) 
					{
						$selected = ($rows['id'] == $gr) ? "selected" : "";
						echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
					}				  
                 echo '</select><i class="arrow double"></i>
                </label>
              </div>             
              <h5><small>'.$lang['users_search_date'].'</small></h5>
              <div class="section row">
                <div class="col-md-6">
                  <label for="date1" class="field prepend-icon">
                    <input id="date1" type="text" name="date1" placeholder="01/01/14" class="gui-input">
                    <label for="date1" class="field-icon"><i class="fa fa-calendar"></i></label>
                  </label>
                </div>
                <div class="col-md-6">
                  <label for="date2" class="field prepend-icon">
                    <input id="date2" type="text" name="date2" placeholder="06/01/15" class="gui-input">
                    <label for="date2" class="field-icon"><i class="fa fa-calendar"></i></label>
                  </label>
                </div>
              </div>
			    <label class="field option ml15">
                  <input type="checkbox" name="info"><span class="checkbox"></span><span class="text-muted">'.$lang['users_banned'].'</span>
                </label>
              <hr class="short">
              <div class="section">
                <button type="button" class="btn btn-default btn-sm ph25">'.$lang['search'].'</button>              
              </div>
            </div>
			</form>
          </aside>
        </section>';	
		$queryq = $db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` " . str_replace('u.', '', $where));		
		$adminTpl->pages($page, $numU, $db->numRows($queryq), ADMIN.'/user/{page}');
		$adminTpl->close();
		$adminTpl->admin_foot();
	break;	
	
	
	
	
	case 'edit':
		$usrConf = $user;
		$uid = $url[3];
		$ok = isset($url[4]) ? true : false;
		$query = $db->query('SELECT * FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` WHERE id='.$uid);
		$user_row = $db->getRow($query);		
		$query2 = $db->query('SELECT * FROM ' . DB_PREFIX . '_board_users WHERE uid='.$uid);
		$forum = $db->getRow($query2);
		$adminTpl->admin_head('Редактирование пользователя');
		
	
		if($user_row['birthday']) 
		{
			$birthday = explode('.', $user_row['birthday']);
		}
		else
		{
			$birthday = explode('.', '0.0.0');
		}
		//$bbp = new bb;		
		//$bb = adminArea('signature', $bbp->htmltobb($user_row['signature']), 5, 'textarea', false, true);
		
		$bb = '<textarea name="signature" id="signature" class="form-control" rows="5" >'.$user_row['signature'].'</textarea>';
		$gender = '<option value="">---</option>';
		$gender .= '<option value="1"' . ($user_row['sex'] == '1' ? ' selected' : '') . '>Мужской</option>';
		$gender .= '<option value="2"' . ($user_row['sex'] == '2' ? ' selected' : '') . '>Женский</option>';
		$day = '<option value="">--</option>';

		for ($i = 1; $i < 32; $i++)
		{
			$day .= '<option value="' . ($i < 10 ? '0' . $i : $i) . '"' . ($birthday[0] == $i ? ' selected' : '') . '>' . $i . '</option>';
		}
				
		$month = '<option value="">---</option>';
		$month .= '<option value="01"' . ($birthday[1] == '1' ? ' selected' : '') . '>Январь</option>';
		$month .= '<option value="02"' . ($birthday[1] == '2' ? ' selected' : '') . '>Февраль</option>';
		$month .= '<option value="03"' . ($birthday[1] == '3' ? ' selected' : '') . '>Март</option>';
		$month .= '<option value="04"' . ($birthday[1] == '4' ? ' selected' : '') . '>Апрель</option>';
		$month .= '<option value="05"' . ($birthday[1] == '5' ? ' selected' : '') . '>Май</option>';
		$month .= '<option value="06"' . ($birthday[1] == '6' ? ' selected' : '') . '>Июнь</option>';
		$month .= '<option value="07"' . ($birthday[1] == '7' ? ' selected' : '') . '>Июль</option>';
		$month .= '<option value="08"' . ($birthday[1] == '8' ? ' selected' : '') . '>Август</option>';
		$month .= '<option value="09"' . ($birthday[1] == '9' ? ' selected' : '') . '>Сентябрь</option>';
		$month .= '<option value="10"' . ($birthday[1] == '10' ? ' selected' : '') . '>Октябрь</option>';
		$month .= '<option value="11"' . ($birthday[1] == '11' ? ' selected' : '') . '>Ноябрь</option>';
		$month .= '<option value="12"' . ($birthday[1] == '12' ? ' selected' : '') . '>Декабрь</option>';
		
		$year = '<option value="">---</option>';
		
		for ($i = 2008; $i > 1935; $i--)
		{
			$year .= '<option value="' . $i . '"' . ($birthday[2] == $i ? ' selected' : '') . '>' . $i . '</option>';
		}
		echo '
		<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<div class="panel-heading">
						<b>Редактирование пользователя ' . ($ok ? ' - <font color="green">Профиль сохранён</font>' : '') . '</b>						
					</div>
					<div class="panel-body">
					<form class="form-horizontal parsley-form" role="form" action="{ADMIN}/user/save" method="post">
												<div class="form-group">
													<label class="col-sm-3 control-label">Ник</label>
													<div class="col-sm-4">
														<input value="' . $user_row['nick'] . '" type="text" name="nick" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Статус на форуме</label>
													<div class="col-sm-4">
														<input  name="forumStatus" value="' . $forum['specStatus'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Фимилия</label>
													<div class="col-sm-4">
														<input  name="surname" value="' . $user_row['surname'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Имя</label>
													<div class="col-sm-4">
														<input  name="name" value="' . $user_row['name'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Отчество</label>
													<div class="col-sm-4">
														<input name="ochestvo" value="' . $user_row['ochestvo'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Группа</label>
													<div class="col-sm-4">';
													echo "<select name=\"group\" id=\"group\" class=\"textinput\">";
													$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
													while($rows = $db->getRow($query)) 
													{
														$_groups[$rows['special']][] = $rows;
													}
													foreach($_groups[0] as $r)
													{
														$selected = ($r['id'] == $user_row['group']) ? "selected" : "";
														echo '<option value="' . $r['id'] . '" ' . $selected . '>' . $r['name'] . '</option>';
													}
													echo '</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Спец. группа</label>
													<div class="col-sm-4">';
													if(!empty($_groups[1]))
													{
														echo "<select name=\"exgroup\" id=\"exgroup\" class=\"textinput\"><option value=\"0\">Нет</option>";
														foreach($_groups[1] as $g)
														{
															$selected2 = ($g['id'] == $user_row['exgroup']) ? "selected" : "";
															echo '<option value="' . $g['id'] . '" ' . $selected2 . '>' . $g['name'] . '</option>';
														}
														echo "</select>";
													}
													else
													{
														echo '<p class="form-control-static">Спец. групп нет</p>';
													}
													echo' </div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">День рождения</label>
													<div class="col-sm-4">
														<select name="birthDay" style="width:130px;" >' . $day . '</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Месяц рождения</label>
													<div class="col-sm-4">
														<select name="birthMonth" style="width:130px;" >' . $month . '</select> 
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Год рождения</label>
													<div class="col-sm-4">
														<select name="birthYear" style="width:130px;" >' . $year . '</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Хобби</label>
													<div class="col-sm-4">
														<input  name="hobby" value="' . $user_row['hobby'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Место проживания</label>
													<div class="col-sm-4">
														<input  name="place" value="' . $user_row['place'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Пол</label>
													<div class="col-sm-4">
														<select name="gender" style="width:394px;" class="textinput" >' . $gender . '</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Активен ли пользователь?</label>
													<div class="col-sm-4">
														' . checkbox('active', $user_row['active']) . '
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Ведите url адрес автарки</label>
													<div class="col-sm-4">
														<input  name="avatar_link" value="" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Загрузите автарку</label>
													<div class="col-sm-4">
														<input type="file" name="avatar"  />
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Подпись пользователя</label>
													<div class="col-sm-4">
														' . $bb . '
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">E-mail</label>
													<div class="col-sm-4">
														<input  name="mail" value="' . $user_row['email'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">ICQ</label>
													<div class="col-sm-4">
														<input  name="icq" value="' . $user_row['icq'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Skype</label>
													<div class="col-sm-4">
														<input  name="skype" value="' . $user_row['skype'] . '" type="text" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Новый пароль</label>
													<div class="col-sm-4">
														<input  name="newpass" value="" type="password" class="form-control" data-parsley-required="true" data-parsley-trigger="change">
													</div>
												</div>
												
												
												
											<input name="uid" value="' . $uid . '" type="hidden" />
											<div class="form-group">
								<label class="col-sm-3 control-label"></label>
								<div class="col-sm-4">
									<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="Обновить">						
								</div>
					</div>';
	$queryF = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE module='profile' and to_user='1'");
	if($db->numRows($queryF) > 0) 
	{
		$fields = unserialize($user_row['fields']);
		$xfileds = '<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#EEEEEE" style="margin-bottom:5px;" classs="pad_table"><tr bgcolor="#FFFFFF"><th colspan="3" class="in_conf_title">Дополнительные поля</th></tr>';
		while($xfield = $db->getRow($queryF)) 
		{
			if($xfield['type'] == 3)
			{
				$dxfield = array_map('trim', explode("\n", $xfield['content']));
				$xfieldChange = '<select class="textinput" name="xfield[' . $xfield['id'] . ']">';
				foreach($dxfield as $xfiled_content)
				{
					$xfieldChange .= '<option value="' . $xfiled_content . '" ' . (isset($fields[$xfield['id']][1]) && $fields[$xfield['id']][1] == $xfiled_content ? 'selected' : ''). '>' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : $xfield['content']) . '</option>';
				}
				$xfieldChange .= '</select>';
			}
			elseif($xfield['type'] == 2)
			{
				$xfieldChange = '<textarea class="textarea" name="xfield[' . $xfield['id'] . ']" >' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : $xfield['content']) . '</textarea>';
			}
			else
			{
				$xfieldChange = '<input type="text" class="textinput" name="xfield[' . $xfield['id'] . ']" value="' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : $xfield['content']) . '" />';
			}
						
			$xfileds .= '<tr bgcolor="#FFFFFF"><td class="in_conf_input" align="center">' . $xfield['title'] . '</td><td class="in_conf_input"><input type="hidden" name="xfieldT[' . $xfield['id'] . ']" value="' . $xfield['title'] . '" />' . $xfieldChange . '</select></td></tr>';
		}
		$xfileds .= '</table>';
		echo $xfileds;
	}
echo '</form>';
echo '</div>';
			echo'</section></div></div>';	
		$adminTpl->admin_foot();
		break;
		
		case 'save':
		require ROOT . 'etc/user.config.php';
			$surname = !empty($_POST['surname']) ? filter($_POST['surname'], 'a') : '';
			$name = !empty($_POST['name']) ? filter($_POST['name'], 'a') : '';
			$nick = !empty($_POST['nick']) ? filter($_POST['nick'], 'nick') : '';
			$ochestvo = !empty($_POST['ochestvo']) ? filter($_POST['ochestvo'], 'a') : '';
			$forumStatus = !empty($_POST['forumStatus']) ? filter($_POST['forumStatus'], 'a') : '';
			$birthDay = !empty($_POST['birthDay']) ? intval($_POST['birthDay']) : '';
			$birthMonth = !empty($_POST['birthMonth']) ? intval($_POST['birthMonth']) : '';
			$birthYear = !empty($_POST['birthYear']) ? intval($_POST['birthYear']) : '';
			$gender = !empty($_POST['gender']) ? intval($_POST['gender']) : '';
			$avatar_link = !empty($_POST['avatar_link']) ? filter($_POST['avatar_link'], 'dir') : '';
			$signature = !empty($_POST['signature']) ? parseBB(processText(filter($_POST['signature'], 'bb'))) : '';
			$mail = !empty($_POST['mail']) ? filter($_POST['mail'], 'mail') : '';
			$hobby = !empty($_POST['hobby']) ? filter($_POST['hobby'], 'a') : '';
			$icq = !empty($_POST['icq']) ? filter($_POST['icq'], 'a') : '';
			$skype = !empty($_POST['skype']) ? filter($_POST['skype'], 'a') : '';
			$place = !empty($_POST['place']) ? filter($_POST['place'], 'a') : '';
			$newpass = !empty($_POST['newpass']) ? $_POST['newpass'] : '';
			$uid = !empty($_POST['uid']) ? intval($_POST['uid']) : '';
			$group = !empty($_POST['group']) ? intval($_POST['group']) : '';
			$exgroup = !empty($_POST['exgroup']) ? intval($_POST['exgroup']) : '';
			$active = (!empty($_POST['active']) && $_POST['active'] == 'on') ? 1 : 0;

			
			if($birthDay && $birthMonth && $birthYear)
			{
				$birthDate = $birthDay . '.' . $birthMonth . '.' . $birthYear;
				$unixBirth = gmmktime(0, 0, 0, $birthMonth, $birthDay, $birthYear);
				$age = mb_substr((time()-$unixBirth)/31536000, 0, 2);
			}
			else
			{
				$birthDate = '';
				$age = '';
			}
			
			if($newpass)
			{
				$core->auth->updatePassword($newpass, $uid);
				if($config['plugin']) $plugin->updatePassword($newpass, $uid);
			}
			
			if(!empty($forumStatus))
			{
				$db->query("UPDATE `" . DB_PREFIX . "_board_users` SET `specStatus` = '" . $forumStatus . "' WHERE `uid` = " . $uid . " LIMIT 1 ;");
			}
			
			if($mail)
			{
				if(!preg_match('/[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-\.]+$/i', $mail)) 
				{
					$mail = '';
					$error[] = 'E-Mail имеет неверный формат';
				}				
			}
			
			if(empty($nick))
			{
				$error[] = 'Ник не может быть пустым!';
			}
				
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET `nick` = '" . $db->safesql($nick) . "', `group` = '" . $group . "', `exgroup` = '" . $exgroup . "', `active` = '" . $active . "' WHERE `id` = " . $uid . " LIMIT 1 ;");
				$core->auth->updateProfile($mail, $icq, $skype, $surname, $name, $ochestvo, $place, $age, $gender, $birthDate, $hobby, $signature, '', $uid);
				if($config['plugin']) $plugin->updateProfile($mail, $icq, $skype, $surname, $name, $ochestvo, $place, $age, $gender, $birthDate, $hobby, $signature, '', $uid);

			if($_FILES['avatar']['size'] > 0) 
			{
				deleteAvatar($uid);
				if($foo = new Upload($_FILES['avatar']))
				{
					$foo->file_new_name_body = 'av' .$uid;
					$foo->image_resize = true;
					$foo->image_x = $user['avatar_width'];
					$foo->image_ratio_y = true;
					$foo->file_overwrite = true;
					$foo->file_auto_rename = false;
					$foo->Process(ROOT.'files/avatars/users/');
					$foo->allowed = array("image/*");
						
					if ($foo->processed) 
					{
						$foo->Clean();
					}
				}
			}
			
			if(isset($error))
			{
				$txt = '';
					
				foreach($error as $msg)
				{
					$txt .= $msg . '<br />';
				}
			}
			
			location(ADMIN . '/user/edit/' . $uid . '/ok');
	
			break;
			
	case 'regroup':
		$uid = intval($url[3]);
		delcache('userInfo_'.$uid);
		$query = $db->query('SELECT * FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` WHERE id='.$uid);
		$user = $db->getRow($query);		
		windowOpen();
		if(!isset($_POST['group']))
		{
			echo '<form action="" method="post" enctype="multipart/form-data">';
			echo "<div align=\"center\"><select name=\"group\" id=\"group\" class=\"textinput\">";
			$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`  WHERE special='0' ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
			while($rows = $db->getRow($query)) 
			{
				$selected = ($rows['id'] == $user['group']) ? "selected" : "";
				if($rows['id'] != 5) echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
			}
			echo '</select> <input value="Сменить группу" type="submit" size="11" maxlength="20" class="b" /></div></form>';
		}
		else
		{
			$group = !empty($_POST['group']) ? intval($_POST['group']) : '';
			
			if($uid)
			{
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET `group` = '" . $group . "' WHERE `id` = " . $uid . " LIMIT 1 ;");
				echo '<div align="center"><font color="green"><b>Группа успешно изменена. Окно закроется атоматом.</b></font></div>
				<script type="text/javascript">setTimeout(\'window.close()\', 3000)</script>
				';
			}		
		}
		break;	
		
	case 'repass':
		$uid = intval($url[3]);
		$query = $db->query('SELECT * FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` WHERE id='.$uid);
		$user = $db->getRow($query);		
		windowOpen();
		if(!isset($_POST['newpass']))
		{
			echo '<form action="" method="post" enctype="multipart/form-data">';
			echo '<div align="center"> <input name="newpass" value="" class="textinput" type="text" size="11" maxlength="20" /> <input value="Изменить пароль" type="submit" size="11" maxlength="20" class="b" /></div></form>';
		}
		else
		{
			$newpass = !empty($_POST['newpass']) ? intval($_POST['newpass']) : '';
			
			if($uid)
			{
				$core->auth->updatePassword($newpass, $uid);
				if($config['plugin']) $plugin->updatePassword($newpass, $uid);
				echo '<div align="center"><font color="green"><b>Пароль успешно изменён, окно закроется атоматически.</b></font></div>
				<script type="text/javascript">setTimeout(\'window.close()\', 3000)</script>
				';
			}		
		}
		break;
		
	case 'delete':
		$uid = intval($url[3]);
		delcache('userInfo_'.$uid);
		$db->query("DELETE FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE `id` = " . $uid . " LIMIT 1");
		$db->query("DELETE FROM `" . DB_PREFIX . "_board_users` WHERE `uid` = " . $uid . " LIMIT 1");
		@unlink("files/avatars/users/av" . $uid . ".jpg");
		location(ADMIN . '/user');
		break;
		
	case 'ban':
		$uid = intval($url[3]);
		if($uid != $core->auth->user_info['id'])
		{
			delcache('userInfo_'.$uid);
			$query = $db->query('SELECT id FROM `' . USER_DB . '`.`' . USER_PREFIX . '_groups` WHERE `banned`=1');
			$group = $db->getRow($query);
			$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET `group` = '" . $group['id'] . "' WHERE `id` = " . $uid . " LIMIT 1 ;");
		}
		location(ADMIN . '/user');
		break;
		
	
		
	case 'addUsr':
	
		require ROOT . 'etc/user.config.php';
		$surname = !empty($_POST['surname']) ? filter($_POST['surname'], 'a') : '';
		$name = !empty($_POST['name']) ? filter($_POST['name'], 'a') : '';
		$nick = !empty($_POST['nick']) ? filter($_POST['nick'], 'nick') : '';
		$ochestvo = !empty($_POST['ochestvo']) ? filter($_POST['ochestvo'], 'a') : '';
		
		$mail = filter($_POST['mail'], 'mail');
		$group = intval($_POST['group']);
		list($check) = $db->fetchRow($db->query("SELECT Count(id) FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick='" . $db->safesql($nick) . "' OR email='" . $db->safesql($mail) . "'"));
		if($check > 0 && !empty($nick) && !empty($pass)) 
		{
			$result = 'adderr';
		}
		else
		{
			
			$active = 0;
			if (isset($_POST['activate']))
			{
				$active = 1;
			}
			
			$tail = gencode(rand(6, 11));				
			$core->auth->register($nick, $pass, $tail, $mail, '', '', $surname, $name, $ochestvo, '', '', '', '', $active, '127.0.0.1', $group);
			if($config['plugin']) $plugin->registration($nick, $pass, $tail, $mail, '', '', $surname, $name, $ochestvo, '', '', '', '', $active, '127.0.0.1', $group);
			list($uid) = $db->fetchRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick='" . $db->safesql($nick) . "' LIMIT 1"));
			$db->query("INSERT INTO `" . DB_PREFIX . "_board_users` (`uid`) VALUES ('" . $uid . "');", true);
			
			
			$queryUU = $db->query('SELECT id FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` WHERE `nick`="'.$db->safesql($nick).'" LIMIT 1');
			$UUID = $db->getRow($queryUU);
			
			echo $UUID['id'];
			if($_FILES['avatar']['size'] > 0) 
			{		
echo $UUID['id'].'1';		
				if($foo = new Upload($_FILES['avatar']))
				{
					echo $UUID['id'].'2';
					$foo->file_new_name_body = 'av' .$UUID['id'];
					$foo->image_resize = true;
					$foo->image_x = $user['avatar_width'];
					$foo->image_ratio_y = true;
					$foo->file_overwrite = true;
					$foo->file_auto_rename = false;
					$foo->Process(ROOT.'files/avatars/users/');
					$foo->allowed = array("image/*");
						
					if ($foo->processed) 
					{
						$foo->Clean();
					}
				}
			}
			$result = 'addok';
		}		
		location(ADMIN.'/user/'.$result);
		break;
}