<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Anton Goncharenko, Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

function blog_main() 
{
global $adminTpl, $core, $db, $admin_conf;
	$adminTpl->admin_head(_MODULES .' | '. _AP_BLOG);
	$page = init_page();
	$limit = ($page-1)*$admin_conf['num'];	
		echo '<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<div class="panel-heading">
						<b>'._AP_BLOG_LIST.'</b>
					</div>';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_blogs ORDER BY id ASC LIMIT " . $limit . ", " . $admin_conf['num'] . "");
	if($db->numRows($query) > 0) {	
	echo '<div class="panel-body no-padding">
					<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{MOD_LINK}/action">
						<table class="table no-margin">
							<thead>
								<tr>
									<th><span class="pd-l-sm"></span>ID</th>
									<th class="col-md-3">' . _AP_BLOG_NAME . '</th>
									<th class="col-md-2">' . _DATE . '</th>
									<th class="col-md-4">' . _AP_BLOG_A_COUNT .'</th>
									<th class="col-md-2">' . _ACTIONS . '</th>									
								</tr>
							</thead>
							<tbody>';
	while($blog = $db->getRow($query)) 
		{			
			echo '
				<tr>
				<td><span class="pd-l-sm"></span>' .  $blog['id'] . '</td>
				<td><a target="_blank" class="tooltip1" href="/blog/view/' . $blog['altname'] . '">' . $blog['title'] . '<span><img src="' . (isset($blog['avatar']) ? $blog['avatar'] : 'files/blog/default-blog-avatar.png') . '"/></span></a></td>
				<td>' . formatDate($blog['date'], true) . '</td>
				<td>' . $blog['posts'] . '</td>
				<td>		
				<a href="{MOD_LINK}/blog_edit/' . $blog['id'] . '">
				<button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _EDIT .'">E</button>
				</a>
				<a href="{MOD_LINK}/blog_delete/' . $blog['id'] . '" onClick="return getConfirm(\'' . _AP_BLOG_DEL .' - ' . str($blog['title'], 15) . '?\')">
				<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DELETE .'">X</button>
				</a>
				</td>
				
			</tr>';	
		}		
		echo '<tr><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>';		
	echo '
	</form></div>';		
	} 	
	else {
		echo '<div class="panel-heading">' . _AP_BLOG_EMPTY . '</div>';
	}
	echo'</section></div></div>';	
	$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_blogs ");
	$all = $db->numRows($all_query);
	$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/blog/{page}');
	$adminTpl->admin_foot();	
}

function blog_add($bid = null) 
{
global $adminTpl, $core, $db, $admin_conf;
require (ROOT.'etc/blog.config.php');
	if(isset($bid))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_blogs WHERE id = '" . $bid . "'");
		$blog = $db->getRow($query);
		$id = $blog['id'];
		$title = $blog['title'];
		$description = $blog['description'];
		$altname = $blog['altname'];
		$tit = _AP_BLOG_UPDATE;
		$dosave = _UPDATE;
	}
	else
	{
		$id = false;
		$title = false;
		$description = false;
		$altname = false;
		$tit = _AP_BLOG_CREATE;
		$dosave = _ADD;
	}
	$adminTpl->admin_head(_MODULES . ' | ' . $tit);
	echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>'. $tit .'</b></div><div class="panel-body"><div class="switcher-content">';
echo '
<form class="form-horizontal parsley-form" action="{MOD_LINK}/blog_save" method="post" enctype="multipart/form-data">
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_NAME.': </label>
<div class="col-sm-4">
<input class="form-control" name="title" value="'.$title.'" autocomplete="off" type="text">
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_ALT.': </label>
<div class="col-sm-4">
<input class="form-control" name="altname" value="'.$altname.'" autocomplete="off" type="text">
<span class="help-block">'._AP_BLOG_ALT_HELP.'</span>
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_DESCRIPTION.': </label>
<div class="col-sm-4">
<textarea class="form-control" name="description" id="description" rows="3">'.$description.'</textarea>
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_AVATAR.':</label>
<div class="col-sm-4">
<div class="media no-margin-top">
<div class="media-left">
<a href="#"><img src="' . (isset($blog['avatar']) ? $blog['avatar'] : 'media/avatar/default-blog-avatar.png') . '" style="width: 58px; height: 58px;" class="img-rounded" alt=""></a>
</div>
<br>
<div class="media-body">
<input type="file" class="file-styled" name="blogAvatar" id="blogAvatar">
<span class="help-block">'._AP_BLOG_AVATAR_FORMAT.'</span>
</div>
</div>
</div>
</div>';
if(isset($bid)) echo "<input type=\"hidden\" name=\"edit\" value=\"$bid\" />";
echo '
<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. $dosave .'" />						
					</div>
		</div>
		</form></div></div>';  
	echo'</section></div></div>';
