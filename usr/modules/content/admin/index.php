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
    header('Location: /');
    exit;
}

function content_main() 
{
global $adminTpl, $core, $db, $admin_conf, $lang;
	$adminTpl->admin_head($lang['modules'] .' | '. $lang['static']);
	echo '<div id="content" class="animated fadeIn">';
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];		
	$query = $db->query("SELECT c.*, l.* FROM ".DB_PREFIX."_content as c  LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=c.id and l.module='content') WHERE l.lang = '" . $core->InitLang() . "' LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	if($db->numRows($query) > 0)
	{	
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">' . $lang['static_list'] . ':</span>  						
              </div>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" action="{MOD_LINK}/action">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th><span class="pd-l-sm"></span>#</th>
							<th>' . $lang['title'] . '</th>
							<th>' . $lang['date'] . '</th>
							<th>' . $lang['template'] .'</th>
							<th>' . $lang['keywords'] .'</th>
							<th class="text-center">' . $lang['status'] .'</th>
							<th class="text-center"><span class="fa fa-comments-o fa-lg"></span></th>
							<th>' . $lang['actions'] . '</th>
							<th>
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody>';
		while($content = $db->getRow($query)) 
		{
			$contentLink = $content['cat'] !== ',0,' ? 'content/' . $core->getCat('content', $content['cat'], 'development') . '/' : 'content/';
			if ($content['active'] == 1)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}		
			else
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}			
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $content['id'] . '</td>
				<td>' . $content['title'] . '</td>
				<td>' . formatDate($content['date'], true) . '</td>				
				<td>' . ($content['theme'] == '0' ? $lang['static_tpl_default'] : $content['theme']) . '</td>
				<td>' . (!empty($content['keywords']) ? str($content['keywords'], 20) : $lang['no']) . '</td>
				<td class="text-center">' . $status_icon . '</td>
				<td class="text-center">' . $content['comments'] . '</td>
					<td>				
					<div class="btn-group">
						<button type="button" onclick="location.href = \'{MOD_LINK}/edit/' . $content['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="' . $contentLink . $content['translate'] . '.html">'. $lang['view'] .'</a></li>  
							<li><a href="{MOD_LINK}/reactivate/'.$content['id'].'">'. (($content['active'] != 1) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>  													
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$content['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[content]', $content['title'], $lang['static_delete_title']) .'</h3>					
							<hr class="short alt">
							<p>' . str_replace('[content]', $content['title'], $lang['static_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{MOD_LINK}/delete/'.$content['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '">
						<label for="checkbox' . $content['id'] . '"></label>
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
		$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_content ");
		$all = $db->numRows($all_query);
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/content/{page}');	
	} 	
	else 
	{
		$adminTpl->info($lang['static_empty'], 'empty', null, $lang['static_list'], $lang['static_add'], ADMIN.'/module/content/add');	
	}	
	echo'</div>';
	$adminTpl->admin_foot();
} 

