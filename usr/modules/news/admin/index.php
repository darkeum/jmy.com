<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

function news_main() 
{
	global $adminTpl, $core, $db, $admin_conf, $url, $lang;
	$adminTpl->admin_head($lang['news'] . ' | ' . $lang['news_list']);
	echo '<div id="content" class="animated fadeIn">';
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];
	$query_search = isset($_POST['query']) ? filter($_POST['query'], 'text') : '';
	$where = 'WHERE active != 2';
	$cat = 0;	
	if(isset($url[3]) && $url[3] == 'cat')
	{
		$cat = $url[4];
		$where .= "AND cat LIKE '%," . $db->safesql($url[4]) . ",%' ";
	}	
	$whereC = $where;		
	$where .= ' AND l.lang = \'' . $core->InitLang() . '\'';	
	if($query_search)
	{
		$where .= 'AND l.title LIKE \'%' . $db->safesql($query_search) . '%\'';
	}	
	$cats_arr = $core->aCatList('news');	
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_news $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/module/news/{page}');
	$query = $db->query("SELECT n.*, l.*, c.id as cid, c.name, c.altname as alturl FROM ".DB_PREFIX."_news AS n LEFT JOIN ".DB_PREFIX."_categories AS c ON (n.cat=c.id) LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='news') $where ORDER BY n.date DESC LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_news WHERE active=2'));
		if($count > 0)
		{
			$adminTpl->alert('warning', $lang['info'], $lang['news_moder']);
		}
	if($db->numRows($query) > 0) 
	{		
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">' . $lang['news_list'] . ':</span>    
						<div class="widget-menu pull-right mr5" >						
							<form style=" display: inline-block;" method="POST" action="administration/module/news">
								<input name="query" value="" placeholder="'.$lang['search'].'" class="form-control mr10">
							</form>
							<select style="width: 150px; display: inline-block;" class="form-control" onchange="top.location=this.value">
								<option value="">'.$lang['choose_cat'].'</option>';
								foreach ($cats_arr as $cid => $name) 
									{
										echo '  <option value="' . ADMIN . '/module/news/cat/' . $cid . '" ' . (($cid == $cat) ? 'selected' : '') . '">'.$name.'</option>';	
									}
						echo '</select>
					</div>
              </div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" action="{ADMIN}/module/news/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th class="col-md-2">' . $lang['title'] . '</th>
							<th class="col-md-2">' . $lang['date'] . '</th>
							<th class="col-md-2">' . $lang['cats'] .'</th>
							<th class="col-md-1 text-center">' . $lang['status'] .'</th>
							<th class="col-md-1 text-center"><span class="fa fa-comments-o fa-lg"></span></th>
							<th class="col-md-1 text-center"><span class="fa fa-eye fa-lg"></span></th>
							<th class="col-md-1">' . $lang['author'] . '</th>
							<th class="col-md-3">' . $lang['actions'] . '</th>
							<th class="col-md-1">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody>';
		while($news = $db->getRow($query)) 
		{
			if ($news['active'] == 1)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}
			elseif ($news['active'] == 2)
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
			}
			else
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}			
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $news['id'] . '</td>
				<td>' . $news['title'] . '</td>
				<td>' . formatDate($news['date'], true) . '</td>				
				<td>' . ($news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'short', 3) : 'Нет') . '</td>
				<td class="text-center">' . $status_icon . '</td>
				<td class="text-center">' . $news['comments'] . '</td>
				<td class="text-center">' . $news['views'] . '</td>
				<td>' . $news['author'] . '</td>
					<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{ADMIN}/module/news/edit/'.$news['id'].'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">
							'.(($news['active'] != 2) ? '<li><a href="{ADMIN}/module/news/moderation/'.$news['id'].'">' . $lang['do_moderation'] . '</a></li>' : '' ).'
							<li><a href="{ADMIN}/module/news/retivate/'.$news['id'].'">'. (($news['active'] != 1) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>  
							<li><a href="{ADMIN}/module/news/index/'.$news['id'].'">'. (($news['allow_index'] == 0) ? $lang['news_action_index'] : $lang['news_action_noindex']).'</a></li> 	
							<li><a href="{ADMIN}/module/news/fix/'.$news['id'].'">'. (($news['fixed'] == 0) ? $lang['news_action_fix'] : $lang['news_action_nofix']).'</a></li> 								
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$news['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$news['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[news]', $news['title'], $lang['news_delete_title']) .'</h3>					
							<hr class="short alt">
							<p>' . str_replace('[news]', $news['title'], $lang['news_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{ADMIN}/module/news/delete/'.$news['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $news['id'] . '" type="checkbox" name="checks[]" value="' . $news['id'] . '">
						<label for="checkbox' . $news['id'] . '"></label>
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
								<option value="allowComment">' . $lang['news_allow_comment'] . '</option>
								<option value="blockComment">' . $lang['news_block_comment'] . '</option>
								<option value="blockIndex">' . $lang['news_remove_main'] .'</option>
								<option value="setIndex">' . $lang['news_set_main'] .'</option>
								<option value="nowDate">' . $lang['news_set_nowdate'] . '</option>
								<option value="activate">' . $lang['do_activation'] . '</option>
								<option value="deActivate">' . $lang['do_deactivation'] . '</option>
								<option value="reActivate">' . $lang['do_reactivation']  . '</option>		
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
        </div';	
		$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_news $whereC"));
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/news/{page}');
	} 
	else
	{
		$adminTpl->info($lang['news_empty'], 'empty', null, $lang['news_list'], $lang['news_add'], ADMIN.'/module/news/add');	
	}		
	echo'</div>';	
	$adminTpl->admin_foot();
} 