$adminTpl->admin_foot();
}

function blog_save() 
{
global $adminTpl, $config, $db, $core;
require (ROOT.'etc/blog.config.php');
		$bid = isset($_POST['bid']) ? intval($_POST['bid']) : '';
		$title = filter($_POST['title'], 'title');
		$description = filter($_POST['description']);
		$altname = !empty($_POST['altname']) ? translit(filter($_POST['altname'], 'a')) : translit($title);
		$adminTpl->admin_head(_MODULES . ' | ' . _AP_BLOG_UPDATE2);
		if($title && $description && $altname)
			{
				if(isset($_POST['edit']))
				{
					$edit = intval($_POST['edit']);
					$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_blogs` WHERE id = '" . $bid . "' LIMIT 1");
					if($db->numRows($query) == 0)
					{
						$blog = $db->getRow($query);
						$isAdmin = ($core->auth->isAdmin || eregStrt(','.$core->auth->user_info['id'].',', $blog['admins'])) ? true : false;
						if($isAdmin == true)
						{
							if(isset($_POST['deleteAvatar']))
							{
								@unlink($blog['avatar']);
								$avatar = '';
							}
							
							if(!empty($_FILES['blogAvatar']['name']))
							{
								@unlink($blog['avatar']);
								
								if($foo = new Upload($_FILES['blogAvatar']))
								{
									$foo->file_new_name_body = 'blogAvatar_'.$altname;
									$foo->image_resize = true;
									$foo->image_x = 50;
									$foo->image_ratio_y = true;
									$foo->Process(ROOT.'files/blog');
									
									if ($foo->processed) 
									{
										$avatar = 'files/blog/blogAvatar_'.$altname.'.'.$foo->file_dst_name_ext;
										$foo->Clean();
									}
								}
							}										
							$update = $db->query("UPDATE `" . DB_PREFIX . "_blogs` SET `title` = '" . $db->safesql(processText($title)) . "', `altname` = '" . $db->safesql(processText($altname)) . "', `description` = '" . $db->safesql(processText($description)) . "'" . (isset($avatar) ? ", `avatar` = '" . $avatar . "'" : '') . " WHERE `id` =" . $edit . ";");
							if($update)
							{
								$adminTpl->info(_AP_BLOG_UPDATE_OK);
							}
						}
					}
				}
				else
				{

					$adminTpl->admin_head(_MODULES . ' | ' . _AP_BLOG_CREATE2);	
					$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_blogs` WHERE title = '" . $title . "' OR altname = '" . $db->safesql($altname) . "'");
					if($db->numRows($query) == 0)
					{
						$avatar = '';
						if(!empty($_FILES['blogAvatar']['name']))
						{
							if($foo = new Upload($_FILES['blogAvatar']))
							{
								$foo->file_new_name_body = 'blogAvatar_'.$altname;
								$foo->image_resize = true;
								$foo->image_x = 50;
								$foo->image_ratio_y = true;
								$foo->Process(ROOT.'files/blog');
								
								if ($foo->processed) 
								{
									$avatar = 'files/blog/blogAvatar_'.$altname.'.'.$foo->file_dst_name_ext;
									$foo->Clean();
								}
							}
						}
						$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_blogs` (`title` ,`altname` ,`description` ,`avatar` ,`date` ,`admins`) VALUES ('" . $db->safesql(processText($title)) . "', '" . $altname . "', '" . $db->safesql(processText($description)) . "', '" . $avatar . "', '" . time() . "', '," . $core->auth->user_info['id'] . ",');");
						if($insert)
						{
						$adminTpl->info(_AP_BLOG_CREATE_OK);
						}
					}

				}
			}
			else 
	{
		$adminTpl->info(_AP_BLOG_CREATE_ERROR, 'error');
	}
			$adminTpl->admin_foot();
}	

function articles_main() 
	{
	global $adminTpl, $core, $db, $admin_conf;
	$adminTpl->admin_head(_MODULES .' | '. _AP_BLOG .' | '. _AP_BLOG_A);
	$page = init_page();
	$limit = ($page-1)*$admin_conf['num'];
	echo '<div class="row">
				<div class="col-lg-12">
					<section class="panel">
						<div class="panel-heading">
							<b>'._AP_BLOG_LIST_ARTICLES.'</b>
						</div>';
		$queryP = $db->query("SELECT u.nick, b.* FROM `" . DB_PREFIX . "_blog_posts` as b LEFT JOIN `" . DB_PREFIX . "_users` as u ON (u.id = b.uid) ORDER BY b.date DESC LIMIT " . $limit . ", ".$admin_conf['num']."");
		if($db->numRows($queryP) > 0)
		{	
		echo '<div class="panel-body no-padding">
						<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{MOD_LINK}/action">
							<table class="table no-margin">
								<thead>
									<tr>
										<th><span class="pd-l-sm"></span>ID</th>
										<th class="col-md-3">' . _TITLE . '/' . _AP_BLOG_BLOG . '</th>
										<th class="col-md-2">' . _AUTHOR . '</th>
										<th class="col-md-2">' . _DATE . '</th>
										<th class="col-md-2">' . _COMMENTS .'</th>
										<th class="col-md-4">' . _ACTIONS . '</th>									
									</tr>
								</thead>
								<tbody>';
			$query = $db->query("SELECT id, title, altname FROM `" . DB_PREFIX . "_blogs`");
			while($blog = $db->getRow($query)) $blogName[$blog['id']] = array($blog['title'], $blog['altname']);
			while($posts = $db->getRow($queryP)) 
			{		
				$blogTitle = $posts['bid'] == 0 ? '<a target="_blank" href="blog/user/' . $posts['uid'] . '"><font color="#333">'._AP_BLOG_FROM.' '.$posts['nick'].'</font></a>' : '<a target="_blank" href="blog/view/' . $blogName[$posts['bid']][1] . '"><font color="#333">' . $blogName[$posts['bid']][0] . '</font></a>';			
				$status_icon = ($posts['status'] == 0) ? '<a href="{MOD_LINK}/article_activate/' . $posts['id'] . '" onClick="return getConfirm(\'' . _ACTIVATE .' - ' . $posts['title'] . '?\')"><button  type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _ACTIVATE .'">A</button></a>' : '<a href="{MOD_LINK}/article_deactivate/' . $posts['id'] . '" onClick="return getConfirm(\'' . _DEACTIVATE .' - ' . $posts['title'] . '?\')" ><button  type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DEACTIVATE .'">A</button></a>';
				echo '
					<tr '.(($posts['status'] == 0) ? 'class="danger"' : '' ).'>
					<td><span class="pd-l-sm"></span>' .  $posts['id'] . '</td>
					<td><a target="_blank" href="blog/read/' . $posts['id'] . '" target="_blank">' .strip_tags(str($posts['title'], 40)) . '</a> / '.$blogTitle.'</td>
					<td><a href="profile/'.$posts['nick'].'" title="'.$posts['nick'].'">'.$posts['nick'].'</a></td>
					<td>' . formatDate($posts['date'], true)  . '</td>
					<td>' .$posts['comments']  . '</td>
					<td>	
					'.$status_icon.'	
					<a href="{MOD_LINK}/article_edit/' . $posts['id'] . '">
					<button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _EDIT .'">E</button>
					</a>
					<a href="{MOD_LINK}/article_delete/' . $posts['id'] . '" onClick="return getConfirm(\'' . _AP_BLOG_DEL .' - ' . str($blog['title'], 15) . '?\')">
					<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DELETE .'">X</button>
					</a>
					</td>
					
				</tr>';	
			}		
			echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>';		
		echo '
		</form></div>';		
		} 	
		else {
			echo '<div class="panel-heading">' ._AP_BLOG_A_EMPTY . '</div>';
		}
		echo'</section></div></div>';	
	$all_query = $db->query("SELECT * FROM ".DB_PREFIX."_blog_posts ");
	$all = $db->numRows($all_query);
	$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/blog/articles/{page}');
	$adminTpl->admin_foot();
}

function article_add($pid = null) 
{
global $adminTpl, $core, $db, $admin_conf;
require (ROOT.'etc/blog.config.php');
	if(isset($pid))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_blog_posts WHERE id = '" . $pid . "'");
		$posts = $db->getRow($query);
		$id = $posts['id'];
		$title = $posts['title'];
		$postText = $posts['text'];
		$tags = $posts['tags'];
		$note = ($posts['note'] == 1 ? 0 : $posts['note']);
		$query = $db->query("SELECT id, title FROM `" . DB_PREFIX . "_blogs`");
		$blogList = '<option value="0">'._AP_BLOG_PERSONAL.'</option><option disabled>---------</option>';
		while($blogs = $db->getRow($query)) 
		{
			$blogList .= '<option value="' . $blogs['id'] . '" ' . ($posts['bid'] == $blogs['id'] ? 'selected' : '') . '>' . $blogs['title'] . '</option>';
		}
		$tit = _AP_BLOG_A_UPDATE;
		$dosave = _UPDATE;
	}
	else
	{
		$id = false;
		$title = false;
		$postText = false;
		$tags = false;
		$note = 0;
		$query = $db->query("SELECT id, title FROM `" . DB_PREFIX . "_blogs`");
		$blogList = '<option value="0">'._AP_BLOG_PERSONAL.'</option><option disabled>---------</option>';
		while($blogs = $db->getRow($query)) 
		{
			$blogList .= '<option value="' . $blogs['id'] . '" ' . (isset($url[2]) && $url[2] == $blogs['id'] ? 'selected' : '') . '>' . $blogs['title'] . '</option>';
		}
		$tit = _AP_BLOG_A_CREATE;
		$dosave = _ADD;
	}
	$adminTpl->admin_head(_MODULES . ' | ' . $tit);
	echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>'. $tit .'</b></div><div class="panel-body"><div class="switcher-content">';
echo '<form class="form-horizontal" action="{MOD_LINK}/article_save" method="post" enctype="multipart/form-data">
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_A_NAME.': </label>
<div class="col-sm-4">
<input class="form-control" name="title" value="'.$title.'" autocomplete="off" type="text">
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_CHOOSE.': </label>
<div class="col-sm-4">
<select name="blog" class="form-control" style="width:auto;">';
echo $blogList;
echo'
</select>
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_TAGS.':</label>
<div class="col-sm-4">
<input type="text" name="tags" value="'. $tags .'" class="form-control" id="tags">
<span class="help-block">'._AP_BLOG_TAGS_HELP.'</span>
</div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">'._AP_BLOG_DRAFT.':</label>
<div class="col-sm-4">
'.radio('note', $note).'
<span class="help-block">'._AP_BLOG_DRAFT_HELP.'</span>
</div>
</div>
</div>
</div>

</section></div></div>

<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<header class="panel-heading">'. _AP_BLOG_A_DESC .'</header>			
					<div class="panel-body">
						<div class="form-horizontal bordered-group">									
							<div class="form-group">								
								<div style="padding-left:34px;padding-right:34px"  class="col-sm-12">'
									.adminArea('postText', $postText, 5, 'textarea', false, true).'
									
								</div>
								<div style="padding-left:17px;">
								<input  name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="' . $dosave . '" />
								</div>
							</div>
						</div>
					</div>	';
				if(isset($pid)) echo "<input type=\"hidden\" name=\"edit\" value=\"$pid\" />";
		echo '</form></div></div>';  
	echo'</section></div></div>';
$adminTpl->admin_foot();
} 

function article_save() 
{
global $adminTpl, $config, $db, $core;
require (ROOT.'etc/blog.config.php');
		$blog = isset($_POST['blog']) ? intval($_POST['blog']) : '';
		$title = isset($_POST['title']) ? filter($_POST['title'], 'title') : '';
		$postText = isset($_POST['postText']) ? filter($_POST['postText']) : '';
		$tags = isset($_POST['tags']) ? filter($_POST['tags'], 'a') : '';
		$note = !empty($_POST['note']) ? 1 : 0;
		$pid = !empty($_POST['pid']) ? intval($_POST['pid']) : '';						
		$blogCheck = $db->query("SELECT altname FROM `" . DB_PREFIX . "_blogs` WHERE id = '" . $blog . "' LIMIT 1");
		$adminTpl->admin_head(_MODULES . ' | ' . _AP_BLOG_A_UPDATE);
		if(!empty($title) && !empty($postText) && ($db->numRows($blogCheck) == 1 || $blog == 0))
		{
			if($blog != 0) $blogInfo = $db->getRow($blogCheck);
			
		if(isset($_POST['edit']))
			{
				$edit = intval($_POST['edit']);
				$update = $db->query("UPDATE `" . DB_PREFIX . "_blog_posts` SET `title` = '" . $db->safesql(processText($title)) . "', `text` = '" . $db->safesql(parseBB(processText($postText))) . "', `tags` = '" . $db->safesql(processText($tags)) . "', `status` = '" . ($note == 1 ? 2 : 1) . "' WHERE `id` =" . $edit . ";");
				if($update)
				{
				$adminTpl->info(_AP_BLOG_A_UPDATE_OK);
				}
			}
			else
			{
				$adminTpl->admin_head(_MODULES . ' | ' . _AP_BLOG_A_CREATE);	
				$t = time();
				$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_blog_posts` (`bid` ,`title` ,`text` ,`date` ,`tags` ,`uid` ,`status` ) VALUES ('" . $blog . "', '" . $db->safesql(processText($title)) . "', '" . $db->safesql(parseBB(processText($postText))) . "', '" . $t . "', '" . $tags . "', '" . $core->auth->user_info['id'] . "', '" . ($note == 1 ? 2 : 1) . "');");
				if($insert) 
				{
				if($blog != 0)
				{
				$db->query("UPDATE `" . DB_PREFIX . "_blogs` SET `posts` = `posts`+1,`lastUpdate` = '" . $t . "' WHERE `id` =" . $blog . ";");
				}
				$adminTpl->info(_AP_BLOG_A_ADD_OK);
				}
			}				
		}
			else 
			{
		$adminTpl->info(_AP_BLOG_A_CREATE_ERROR, 'error');
			}
		$adminTpl->admin_foot();
}

function blog_delete($id) {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_blogs` WHERE `id` = " . $id . " LIMIT 1");	
}

switch(isset($url[3]) ? $url[3] : null) {
	default:
		blog_main();
	break;

	case "blog_add":
		blog_add();
	break;
	
	case "blog_save":
		blog_save();
	break;

	case "blog_edit":
		$id = intval($url[4]);
		blog_add($id);
	break;

	case "blog_delete":
		$id = intval($url[4]);
		blog_delete($id);
		header('Location: /'.ADMIN.'/module/blog');
	break;

	case "articles";
		articles_main();
		$where = '';
	break;

	case "article_add":
		article_add();
	break;

	case "article_save":
		article_save();
	break;

	case "article_edit":
		$id = intval($url[4]);
		article_add($id);
	break;

	case "article_activate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_blog_posts` SET `status` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_blog_posts WHERE id = '" . $id . "'");
		location(ADMIN.'/module/blog/articles');
	break;

	case "article_deactivate":
	global $adminTpl, $db;
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_blog_posts` SET `status` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_blog_posts WHERE id = '" . $id . "'");
		location(ADMIN.'/module/blog/articles');
	break;
	
	case 'config':
		require (ROOT.'etc/blog.config.php');
		
		$configBox = array(
			'blog' => array(
				'varName' => 'blog_conf',
				'title' => _AP_BLOG_CONF,
				'groups' => array(
					'main' => array(
						'title' => _AP_BLOG_CONF_MAIN,
						'vars' => array(							
							'blogsPerPage' => array(
								'title' => _AP_BLOG_CONF_B_COUNT,
								'description' => _AP_BLOG_CONF_B_COUNT_HELP,
								'content' => '<input type="text" name="{varName}" value="{var}" class="form-control" id="{varName}">',
							),
							'postsPerPage' => array(
								'title' => _AP_BLOG_CONF_A_COUNT,
								'description' => _AP_BLOG_CONF_A_COUNT_HELP,
								'content' => '<input type="text" name="{varName}" value="{var}" class="form-control" id="{varName}">',
							),							
							'preModer' => array(
								'title' => _AP_BLOG_CONF_A_MODER,
								'description' => _AP_BLOG_CONF_A_MODER_HELP,
								'content' => radio("preModer", $blog_conf['preModer']),
							),	
							'comments' => array(
								'title' => _AP_BLOG_CONF_A_COMM,
								'description' => _AP_BLOG_CONF_A_COMM_HELP,
								'content' => radio("comments", $blog_conf['comments']),
							),		
							'comperpage' => array(
								'title' => _AP_BLOG_CONF_COMM_COUNT,
								'description' => _AP_BLOG_CONF_COMM_COUNT_HELP,
								'content' => '<input type="text" name="{varName}" value="{var}" class="form-control" id="{varName}">',
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
		
		generateConfig($configBox, 'blog', '{MOD_LINK}/config', $ok);
		break;

}