function content_add($nid = null) 
{
global $adminTpl, $core, $db, $core, $config, $lang;
	if(isset($nid)) 
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_content WHERE id = '" . $nid . "'");
		$content = $db->getRow($query);
		$id = $content['id']; 
		$keywords = $content['keywords']; 
		$description = $content['description']; 	
		$theme = $content['theme']; 
		$active = $content['active']; 
		$altname = $content['translate']; 
		$preview = $content['preview']; 
		$cat = $content['cat']; 
		$catttt = explode(',', $cat);
		$firstCat = $catttt[1];
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);		
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_langs WHERE postId = '" . $id . "' AND module='content'");
		while($langs = $db->getRow($query))
		{
			$title[$langs['lang']] = prepareTitle($langs['title']);
			$fulltitle[$langs['lang']] = prepareTitle($langs['fulltitle']);
			$text[$langs['lang']] = html2bb($langs['short']);
		}
		$remote = ADMIN.'/module/content/ajax/isurl/update';
		$lln = $lang['static_edit'];
		$dosave = $lang['update'];
	} 
	else 
	{
		$id = false; 
		$title = false; 
		$fulltitle = false; 
		$keywords = false; 
		$description = false; 		
		$cat = false; 
		$altname = false; 
		$preview = false; 
		$catttt = false; 
		$active = 1;
		$text = '';
		$theme = '';
		$firstCat = '';
		$remote = ADMIN.'/module/content/ajax/isurl';
		$lln = $lang['static_add'];
		$dosave = $lang['add'];		
	}	
	$validation_array = array(		
		'title' => array(
			'required' =>  array('true', $lang['static_add_title_err'])			
		),
		'altname' => array(
			'required' =>  array('true', $lang['static_add_url_err_1']),
			'remote' =>  array($remote,  $lang['static_add_url_err_3'])
		)
	);
	$codem = '';
	$file = $theme;
	$adminTpl->footIncludes[] ='<script src="' . PLUGINS . '/highlight_code/codemirror.js" type="text/javascript"></script>';
	//$codem = 'CodeMirror.fromTextArea("_code", {height: "dynamic",parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],stylesheet: ["' . PLUGINS . 'highlight_code/xmlcolors.css", "' . PLUGINS . 'highlight_code/jscolors.css", "' . PLUGINS . '/highlight_code/csscolors.css"], path: "' . PLUGINS . 'highlight_code/", lineNumbers: true});';
	$adminTpl->footIncludes[] ='<script type="text/javascript">	ajaxGetJS(\'' . ADMIN . '/module/content/ajax/loadtpl/'.$file.'\', \''.$codem .'\', \'_div\');</script>';
	$adminTpl->footIncludes[] ='<script type="text/javascript">
									function loadtpl(file)
									{
										ajaxGetJS(\'' . ADMIN . '/module/content/ajax/loadtpl/\'+file, \''.$codem .'\', \'_div\');
									}
								</script>';
	validationInit($validation_array);
	ajaxInit();
	fancyboxInit();
	$cats_arr = $core->aCatList('content');	
	$adminTpl->admin_head($lang['modules'] . ' | ' . $lln);
	echo '<section id="content" class="table-layout animated fadeIn">			
			<div class="tray tray-center">
				<form action="{MOD_LINK}/save" enctype="multipart/form-data" role="form" method="POST" name="content" id="admin-form">			
					<div class="panel mb25 mt5">
						<div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs">'. $lln .'</span>
							<ul class="nav panel-tabs-border panel-tabs">
								<li class="active"><a href="#tab1_1" data-toggle="tab">'.$lang['static_tab_main'].'</a></li>
								<li><a href="#tab1_2" data-toggle="tab">'.$lang['static_tab_settings'].'</a></li>								
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
													<input type="text" name="title" '. (!isset($nid) ? 'onchange="getTranslit(gid(\'title\').value, \'altname\'); caa(this);"' : '').'  value="' . (isset($title[$config['lang']]) ? $title[$config['lang']] : '') . '" class="form-control" id="title" placeholder="'.$lang['static_add_title'].'" data-parsley-required="true" data-parsley-trigger="change">	
													<label for="title" class="field-icon"><i class="fa fa-pencil"></i></label>
												</label>
											</div>
											<div class="col-xs-6">
												<label for="altname" class="field prepend-icon">
													<input type="text" name="altname" value="'.$altname.'" class="form-control" id="altname"  data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['static_add_url'].'">
													<label for="altname" class="field-icon"><i class="fa fa-link"></i></label>
												</label>
											</div>
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="category" class="field">
														<select class="form-control" name="theme" onchange="loadtpl(this.value)">
															<option value="0">'.$lang['static_tpl_default'].'</option>';
															$level = 0;
															$path = ROOT.'/usr/tpl/'.$config['tpl'].'/content/';	
															$ignore = array( 'cgi-bin', '.', '..' ); 
															$dh = @opendir( $path ); 
															while( false !== ( $file = readdir( $dh ) ) )
															{ 
																if( !in_array( $file, $ignore ) ){ 
																	$spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
																	if( is_dir( "$path/$file" ) ){ 
																		echo '<option value="'.$file.'" '.(($file == $theme) ? 'selected' : '').'>'.$file.'</option>';		
																	} 
																} 
															} 
															closedir($dh); 															
													echo '</select>
													</label>
												</div>
												<div class="col-xs-6">
													<label for="keywords" class="field prepend-icon">
														<input type="text" name="tags" value="'.$keywords.'" class="form-control" id="keywords"  data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['static_keywords'].'">
														<label for="keywords" class="field-icon"><i class="fa fa-star"></i></label>
													</label>
												</div>
											</div>
											<div class="section row mb15">
												<div class="col-xs-6">
													<label for="category" class="field">
														<select class="form-control" name="category[]" id="maincat" onchange="if(this.value != \'0\') {show(\'catSub\');}" >
															<option value="0">'.$lang['static_add_nocat'].'</option>';	
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
										<div class="section row mb15">
											<div class="col-xs-12">
												<div class="checkbox-custom checkbox-info mb5 mt15">
													<input id="active" name="status" type="checkbox" '.($active ? 'checked' : '').'>
													<label for="active">'.$lang['static_add_active'].'</label>
												</div>
											</div>
										</div>		
									</div>
									<div class="col-md-3">
										<div data-provides="fileupload" class="fileupload fileupload-'.($preview ? 'exists' : 'new').' admin-form">
											<div class="fileupload-preview thumbnail mb15">
												<img '.($preview ? 'src="'.$config['url'].$preview.'"' : 'data-src="holder.js/100%x147/text:'.$lang['static_add_mini'].'"').' alt="holder">
											</div>
											<span class="button btn-system btn-file btn-block ph5">
												<span class="fileupload-new">'.$lang['upload'].'</span>
												<span class="fileupload-exists">'.$lang['upload_again'].'</span>
												<input name="preview" type="file">
											</span>											
										</div>										
										<div class="checkbox-custom checkbox-warning mb5 mt15 text-center">
										  <input id="preview_del" name="preview_del" type="checkbox">
										  <label for="preview_del">'.$lang['static_add_mini_del'].'</label>
										</div>	
									</div>
								</div>								
							</div>
							<div id="tab1_2" class="tab-pane">
								<div class="section row mbn">
									<div class="col-xs-6 pr15">
										<div class="section mb10">
											<label class="field mb5">'.$lang['static_add_fulltitle'].':</label>
											<label for="fulltitle" class="field prepend-icon">												
												<input id="fulltitle" type="text" name="fulltitle" placeholder="'.$lang['static_add_fulltitle_pre'].'" class="event-name gui-input bg-light br-light" value="' .  (isset($fulltitle[$config['lang']]) ? $fulltitle[$config['lang']] : '') . '">
												<label for="fulltitle" class="field-icon"><i class="fa fa-code"></i></label>
											</label>
										</div>
										<div class="section mb10">
											<label class="field mb5">'.$lang['static_add_keywords'].':</label>
											<label for="keywords" class="field prepend-icon">												
												<input id="keywords" type="text" name="keywords" placeholder="'.$lang['static_add_keywords_pre'].'" class="event-name gui-input bg-light br-light" value="' . $keywords . '">
												<label for="keywords" class="field-icon"><i class="fa fa-edit"></i></label>
											</label>
										</div>
										
										<hr class="alt short mv15">
										<p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> '.$lang['static_add_seo'].'</p>
									</div>
									<div class="col-xs-6">
										<div class="section mb10">
											<label class="field mb5">'.$lang['static_add_description'].':</label>
											<label for="description" class="field prepend-icon">
												<input id="description" type="text" name="description" placeholder="'.$lang['static_add_description_pre'].'" class="event-name gui-input bg-light br-light" value="' . $description . '">
												<label for="description" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>									
									</div>
								</div>
								<br>
							</div>
							
							
						</div>
					</div>
				</div>	';
						if ($id == false)
						{
							$dir = ROOT.'files/content/temp';
							if (!file_exists($dir))
							{
								mkdir($dir, 0777);
								
							}
							$_SESSION["RF"]["fff"] ="files/content/temp/";		
						}
						else
						{
						
							$_SESSION["RF"]["fff"] ="files/content/".$id."/";
						}
						echo'
						<div class="tab-block mb25">
							<ul class="nav nav-tabs_editor nav-tabs-right">
								<li class="active">
									<a href="#tab1" data-toggle="tab" aria-expanded="true">'.$lang['static_add_content'].'</a>
								</li>
								<li class="">
									<a href="#tab2" data-toggle="tab" aria-expanded="false">'.$lang['static_add_tpl'].'</a>
								</li>
								<li class="">
									<a id="fbox" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=2"><i class="fa fa-folder-open-o text-purple"></i> '.$lang['static_add_upload'].'</a>
								</li>    
							</ul>
							<div class="tab-content_editor">
								<div id="tab1" class="tab-pane active">'
									.adminArea('text[' . $config['lang'] . ']', (isset($text[$config['lang']]) ? $text[$config['lang']] : ''), 10, 'textarea', 'onchange="caa(this);"', true, false).'
								</div>
								<div id="tab2" class="tab-pane p20">
									<div id="_div"></div>
								</div>
							</div>
						</div>
						<div class="btn-group">
						  <input type="submit" class="btn btn-primary btn-parsley" id="sub" value="' . $dosave . '" />
						</div>';
						if(isset($nid)) 
						{
							$adminTpl->footIncludes[] = '<script>
									$(\'#admin-form\').submit(function (e) {
										var form = this;
										e.preventDefault();
										setTimeout(function () {
											form.submit();
										}, 1000); // in milliseconds
									});
									</script>';		
								echo '	<input type="hidden" name="edit" value="1" />						
							<input type="hidden" name="edit_id" value="'.$id.'" />';
							echo '<input type="hidden" name="oldAltName" value="'.$altname.'" />';
						}				
				echo'</form>
					</div>
				</section>';	
		$adminTpl->admin_foot();
} 

