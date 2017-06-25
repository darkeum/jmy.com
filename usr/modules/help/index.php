<?php

 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

require_once ROOT . 'etc/help.config.php';
$mod = $url[0];

function menu($title )
{
global $core, $url;
	$mod = $url[0];
	$core->tpl->title($title);
	$core->tpl->open('menu');
	$core->tpl->loadFile('help/menu');
	$core->tpl->end();
	$core->tpl->close();
}

switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		$page = init_page();
		$cut = ($page-1)*$help['num'];
		set_title(array(_F_FAQ));
		menu();
		
		$query = $db->query("SELECT q.*, u.nick FROM " . DB_PREFIX . "_help AS q LEFT JOIN " . DB_PREFIX . "_users AS u ON (q.uid = u.id) ORDER BY " . (isset($_GET['rated']) ? 'replies' : 'time') . " DESC LIMIT " . $cut . ", " . $help['num'] . "");
		
		if($db->numRows($query) > 0) 
		{
			while($rows = $db->getRow($query)) 
			{
				$core->tpl->loadFile('help/main');
				$core->tpl->setVar('AVATAR', avatar($rows['uid']));
				$core->tpl->setVar('NICK', $rows['nick']);
				$core->tpl->setVar('TIME', formatDate($rows['time']));
				$core->tpl->setVar('TITLE', $rows['title']);
				$core->tpl->setVar('QUESTION', $rows['question']);
				$core->tpl->setVar('REPLIES', $rows['replies']);
				$core->tpl->setVar('EDIT', ($core->auth->isAdmin == true OR $core->auth->user_id == $rows['uid']) ? '<a href="' . $mod . '/delete/' . $rows['id'] . '" onclick="return confirm(\'Уверены что хотите удалить вопрос?\');">'._DELETE.'</a> | <a href="' . $mod . '/edit/' . $rows['id'] . '">'._EDIT.'</a>' : '');
				$core->tpl->sources = preg_replace("#\\{%MORE%\\}(.*?)\\{/%MORE%\\}#ies","format_link('\\1', '" . $mod . "/view/" . $rows['id'] . "')", $core->tpl->sources);
				$core->tpl->end();
			}
		}
		
		list($all) = $db->fetchRow($db->query("SELECT COUNT(id) FROM " . DB_PREFIX . "_help"));
		$core->tpl->pages($page, $help['num'], $all, 'help');
		break;
		
	case 'view':
		$id = intval($url[2]);
		
		$query = $db->query("SELECT q.*, u.nick FROM " . DB_PREFIX . "_help AS q LEFT JOIN " . DB_PREFIX . "_users AS u ON (q.uid = u.id) WHERE q.id = '" . $id . "' LIMIT 1");
		
		if($db->numRows($query) > 0) 
		{
			$rows = $db->getRow($query);
			set_title(array('' ._F_QUESTION.': ' . $rows['title']));
			menu($rows['title']);
			$core->tpl->loadFile('help/view');
			$core->tpl->setVar('AVATAR', avatar($rows['uid']));
			$core->tpl->setVar('NICK', $rows['nick']);
			$core->tpl->setVar('TIME', formatDate($rows['time']));
			$core->tpl->setVar('TITLE', $rows['title']);
			$core->tpl->setVar('QUESTION', $rows['question']);
			$core->tpl->setVar('REPLIES', $rows['replies']);
			$core->tpl->sources = preg_replace("#\\{%MORE%\\}(.*?)\\{/%MORE%\\}#ies","format_link('\\1', '" . $mod . "/view/" . $rows['id'] . "')", $core->tpl->sources);
			$core->tpl->end();
			
			$query_reply = $db->query("SELECT r.*, u.nick FROM " . DB_PREFIX . "_help_replies AS r LEFT JOIN " . DB_PREFIX . "_users AS u ON (r.uid = u.id) WHERE r.to = '" . $id . "'");
			if($db->numRows($query_reply) > 0) 
			{
				while($reply_row = $db->getRow($query_reply))
				{
					$core->tpl->loadFile('help/reply-body');
					$core->tpl->setVar('AVATAR', avatar($reply_row['uid']));
					$core->tpl->setVar('NICK', $reply_row['nick']);
					$core->tpl->setVar('TIME', formatDate($reply_row['time']));
					$core->tpl->setVar('REPLY', $reply_row['text']);
					$core->tpl->end();
				}
			}
			else
			{
				$noRepl = true;
			}
			
			if($core->auth->isUser)
			{
				if(isset($noRepl))
				{
					$core->tpl->info(''._F_USER_HELP.'');
				}
				
				$core->tpl->open();
				$core->tpl->loadFile('help/reply');
				$core->tpl->setVar('ID', $rows['id']);
				$core->tpl->end();
				$core->tpl->close();
			}
			else
			{
				$core->tpl->info(''._F_USER_AUTH_ANSWER.'');		
			}
		}
		else
		{
			location('/' . $mod);
		}
		break;
	
	case 'add':
		menu('');		
		set_title(array(''._F_FAQ.'', ''._F_ADD_QUESTION.''));
		if($core->auth->isUser)
		{
			$core->tpl->open();
			$core->tpl->loadFile('help/add');
			$core->tpl->end();
			$core->tpl->close();
		}
		else
		{
			$core->tpl->info(''._F_USER_AUTH_QUESTION.'');
		}
		
		break;
		
	case 'send':
	    menu('');
		set_title(array(''._F_FAQ.'', ''._F_SENDING.''));
		
		if($core->auth->isUser)
		{
			$title = !empty($_POST['title']) ? filter($_POST['title'], 'title') : '';
			$question = !empty($_POST['question']) ? filter($_POST['question']) : '';
			
			if(!empty($question) && !empty($title))
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_help` ( `id` , `title` , `question` , `uid` , `time` , `replies` , `lock` ) VALUES ('', '" . $title . "', '" . $question . "', '" . $core->auth->user_id . "', '" . time() . "', '0', '0');");
				$core->tpl->info(''._F_QUESTION_ADDED.'');
			}
			else
			{
				$core->tpl->info(''._F_FORM_ERROR.' <a href="javascript:history.back()" onclick="/' . $mod . '" >'._BACK.'</a>', 'warning');
			}
		}
		else
		{
			$core->tpl->info(''._F_USER_AUTH_QUESTION.'');
		}
		break;
		
	case 'reply':
		menu('');
		set_title(array(''._F_FAQ.'', ''._F_ANSWER.''));
		
		if($core->auth->isUser)
		{
			$id = intval($_POST['rid']);
			$reply = filter($_POST['reply'], 'a');
			if($db->numRows($db->query("SELECT id FROM " . DB_PREFIX . "_help")) > 0)
			{
				if(empty($reply))
				{
					$core->tpl->info(''._F_ANSWER_ERROR.' <a href="' . $mod . '/view/' . $id . '">'._BACK.'</a>.', 'warning');
				}
				else
				{
					$db->query("UPDATE `" . DB_PREFIX . "_help` SET replies = replies+1 WHERE `id` =" . $id . " LIMIT 1 ;");
					$db->query("INSERT INTO `" . DB_PREFIX . "_help_replies` ( `rid` , `to` , `text` , `time` , `uid` ) VALUES ('', '" . $id . "', '" . $reply . "', '" . time() . "', '" . $core->auth->user_id . "');");
					$core->tpl->info(''._F_ANSWER_ADDED.' <a href="' . $mod . '/view/' . $id . '">'._BACK.'</a>.');
				}
			}
			else
			{
				location();
			}
		}
		else
		{
			$core->tpl->info(''._F_USER_AUTH_QUESTION.'');
		}
		break;
		
	case 'delete':
		$id = intval($url[2]);
		set_title(array(''._F_FAQ.'', ''._F_REMOVE.''));
		
		if($core->auth->isUser)
		{
			$query = $db->query("SELECT uid FROM " . DB_PREFIX . "_help WHERE id = '" . $id . "'");
			list($uid) = $db->fetchRow($query);
			if($db->numRows($query) == 1)
			{
				if($core->auth->isAdmin OR $core->auth->user_id == $uid)
				{
					menu('');
					$db->query("DELETE FROM `" . DB_PREFIX . "_help` WHERE `id` = " . $id . " LIMIT 1");
					$db->query("DELETE FROM `" . DB_PREFIX . "_help_replies` WHERE `to` = " . $id . "");
					$core->tpl->info(''._F_REMOVE_SUCCESS.' <a href="' . $mod . '">'._BACK.'</a>');
				}
			}
			else
			{
				location();
			}
		}
		else
		{
			location();
		}
		break;
		
	case 'edit':
		if(isset($_POST['id']))
		{
			$id = intval($_POST['id']);
		}
		else
		{
			$id = intval($url[2]);
		}
		
		if($core->auth->isUser)
		{
			$query = $db->query("SELECT * FROM " . DB_PREFIX . "_help WHERE id = '" . $id . "'");
			if($db->numRows($query) == 1)
			{
				$rows = $db->getRow($query);
				if($core->auth->isAdmin OR $core->auth->user_id == $rows['uid'])
				{
					menu('');
					set_title(array(''._F_FAQ.'', ''._F_EDITING.''));
					
					if(isset($_POST['title']))
					{
						$title = !empty($_POST['title']) ? filter($_POST['title']) : '';
						$question = !empty($_POST['question']) ? filter($_POST['question']) : '';
						
						if(!empty($question) && !empty($title))
						{
							$db->query("UPDATE `" . DB_PREFIX . "_help` SET `title` = '" . $db->safesql($title) . "', `question` = '" . $db->safesql($question) . "' WHERE `id` =" . $id . " LIMIT 1 ;");
							$core->tpl->info(''._F_QUESTION_UPDATED.' <a href="' . $mod . '/view/' . $id . '">'._BACK.'</a>');
						}
						else
						{
							$core->tpl->info(''._F_SUBJECT_ERROR.' <a href="' . $mod . '/edit/' . $id . '">'._BACK.'</a>', 'warning');
						}
					}
					else
					{
						$core->tpl->open();
						$core->tpl->loadFile('help/edit');
						$core->tpl->setVar('TITLE', $rows['title']);
						$core->tpl->setVar('QUESTION', $rows['question']);
						$core->tpl->setVar('ID', $rows['id']);
						$core->tpl->end();
						$core->tpl->close();					
					}
				}
			}
			else
			{
				location('/' . $mod);
			}
		}
		else
		{
			location('/' . $mod);
		}
		break;
}