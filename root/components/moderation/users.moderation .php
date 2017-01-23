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
if(empty($url[1]))
{
	$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_news WHERE active=2'));
	if($count > 0)
	{
		$onModer['news'] = '<img src="media/admin/news.png" border="0" style="vertical-align:middle" /> <a href="administration/publications/mod/news">Новости: '.$count.'</a>';
	}
}
else
{
	$adminTpl->admin_head($lang['news_onmoder']);
	echo '<div id="content" class="animated fadeIn">';		
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];
	$where = '';
	$cat = 0;
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_news $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/module/news/{page}');
	$query = $db->query("SELECT n.*, l.*, c.id as cid, c.name, c.altname as alturl FROM ".DB_PREFIX."_news AS n LEFT JOIN ".DB_PREFIX."_categories AS c ON (n.cat=c.id) LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='news') WHERE n.active='2' ORDER BY n.date DESC LIMIT " . $cut . ", " . $admin_conf['num'] . "");	
	if($db->numRows($query) > 0) 
	{
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">' . $lang['news_onmoder_list'] . ':</span>  						
				</div>
              <div class="panel-body pn"> 
				<form id="tablesForm" method="POST" action="{ADMIN}/module/news/action&moderate">
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
							<li><a href="{ADMIN}/module/news/moder_moderation/'.$news['id'].'">' . $lang['do_active'] . '</a></li>
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
							<button type="button" onclick="location.href = \'{ADMIN}/module/news/delete/'.$news['id'].'&moderate\'" class="btn btn-danger">' . $lang['delete'] .'</button>
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
		$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_news WHERE active=2");
		$all = $db->numRows($all_query);
		$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/administration/publications/mod/news/{page}');
	} 
	else
	{
		$adminTpl->info($lang['news_onmoder_empty'], 'empty', null, $lang['news_onmoder_list'], $lang['news_list'], ADMIN.'/module/news/');	
	}	
	echo '</div>';
	$adminTpl->admin_foot();
}