function content_save() 
{
global $adminTpl, $core, $db, $cats, $groupss, $config, $content_conf, $lang;
	$word_counter = new Counter();
	$bb = new bb;
	$gen_tag = $word_counter->get_keywords($_POST['text'][$config['lang']]);
	$title = filter(trim($_POST['title']), 'title');
	$langTitle = isset($_POST['langtitle']) ? $_POST['langtitle'] : '';
	$langTitle[$config['lang']] = $title;
	$fulltitle[$config['lang']] = isset($_POST['fulltitle']) ? $_POST['fulltitle'] : ''; 	
	$short = $_POST['text'];
	$theme = $_POST['theme'];
	$templ = $_POST['templ'];
	$tags = isset($_POST['tags']) ? mb_strtolower(filter($_POST['tags'], 'a')) : mb_strtolower(filter($gen_tag, 'a'));
	$translit = ($_POST['altname'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['altname'])) : translit($_POST['title']);
	$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';	
	
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	$cnt = $short['ru'];	
	$gen_tag =  $word_counter->get_keywords(substr($cnt, 0, 500)); 
	$keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500)); 	
	$newcnt = $bb->parse(processText(filter(fileInit('content', $edit_id, 'content', $cnt), 'html')), $edit_id, true);		
	$description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150); 	
	
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
	$cats = ',' . $cats;	
	$status = isset($_POST['status']) ? 1 : 0;
	$fix = isset($_POST['fix']) ? 1 : 0;
	$lln = $lang['static_adds'];
	if(isset($_POST['edit'])) 
	{
		$lln = $lang['static_edits'];
	}
	$adminTpl->admin_head($lang['modules'] . ' | ' . $lln);
	echo '<div id="content" class="animated fadeIn">';		
	if($title && $short[$config['lang']] && $translit) 
	{
		if(isset($_POST['edit'])) 
		{			
			$edit = intval($_POST['edit_id']);
			foreach($langTitle as $k => $v)
			{
				$ntitle = filter(trim($v), 'title');
				$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
				$text = filter(fileInit('content', $edit, 'content', $short[$k]), 'html');				
				if(isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `fulltitle` , `short` , `lang` ) VALUES ('" . $edit . "', 'content', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql(parseBB(processText($text), $edit, true)) . "', '" . $k . "');");
				}
				elseif(!isset($_POST['empty'][$k])  && (trim($v) == '' OR trim($short[$k]) == ''))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` ='" . $edit . "' AND `module` ='content' AND `lang`='" . $k . "' LIMIT 1");
				}
				elseif(!isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($ntitle)) . "', `fulltitle` = '" . $db->safesql(processText($ftitle)) . "', `short` = '" . $db->safesql(parseBB(processText($text), $edit, true)) . "' WHERE `postId` ='" . $edit . "' AND `module` ='content' AND `lang`='" . $k . "' LIMIT 1 ;");
				}
			}			
			$db->query("UPDATE `" . DB_PREFIX . "_content` SET `translate` = '" . $translit . "', `cat` = '" . $cats . "', `keywords` = '" . $tags . "', `description` = '" . $description . "', `theme` = '" . $theme . "', `active` = '" . $status . "' WHERE `id` =" .$edit . " LIMIT 1 ;");
			$adminTpl->info($lang['static_edit_ok'], 'info', null, $lang['info'], $lang['static_list'], ADMIN.'/module/content/');
			$nnid = $edit;
		} 
		else 
		{			
			if($db->query("INSERT INTO `" . DB_PREFIX . "_content` ( `id` , `translate`, `cat` , `keywords` , `description` , `active` , `date` , `theme` ) VALUES (NULL, '" . $translit . "', '" . $cats . "', '" . $tags . "', '" . $description . "', '" . $status . "', '" . time() . "', '" . $theme . "');")) 
			{
				$adminTpl->info($lang['static_add_ok'], 'info', null, $lang['info'], $lang['static_list'], ADMIN.'/module/content/');
			}			
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_content WHERE translate = '" . $translit . "'");
			$content = $db->getRow($query);
			foreach($langTitle as $k => $v)
			{
				if(trim($v) != '' && trim($short[$k]) != '')
				{
					$ntitle = filter(trim($v), 'title');
					$ftitle = filter(trim(htmlspecialchars_decode($fulltitle[$k], ENT_QUOTES)), 'title');
					$text = fileInit('content', $content['id'], 'content', parseBB(processText(filter($short[$k], 'html'), $content['id'], true)));
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `fulltitle` , `short` , `lang` ) 	VALUES ('" . $content['id'] . "', 'content', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql(processText($ftitle)) . "', '" . $db->safesql($text) . "' , '" . $k . "');");
				}
			}
			fileInit('content', $content['id']);
			$nnid = $content['id'];
		}
		
		if (isset($_POST['preview_del']))
		{
			if(isset($_POST['edit'])) 
			{
				$queryPR = $db->query("SELECT preview FROM ".DB_PREFIX."_content WHERE id = '" . $nnid . "' LIMIT 1");
				$contentPR = $db->getRow($queryPR);
				if (file_exists(ROOT.$contentPR['preview'])) 
				{
					unlink(ROOT.$contentPR['preview']);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_content` SET `preview` = '' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
			}
		}
		else
		{
			if($_FILES['preview']['size'] > 0) 
				{	
					$purl = 'files/content/'.$nnid.'/';					
					$path_info = pathinfo($_FILES['preview']['name']);
					$ext = $path_info['extension'];					
					$file_full = $purl.'preview_' .$nnid.'.'.$ext;
					if (file_exists(ROOT.$file_full)) 
					{
						@unlink(ROOT.$file_full);
					}
					if($foo = new Upload($_FILES['preview']))
					{
						$foo->file_new_name_body = 'preview_' .$nnid;
						$foo->image_resize = false;
						$foo->image_x = $content_conf['thumb_width'];
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
					$db->query("UPDATE `" . DB_PREFIX . "_content` SET `preview` = '/" . $file_full . "' WHERE `id` = '" . $nnid . "' LIMIT 1 ;");
				}
		}

		if ($theme == '0') {$theme='';}
		if(is_writable(ROOT .'usr/tpl/'.$config['tpl'].'/content/'.$theme.'/content-view.tpl'))
		{
			$fp = @fopen(ROOT .'usr/tpl/'.$config['tpl'].'/content/'.$theme.'/content-view.tpl', 'w');
			fwrite($fp, stripslashes($templ));
			fclose($fp);
		}		
	} else {
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}