function news_add($nid = null) 
{
global $adminTpl, $core, $db, $core, $config, $lang;
	if(isset($nid)) 
	{	
		$bb = new bb;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $nid . "'");
		$news = $db->getRow($query);
		$id = $news['id']; 
		$author = $news['author']; 
		$date = gmdate('d.m.Y H:i', $news['date']); 
		$tags = $news['tags']; 
		$groups = $news['groups']; 
		$altname = $news['altname']; 
		$keywords = $news['keywords']; 
		$description = $news['description']; 		
		$allow_comments = $news['allow_comments']; 
		$allow_rating = $news['allow_rating']; 
		$allow_index = $news['allow_index']; 
		$score = $news['score']; 
		$votes = $news['votes']; 
		$preview = $news['preview']; 
		$views = $news['views']; 
		$comments = $news['comments']; 
		$fields = unserialize($news['fields']); 
		$fix = $news['fixed']; 
		$active = ($news['active'] == 2 ? 0 : $news['active']);
		$cat = $news['cat']; 
		$cat_array = explode(',', $cat);
		$catttt = explode(',', $cat);
		$edit = true;
		$grroups = explode(',', $groups);
		$firstCat = $catttt[1];
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);
		$langMassiv = $core->getLangList(true);
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_langs WHERE postId = '" . $id . "' AND module='news'");
		while($langs = $db->getRow($query))
		{
			$title[$langs['lang']] = prepareTitle($langs['title']);
			$short[$langs['lang']] = $bb->htmltobb($langs['short']);
			$full[$langs['lang']] = $bb->htmltobb($langs['full']);
		}		
		$lln = $lang['news_edit'];
		$dosave = $lang['update'];
		$remote = ADMIN.'/module/news/ajax/isurl/update';
	} 
	else 
	{
		$id = false; 
		$title = false; 
		$short = false; 
		$full = false; 
		$author = $core->auth->user_info['nick']; 		
		$date = gmdate('d.m.Y H:i'); 
		$tags = false; 
		$cat = false; 
		$altname = false; 
		$keywords = false; 
		$description = false; 		
		$allow_comments = 1; 
		$allow_rating = 1; 
		$allow_index = 1; 
		$score = false; 
		$votes = false; 
		$views = false; 
		$preview = false; 
		$comments = false; 
		$fields = false; 
		$fix = ''; 
		$active = 1;
		$edit = false;
		$catttt = array();
		$grroups = array();
		$firstCat = '';
		$lln = $lang['news_add'];
		$dosave = $lang['add'];
		$remote = ADMIN.'/module/news/ajax/isurl';
	}
	$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE module='news'");
	$adminTpl->admin_head($lang['news'] . ' | ' . $lln);
	$adminTpl->js_code[] = '	
        var selectList = $(\'.admin-form select\');
        selectList.each(function (i, e) {
          $(e).on(\'change\', function () {
            if ($(e).val() == "0") $(e).addClass("empty");
            else $(e).removeClass("empty")
          });
        });
        selectList.each(function (i, e) {
          $(e).change();
        });
        $("input#tagsinput").tagsinput({
          tagClass: function (item) {
            return \'label label-default\';
          }
        });		
        $(".select2-single").select2();';	
	$validation_array = array(		
		'title' => array(
			'required' =>  array('true', $lang['news_add_name_err'])			
		),
		'translit' => array(
			'required' =>  array('true', $lang['news_add_url_err_1']),
			'remote' =>  array($remote,  $lang['news_add_url_err_3'])
		),
		'date' => array(
			'required' =>  array('true', $lang['news_add_date_err']),
		),
		'author' => array(
			'required' =>  array('true', $lang['news_add_author_err']),
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	ajaxInit();
	fancyboxInit();
	$cats_arr = $core->aCatList('news');
	echo '<section id="content" class="table-layout animated fadeIn">			
				<div class="tray tray-center">
					<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'.$lang['news_add'].'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['news_add_tab_general'].'</a></li>
								<li><a href="#tab1_2" data-toggle="tab">'.$lang['news_add_tab_settings'].'</a></li>
								<li><a href="#tab1_3" data-toggle="tab">'.$lang['news_add_tab_access'].'</a></li>
								'.(($db->numRows($queryXF) > 0) ? '<li><a href="#tab1_4" data-toggle="tab">'.$lang['news_add_tab_xfields'].'</a></li>' : '').'
							</ul>
						</div>						
						<div class="panel-body p20 pb10">
							<div class="tab-content pn br-n admin-form">
								<div id="tab1_1" class="tab-pane active">
									<div class="section row mbn">
										<div class="col-md-9 pl15">
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="title" class="field prepend-icon">
														<input id="title" type="text" name="title" placeholder="'.$lang['news_add_name'].'" class="event-name gui-input br-light light" '.(isset($nid) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\');"').' value="' . (isset($title[$config['lang']]) ? $title[$config['lang']] : '') . '">				
														<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
													</label>
												</div>
												<div class="col-xs-6">
													<label for="translit" class="field prepend-icon">
														<input id="translit" type="text" name="translit" placeholder="'.$lang['news_add_url'].'" class="event-name gui-input br-light light" value="'.$altname.'">
														<label for="translit" class="field-icon"><i class="fa fa-link"></i></label>
													</label>
												</div>
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="author" class="field prepend-icon">
														<select name="author" class="select2-single form-control">';
														$query_users_list = $db->query("SELECT * FROM ".DB_PREFIX."_users");
														if($db->numRows($query_users_list) > 0) 
														{
															while($users_list = $db->getRow($query_users_list)) 
															{
																$selected = ($users_list['nick'] == $author) ? "selected" : "";
																echo '<option value="'.$users_list['nick'].'" '.$selected.'>'.$users_list['nick'].'</option>';
															}
														}																
														echo'	</select>	
														<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
													</label>
												</div>
												<div class="col-xs-6">
													 <label for="date" class="field prepend-picker-icon">
														<input id="date" type="text" name="date" placeholder="'.$lang['news_add_date'].'" class="gui-input" value="'.$date.'">
													</label>
												</div>
											</div>
											<div class="section mb10">												
												<label class="field mb15">'.$lang['news_add_tags'].':</label>
												<input id="tagsinput" name="tags" type="text" value="'.$tags.'" class="bg-light mt10">
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="category" class="field">
														<select class="form-control" name="category[]" id="maincat" onchange="if(this.value != \'0\') {show(\'catSub\');}" >
															<option value="0">'.$lang['news_add_nocat'].'</option>';	
															foreach ($cats_arr as $cid => $name) 
															{
																$selected = ($cid == $firstCat) ? "selected" : "";
																echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
															}
												  echo '</select>
													</label>
												</div>
												<div class="col-xs-6" id="catSub" style="' . ((isset($nid)&&($firstCat!=0)) ? '' : 'display:none;') . '">
												<label for="category" class="field">
													<select class="form-control" name="category[]" id="category"  multiple >';
														foreach ($cats_arr as $cid => $name) 
														{
															if($catttt) $selected = in_array($cid, $catttt) ? "selected" : "";
															echo '<option value="' . $cid . '" ' . $selected . ' id="cat_' . $cid . '">' . $name . '</option>';
														}
												echo '</select>
												</label>
											</div>
										</div> 
									</div>
									<div class="col-md-3">
										<div data-provides="fileupload" class="fileupload fileupload-'.($preview ? 'exists' : 'new').' admin-form">
											<div class="fileupload-preview thumbnail mb15">
												<img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:'.$lang['news_add_mini'].'"').' alt="holder">
											</div>
											<span class="button btn-system btn-file btn-block ph5">
												<span class="fileupload-new">'.$lang['upload'].'</span>
												<span class="fileupload-exists">'.$lang['upload_again'].'</span>
												<input name="preview" type="file">
											</span>
											
										</div>
										<div class="checkbox-custom checkbox-warning mb5 mt15 text-center">
										  <input id="preview_del" name="preview_del" type="checkbox">
										  <label for="preview_del">'.$lang['news_add_mini_del'].'</label>
										</div>
									</div>
								</div>
							</div>
							<div id="tab1_2" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">'.$lang['news_add_keywords'].':</label>
											<label for="keywords" class="field prepend-icon">												
												<input id="keywords" type="text" name="keywords" placeholder="'.$lang['news_add_keywords_pre'].'" class="event-name gui-input bg-light br-light" value="' . $keywords . '">
												<label for="keywords" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['news_add_description'].':</label>
											<label for="description" class="field prepend-icon">
												<input id="description" type="text" name="description" placeholder="'.$lang['news_add_description_pre'].'" class="event-name gui-input bg-light br-light" value="' . $description . '">
												<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['news_add_seo'].'</p>
									</div>
									<div class="col-xs-6">
										<label class="field option">
										 '.checkbox('status', $active, $lang['news_add_activate']).'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('rating', $allow_rating, $lang['news_add_raiting']).'
										</label><br>
										<label class="field option mt15">
										 '.checkbox('comments', $allow_comments, $lang['news_add_comments']).'
										</label><br>
										  <label class="field option mt15">
										 '.checkbox('fix', $fix, $lang['news_add_fix']).'
										</label><br>
										  <label class="field option mt15">
										 '.checkbox('index', $allow_index, $lang['news_add_index']).'
										</label>
									</div>
								</div>
								<br>
							</div>
							<div id="tab1_3" class="tab-pane">
								<div class="section">
									<b>'.$lang['news_add_accsess_title'].'</b>
									<p>'.$lang['news_add_accsess'].'</p>
									<hr class="alt short mv15">
									<label class="field select-multiple">
										<select name="groups[]" id="group" class="form-control" multiple>
											<option value="" ' . (empty($grroups) ? 'selected' : '') . '">'. $lang['news_add_accsess_all'] .'</option>';
											$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
											while($rows = $db->getRow($query)) 
											{
												$selected = in_array($rows['id'], $grroups) ? "selected" : "";
												echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
											}
									echo' </select>
									</label>
								</div>
							</div>';
							if($db->numRows($queryXF) > 0)
							{
								echo '<div id="tab1_4" class="tab-pane form-horizontal">';
								while($xfield = $db->getRow($queryXF)) 
								{
									echo '<div class="form-group">
											<label class="col-sm-3 control-label">'. $xfield['title'] .'</label>
												<div class="col-sm-4">';
									if($xfield['type'] == 3)
									{
										$dxfield = array_map('trim', explode("\n", $xfield['content']));
										$xfieldChange = '<select class="form-control" name="xfield[' . $xfield['id'] . ']"><option value="">Пусто</option>';
										foreach($dxfield as $xfiled_content)
										{
											$xfieldChange .= '<option value="' . $xfiled_content . '" ' . (isset($fields[$xfield['id']][1]) && $fields[$xfield['id']][1] == $xfiled_content ? 'selected' : ''). '>' . $xfiled_content . '</option>';
										}
										$xfieldChange .= '</select>';
									}
									elseif($xfield['type'] == 2)
									{
										$xfieldChange = '<textarea class="form-control" name="xfield[' . $xfield['id'] . ']" >' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '</textarea>';
									}
									else
									{
										$xfieldChange = '<input type="text" class="form-control" name="xfield[' . $xfield['id'] . ']" value="' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '" />';
									}
									echo $xfieldChange;
									echo '</div></div>
											<input type="hidden" name="xfieldT[' . $xfield['id'] . ']" value="' . $xfield['title'] . '" />';
								}
								echo '</div>';
							}
						echo'</div>
					</div>
				</div>';		
				if ($id == false)
				{
					$dir = ROOT.'files/news/temp';
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);
						
					}
					$_SESSION["RF"]["fff"] ="files/news/temp/";		
				}
				else
				{
				
					$_SESSION["RF"]["fff"] ="files/news/".$id."/";
				}
				echo'
				<div class="tab-block mb25">
					<ul class="nav nav-tabs_editor nav-tabs-right">
						<li class="active">
							<a href="#tab1" data-toggle="tab" aria-expanded="true">'.$lang['news_add_short'].'</a>
						</li>
						<li class="">
							<a href="#tab2" data-toggle="tab" aria-expanded="false">'.$lang['news_add_full'].'</a>
						</li>
						<li class="">
							<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=2"><i class="fa fa-folder-open-o text-purple"></i> '.$lang['news_add_upload'].'</a>
						</li>    
					</ul>
					<div class="tab-content_editor">
						<div id="tab1" class="tab-pane active">'
							.adminArea('short[' . $config['lang'] . ']', (isset($short[$config['lang']]) ? $short[$config['lang']] : ''), 5, 'textarea', 'onchange="caa(this);"', true, true).'
						</div>
						<div id="tab2" class="tab-pane">'
							.adminArea('full[' . $config['lang'] . ']', (isset($full[$config['lang']]) ? $full[$config['lang']] : ''), 5, 'textarea', 'onchange="caa(this);"', true, false).'
						</div>
					</div>
				</div>
				<div class="btn-group">
				  <button type="button" class="btn btn-hover btn-success" onclick="genPreview(\'title\', \'short[' . $config['lang'] . ']\', \'full[' . $config['lang'] . ']\');"><i class="fa fa-desktop"></i></button>
				  <button  type="submit" class="btn btn-success dark">' . $dosave . '</button>
				</div>';				 
				if($edit) 
				{
					echo '<script>
							$(\'#admin-form\').submit(function (e) {
								var form = this;
								e.preventDefault();
								setTimeout(function () {
									form.submit();
								}, 1000); // in milliseconds
							});
							</script>
							<input type="hidden" name="edit" value="1" />';
					if($news['active'] == 2)
					{
						echo '<input type="hidden" name="from_user" value="1" />';
					}
					echo '<input type="hidden" name="edit_id" value="'.$id.'" />';
				}	
				if(isset($nid))
				{
					echo '<input type="hidden" name="oldAltName" value="'.$altname.'" />';
				}				
	echo'</form>
		</div>
	</section>';	
	$adminTpl->admin_foot();
} 
 