function delete($id) {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_content` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $id . "' AND `module` = 'content'");
}


function activate($id) {
global $adminTpl, $db;
	$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
}

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;
	
	case "add":
		content_add();
	break;
	
	case "save":
		content_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		content_add($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		header('Location: /'.ADMIN.'/module/content');
	break;
	
	case "activate":
		$id = intval($url[4]);
		activate($id);
		header('Location: /'.ADMIN.'/module/content');
	break;	
	
	case "deactivate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		header('Location: /'.ADMIN.'/module/content');
	break;
	
	case "reactivate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_content`  SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
		header('Location: /'.ADMIN.'/module/content');
	break;

	case "action":
	$type = $_POST['act'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case "activate":
				foreach($_POST['checks'] as $id) {
					activate(intval($id));
				}
				break;			
			
			case "deActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "reActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
			
			case "delete":
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
				break;
		}
	}
		header('Location: /'.ADMIN.'/module/content');
	break;
	
	case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{
				case "isurl":					
					if(isset($_POST['altname']))
					{ 
						if(!preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['altname']))
						{
							echo(json_encode($lang['static_add_url_err_2']));
						}
						else
						{
							
							$query = $db->query("SELECT * FROM ".DB_PREFIX."_content WHERE translate = '" . $db->safesql($_POST['altname']) . "'");
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
				
				case "loadtpl":
					$file = (isset($url[5]) && file_exists('usr/tpl/' . $config['tpl'] . '/content/'.$url[5].'/content-view.tpl')) ? $url[5] : '';
					$text = htmlspecialchars(file_get_contents(ROOT . 'usr/tpl/' . $config['tpl'] . '/content/'.$file.'/content-view.tpl'), ENT_QUOTES);
					$count_rows = count(explode("\n", $text))*20;					
					echo '<textarea name="templ" class="textarea" id="_code" style="width: 100%; height: 280px">' .$text . '</textarea>'	;
				break;
			}
	break;
	
	case 'config':
		require (ROOT.'etc/content.config.php');		
		$configBox = array(
			'content' => array(
				'varName' => 'content_conf',
				'title' => $lang['static_config_title'],
				'groups' => array(
					'main' => array(
						'title' => $lang['config_main'],
						'vars' => array(
							'num' => array(
								'title' => $lang['static_config_post'],
								'description' => $lang['static_config_post_t'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'comments_num' => array(
								'title' => $lang['static_config_com'],
								'description' => $lang['static_config_com_t'],
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'allowComm' => array(
								'title' => $lang['static_config_comp'],
								'description' => $lang['static_config_comp_t'],
								'content' => radio("allowComm", $content_conf['allowComm']),
							),
							'thumb_width' => array(
								'title' => $lang['static_config_thumb'],
								'description' => $lang['static_config_thumb_t'],
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
		generateConfig($configBox, 'content', '{MOD_LINK}/config', $ok);
		break;
}