function news_save() 
{
global $adminTpl, $core, $db, $cats, $groupss, $config, $news_conf, $lang;
	$bb = new bb;
	$word_counter = new Counter();	
	$title = $_POST['title'];
	$langTitle = isset($_POST['langtitle']) ? $_POST['langtitle'] : '';
	$langTitle[$config['lang']] = $title;
	$author = filter($_POST['author'], 'nick');
	$ttime = 'UNIX_TIMESTAMP(NOW())';
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$oldAltName = !empty($_POST['oldAltName']) ? filter($_POST['oldAltName']) : '';
	$tags = isset($_POST['tags']) ? mb_strtolower(filter($_POST['tags'], 'a'), 'UTF-8') : mb_strtolower(filter($gen_tag, 'a'), 'UTF-8');
	$translit = ($_POST['translit'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['translit']), 'UTF-8') : translit($title);	
	$full= isset($_POST['full']) ? $_POST['full'] : '';
	$short= isset($_POST['short']) ? $_POST['short'] : '';		
	$xfield = isset($_POST['xfield']) ? $_POST['xfield'] : '';
	$xfieldT = isset($_POST['xfieldT']) ? ($_POST['xfieldT']) : '';
	$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';
	$groups = isset($_POST['groups']) ? $_POST['groups'] : '0';
	$comment = isset($_POST['comments']) ? 1 : 0;
	$rating = isset($_POST['rating']) ? 1 : 0;
	$index = isset($_POST['index']) ? 1 : 0;
	$status = isset($_POST['status']) ? 1 : 0;
	$fix = isset($_POST['fix']) ? 1 : 0;	
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	$cnt = (($full != '') ? $full : $short);	
	$gen_tag =  $word_counter->get_keywords(substr($cnt, 0, 500)); 
	$keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500)); 	
	$newcnt = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $cnt), 'html')), $edit_id, true);		
	$description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150); 	
	
	if($edit_id > 0)
	{			
		$old_dataQ = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $edit_id . "'");
		$old_data = $db->getRow($old_dataQ);
	}	
	if($date != $ttime)
	{
		$parseDate = explode(' ', $date);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	if(is_array($category)) 
	{
		$firstCat = $category[0];
		unset($category[0]);
		$deleteCat = array_search($firstCat, $category);
		unset($category[$deleteCat]);
		$category[0] = $firstCat;
		ksort($category);
		foreach($category as $cid) 
		{
			$cats .= intval($cid) . ",";
		}
	}
	else 
	{
		$cats  = $category . ',';
	}
	
	$fieldsSer = '';
	if(!empty($xfield))
	{
		foreach($xfield as $xId => $xContent)
		{
			if(!empty($xContent) && $xId > 0 && !empty($xfieldT[$xId]))
			{
				$xContent = processText(filter($xContent, 'html'));
				$xId = intval($xId);
				$xfieldT[$xId] = filter($xfieldT[$xId], 'title');
				$fileds[$xId] = array($xfieldT[$xId], $xContent);
			}
		}
		
		$fieldsSer = serialize($fileds);
	}
	
	$cats = ',' . $cats;	
	
	if(is_array($groups)) 
	{
		foreach($groups as $gid) 
		{
			$groupss .= intval($gid) . ",";
		}
	}
	else 
	{
		$groupss  = $groups . ',';
	}
	$groupss = ',' . $groupss;
	$lln = $lang['news_adds'];
	if(isset($_POST['edit'])) 
		{
			$lln = $lang['news_edits'];
		}
	$adminTpl->admin_head($lang['news'] . ' | ' . $lln);
	echo '<div id="content" class="animated fadeIn">';	
	if($title && $short['ru'] && $author && $translit) 
	{		
		if(isset($_POST['edit'])) 
		{
			foreach($langTitle as $k => $v)
			{
				$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
				$nshort = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $short[$k]), 'html')), $edit_id, true);
				$nfull = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $full[$k]), 'html')), $edit_id, true);	
				if(isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `short` , `full` , `lang` ) VALUES ('" . $edit_id . "', 'news', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
				}
				elseif(!isset($_POST['empty'][$k])  && (trim($v) == '' OR trim($short[$k]) == ''))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` ='" . $edit_id . "' AND `module` ='news' AND `lang`='" . $k . "' LIMIT 1");
				}
				elseif(!isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($ntitle)) . "', `short` = '" . $db->safesql($nshort) . "', `full` = '" . $db->safesql($nfull) . "' WHERE `postId` ='" . $edit_id . "' AND `module` ='news' AND `lang`='" . $k . "' LIMIT 1 ;");
				}
			}			
			if(!empty($tags) && $status == 1)
			{
				if($old_data['tags'] != $tags)
				{
					workTags($edit_id, $old_data['tags'], 'delete');
					workTags($edit_id, $tags, 'add');
				}
			}			
			if(isset($_POST['from_user']) && $status == 0)
			{
				$status = 2;
			}			
			if(($old_data['active'] == 2 && $status == 1) || ($old_data['active'] == 0 && $status == 1))
			{
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				user_points($author, 'add_news');
				$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				delcache('userInfo_'.$access['id']);
			}
			
			if($old_data['active'] == 1 && $status == 0)
			{
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				delcache('userInfo_'.$access['id']);
			}		
			$update = $db->query("UPDATE `" . DB_PREFIX . "_news` SET `author` = '" . $author . "', `date` = '" . $date . "', `tags` = '" . $tags . "', `cat` = '" . $cats . "', `altname` = '" . $translit . "', `keywords` = '" . $keywords . "', `description` = '" . $description . "', `allow_comments` = '" . $comment . "', `allow_rating` = '" . $rating . "', `allow_index` = '" . $index . "', `fields` = '" . $fieldsSer . "', `groups` = '" . $groupss . "', `fixed` = '" . $fix . "', `active` = '" . $status . "' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
			$nnid = $edit_id;
			if($update)
			{
				$adminTpl->info($lang['news_edit_ok'], 'info', null, $lang['info'], $lang['news_list'], ADMIN.'/module/news/');
			}
		} 
		else 
		{
			$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_news` ( `id` , `author` , `date` , `tags` , `cat` , `altname` ,`keywords`,`description`, `allow_comments` , `allow_rating` , `allow_index` , `score` , `votes` , `views` , `comments` , `fields` , `groups` , `fixed` , `active` ) VALUES (NULL, '" . $author . "', " . $date . ", '" . $tags . "', '" . $cats . "', '" . $db->safesql($translit) . "', '" . $keywords . "', '" . $description . "', '" . $comment . "', '" . $rating . "', '" . $index . "', '0', '0', '0', '0', '" . $fieldsSer . "', '" . $groupss . "', '" . $fix . "', '" . $status . "');");
			if($insert) 
			{
				$adminTpl->info($lang['news_add_ok'], 'info', null, $lang['info'], $lang['news_list'], ADMIN.'/module/news/');				
				if($status == 1)
				{
					$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
					user_points($author, 'add_news');
					$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
					delcache('userInfo_'.$access['id']);
				}				
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE altname = '" . $db->safesql($translit) . "'");
				$news = $db->getRow($query);
				foreach($langTitle as $k => $v)
				{
					if(trim($v) != '' && trim($short[$k]) != '')
					{
						$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
						$nshort = fileInit('news', $news['id'], 'content', $bb->parse(processText(filter($short[$k], 'html')), $news['id'], true));
						$nfull = fileInit('news', $news['id'], 'content', $bb->parse(processText(filter($full[$k], 'html')), $news['id'], true));	
						$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `short` , `full` , `lang` ) 	VALUES ('" . $news['id'] . "', 'news', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
					}
				}			
				fileInit('news', $news['id']);
				$nnid = $news['id'];
				workTags($news['id'], $tags, 'add');

			}
		}
		if (isset($_POST['preview_del']))
		{
			if(isset($_POST['edit'])) 
			{
				$queryPR = $db->query("SELECT preview FROM ".DB_PREFIX."_news WHERE id = '" . $nnid . "' LIMIT 1");
				$newsPR = $db->getRow($queryPR);
				if (file_exists(ROOT.$newsPR['preview'])) 
				{
					unlink(ROOT.$newsPR['preview']);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_news` SET `preview` = '' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
			}
		}
		else
		{
			if($_FILES['preview']['size'] > 0) 
				{	
					$purl = 'files/news/'.$nnid.'/';
					$type = str_replace('image/', '', $_FILES['preview']['type']);
					$file_full = $purl.'preview_' .$nnid.'.'.$type;
					if (file_exists(ROOT.$file_full)) 
					{
						@unlink(ROOT.$file_full);
					}
					if($foo = new Upload($_FILES['preview']))
					{
						$foo->file_new_name_body = 'preview_' .$nnid;
						$foo->image_resize = false;
						$foo->image_x = $news_conf['thumb_width'];
						$foo->image_ratio_y = true;
						$foo->file_overwrite = true;
						$foo->file_auto_rename = false;
						$foo->Process(ROOT.$purl);
						$foo->allowed = array("image/*");							
						if ($foo->processed) 
						{
							$foo->Clean();
						}
					}
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
				}
		}
	}
	else 
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

function delete($id) {
global $db;
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
	$news = $db->getRow($query);
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $news['id'] . "' AND `module` = 'news'");
	full_rmdir(ROOT . initDC('news', $news['id']));
	$db->query("DELETE FROM `" . DB_PREFIX . "_news` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
	workTags($id, $news['tags'], 'delete');
	deleteComments('news', $id);
}

function workTags($id, $tags, $do = 'add')
{
global $db;
	if(!empty($tags))
	{
		$tag = array_map('trim', explode(',', $tags));
		foreach($tag as $t)
		{
			if($do == 'add')
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . $db->safesql($t) . "', 'news');");
			}
			elseif($do == 'delete')
			{
				$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $t . "' and module='news' LIMIT 1");			
			}
		}
	}
}

function changeuGroup($var)
{
global $adminTpl, $db, $news_conf;
    $content = '<select class="form-control" name="{varName}">';
	$query2 = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`");
	while($rows2 = $db->getRow($query2)) 
	{
		$sel = ($news_conf[$var] == $rows2['id']) ? 'selected' : '';
		$content .= '<option value="' . $rows2['id'] . '" ' . $sel . '>' . $rows2['name'] . '</option>';
	}
	$content .= '</select>';
	return $content;
}

global $lang;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		news_main();
	break;
	
	case 'tags':
		$adminTpl->admin_head($lang['modules'] . ' | ' . $lang['news_tags']);
		echo '<div id="content" class="animated fadeIn">';			
		if(isset($url[4]))
		{
			switch($url[4])
			{
				case 'addOk':
					$adminTpl->alert('success', $lang['info'], $lang['news_tags_add_ok']);
					break;				
					
				case 'delOk':
					$adminTpl->alert('success', $lang['info'], $lang['news_tags_delete_ok']);
					break;
			}
		}
		$adminTpl->open();
		$query = $db->query("SELECT tag FROM " . DB_PREFIX . "_tags WHERE module = 'news'");
		if($db->numRows($query) > 0) 
		{
			echo '<div class="panel panel-dark panel-border top">
			<div class="panel-heading">
				<span class="panel-title">' . $lang['news_tags_list'] . '</span>	
				<div class="widget-menu pull-right" >
					<div class="btn-group">
						<button onclick="modal_o(\'#modal-form-add\')" type="button" class="btn btn-xs btn-success btn-block">'.$lang['news_tags_add'].'</button>
					</div>					
				</div>
			</div>
			<div class="panel-body pn">
				<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{MOD_LINK}/actionTag">
					 <table class="table table-striped">
						<thead>
							<tr>								
								<th class="w450">' . $lang['title'] . '</th>
								<th class="w350">' . $lang['news_tags_numb'] . '</th>
								<th>' . $lang['action'] . '</th>
								<th class="text-right">
									<div class="checkbox-custom mb15">
										<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
										<label for="all"></label>
									</div>	
								</th>
							</tr>
						</thead>
						<tbody>';		
						while($tag = $db->getRow($query)) 
						{
							$tags[] = $tag['tag'];
						}			
						$tag_cloud = new TagsCloud;
						$tag_cloud->tags = $tags;
						$cloud = Array();
						$tags_list = $tag_cloud->tags_cloud($tag_cloud->tags);
						$min_count = $tag_cloud->get_min_count($tags_list);	
						$i=0;
						foreach ($tags_list as $tag => $count) 
						{
							$i++;
							echo '
							<tr>
								<td><span class="pd-l-sm"></span>' . $tag . '</td>
								<td>' . $count . '</td>
								<td>
									<button type="button"  onclick="modal_o(\'#modal-form-'.$i.'\')" class="btn btn-xs btn-primary">'.$lang['delete'].'</button>
									<div id="modal-form-'.$i.'" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<div class="panel">
											<div class="panel-heading">
												<span class="panel-icon"><i class="fa fa-check-square-o"></i></span>
												<span class="panel-title">'.$lang['confirm'].'</span>
											</div>
											<div class="panel-body">
												<h3 class="mt5">' . str_replace('[tag]', $tag, $lang['news_tags_delete_title']) .  '</h3>
												<hr class="short alt">
												<p>' . str_replace('[tag]', $tag, $lang['news_tags_delete_text']) .  '</p>
											</div>
											<div class="panel-footer text-right">
												<button type="button" onclick="location.href = \'{MOD_LINK}/tagDelete/' . $tag . '\'" class="btn btn-danger">' . $lang['delete'] .'</button>
											</div>
										</div>
									</div>	
								</td>
								<td class="text-right"> 
									<div class="checkbox-custom mb15">
										<input id="checkbox' . $i . '" type="checkbox" name="checks[]" value="' . $tag . '">
										<label for="checkbox' . $i . '"></label>
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
			$adminTpl->info($lang['news_tags_empty'], 'empty', null, $lang['news_tags_list'], $lang['news_tags_add'], 'modal-form-add', 'modal');		
		}
		echo'<div id="modal-form-add" class="popup-basic admin-form mfp-with-anim mfp-hide">
            <div class="panel">
              <div class="panel-heading"><span class="panel-title"><i class="fa fa-tags"></i>'.$lang['news_tags_add'].'</span></div>
              <form id="comment" method="post" action="{MOD_LINK}/addTag">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="tag" class="field prepend-icon">
                        <input id="tag" type="text" name="tag" placeholder="'.$lang['news_tags_name'].'" class="gui-input">
                        <label for="tag" class="field-icon"><i class="fa fa-pencil"></i></label>
                      </label>
                    </div>                    
                  </div>                 
                </div>
                <div class="panel-footer">
                  <button type="submit" class="button btn-primary">'.$lang['add'].'</button>
                </div>
              </form>
            </div>
          </div>
		</div>';
		$adminTpl->close();
		$adminTpl->admin_foot();
		break;
		
	case 'addTag':
		if(!empty($_POST['tag']))
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . filter($_POST['tag']) . "', 'news');");
			location(ADMIN.'/module/news/tags/addOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
	
	case 'tagDelete':
		if(isset($url[4]))
		{
			$tag = filter(utf_decode($url[4]));
			$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $tag . "'");
			location(ADMIN.'/module/news/tags/delOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
		
	case 'actionTag':
		if(!empty($_POST['checks']))
		{
			foreach($_POST['checks'] as $id) 
			{
				if(trim($id))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $id . "'");
				}
			}
			
			location(ADMIN.'/module/news/tags/delOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
	
	case "add":
		news_add();
	break;
	
	case "save":
		news_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		news_add($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/mod/news');
		}
		else
		{
			location(ADMIN.'/module/news');
		}
	break;
	
	case "activate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/news');
	break;	
	
	case "moderation":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '2' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/news');
	break;	
	
	case "moder_moderation":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/moderation/mod/news');
	break;	
	
	case "deactivate":
	global $adminTpl, $db;
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/news');
	break;

	case "action":
	$type = $_POST['act'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case "activate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '1' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
				}
				break;			
			
			case "deActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "reActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "nowDate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `date` = '" . time() . "' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "blockIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `allow_index` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;		

			case "setIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `allow_index` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;						
				
			case "blockComment":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `	allow_comments` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "allowComment":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `	allow_comments` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
			
			case "delete":
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
				break;
		}
	}
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/moderation/mod/news');
		}
		else
		{
			location(ADMIN.'/module/news');
		}
	break;
	
	case 'ajax':
		global $adminTpl, $db;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{
				case "isurl":
					
					if(isset($_POST['translit']))
					{ 
						if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['translit']))
						{
							echo(json_encode($lang['news_add_url_err_2']));
						}
						else
						{
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE altname = '" . $db->safesql($_POST['translit']) . "'");
							if(($db->numRows($query) > 0) && ($url[5] != 'update')) 
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
	
	case 'config':
		require (ROOT.'etc/news.config.php');		
		$configBox = array(
			'news' => array(
				'varName' => 'news_conf',
				'title' => $lang['news_config'],
				'groups' => array(
					'main' => array(
						'title' => $lang['config_main'],
						'vars' => array(
							'num' => array(
								'title' => $lang['news_config_main_numt'],
								'description' => $lang['news_config_main_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'comments_num' => array(
								'title' => $lang['news_config_main_comments_numt'],
								'description' => $lang['news_config_main_comments_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'fullLink' => array(
								'title' => $lang['news_config_main_fulllinkt'],
								'description' => $lang['news_config_main_fulllinkd'],
								'content' => radio("fullLink", $news_conf['fullLink']),
							),	
							'noModer' => array(
								'title' => $lang['news_config_main_nomoder'],
								'description' => $lang['news_config_main_nomoder_desc'],
								'content' => changeuGroup('noModer'),
							),	
							'preModer' => array(
								'title' => $lang['news_config_main_premodert'],
								'description' => $lang['news_config_main_premoderd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
							'related_news' => array(
								'title' => $lang['news_config_main_relatedt'],
								'description' => $lang['news_config_main_relatedd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'addNews' => array(
								'title' => $lang['news_config_main_addnewst'],
								'description' => $lang['news_config_main_addnewsd'],
								'content' => radio("addNews", $news_conf['addNews']),
							),
						)
					),
					'cats' => array(
						'title' => $lang['news_config_cats'],
						'vars' => array(
							'showCat' => array(
								'title' => $lang['news_config_cats_showcatt'],
								'description' => $lang['news_config_cats_showcatd'],
								'content' => radio("showCat", $news_conf['showCat']),
							),							
							'subLoad' => array(
								'title' => $lang['news_config_cats_subloadt'],
								'description' => $lang['news_config_cats_subloadd'],
								'content' => radio("subLoad", $news_conf['subLoad']),
							),
							'catCols' => array(
								'title' => $lang['news_config_cats_catcolst'],
								'description' => $lang['news_config_cats_catcolsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'showBreadcumb' => array(
								'title' => $lang['news_config_cats_showbreadcumbt'],
								'description' => $lang['news_config_cats_showbreadcumbd'],
								'content' => radio("showBreadcumb", $news_conf['showBreadcumb']),
							),
						)
					),					
					'tags' => array(
						'title' => $lang['news_config_tags'],
						'vars' => array(
							'tags' => array(
								'title' => $lang['news_config_tags_tagst'],
								'description' => $lang['news_config_tags_tagsd'],
								'content' => radio("tags", $news_conf['tags']),
							),							
							'tags_num' => array(
								'title' => $lang['news_config_tags_tags_numt'],
								'description' => $lang['news_config_tags_tags_numd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'tagIll' => array(
								'title' => $lang['news_config_tags_tagillt'],
								'description' => $lang['news_config_tags_tagilld'],
								'content' => radio("tagIll", $news_conf['tagIll']),
							),
							'illFormat' => array(
								'title' => $lang['news_config_tags_illformatt'],
								'description' => $lang['news_config_tags_illformatd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
						)
					),
					'ratings' => array(
						'title' => $lang['news_config_ratings'],
						'vars' => array(
							'limitStar' => array(
								'title' => $lang['news_config_ratings_limitstart'],
								'description' => $lang['news_config_ratings_limitstard'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'starStyle' => array(
								'title' => $lang['news_config_ratings_starstylet'],
								'description' => $lang['news_config_ratings_starstyled'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'carma_rate' => array(
								'title' => $lang['news_config_ratings_carma_ratet'],
								'description' => $lang['news_config_ratings_carma_rated'],
								'content' => radio("carma_rate", $news_conf['carma_rate']),
							),
							'carma_summ' => array(
								'title' => $lang['news_config_ratings_carma_summt'],
								'description' => $lang['news_config_ratings_carma_summd'],
								'content' => radio("carma_summ", $news_conf['carma_summ']),
							),
						)
					),
					'files' => array(
						'title' => $lang['news_config_file'],
						'vars' => array(
							'fileEditor' => array(
								'title' => $lang['news_config_file_feditort'],
								'description' => $lang['news_config_file_feditord'],
								'content' => radio("fileEditor", $news_conf['fileEditor']),
							),
							'imgFormats' => array(
								'title' => $lang['news_config_file_imgformatst'],
								'description' => $lang['news_config_file_imgformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'attachFormats' => array(
								'title' => $lang['news_config_file_attachformatst'],
								'description' => $lang['news_config_file_attachformatsd'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'max_size' => array(
								'title' => $lang['news_config_file_max_sizet'],
								'description' => $lang['news_config_file_max_sized'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),			
							'thumb_width' => array(
								'title' => $lang['news_config_file_thumb_widtht'],
								'description' => $lang['news_config_file_thumb_widthd'],
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
		generateConfig($configBox, 'news', '{MOD_LINK}/config', $ok);
		break;		
}
