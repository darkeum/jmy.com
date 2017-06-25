<?php

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

function feedback_users() 
{
require (ROOT.'etc/feedback.config.php');	
global $adminTpl, $core, $db, $admin_conf, $feedback_conf;
$adminTpl->admin_head('Обратная Связь');
$page = init_page();
$limit = ($page-1)*$feedback_conf['users_num'];
echo '
<div class="row">
<div class="col-lg-12">
<section class="panel">
<div class="panel-heading">
</div>';
$query = $db->query("SELECT * FROM " . DB_PREFIX . "_feedback ORDER BY id DESC  " . $id . "");
if($db->numRows($query) > 0)
{
echo '
<div class="panel-body no-padding">
<table class="no-margin">
<thead>
<tr>
<th width="10%">' . 'ID' . '</th>
<th width="15%">' . 'IP' . '</th>
<th width="10%">' . 'Имя' . '</th>
<th width="15%" class="text-center">' . 'Почта' . '</th>
<th width="15%" class="text-center">' . 'Дата' . '</th>
<th width="15%" class="text-center">' . 'Тема' . '</th>
<th width="35%" class="text-center">' . 'Сообщение' . '</th>
<th width="15%" class="text-center">' . 'Действие' . '</th>

</tr>
</thead>
<tbody>
';
while($feedback = $db->getRow($query)) 
{

echo '

<tr '.(($feedback['message'] == 0) ? 'class="danger"' : '' ).'>
<td>'.$feedback['id'].'</td>
<td>'.$feedback['ip'].'</td>
<td>'.$feedback['nick']. ''.$feedback['name'].'</td>
<td class="text-center">'.$feedback['email'].'</td>
<td class="text-center">'.formatDate($feedback['date']).'</td>
<td class="text-center">'.$feedback['title'].'</td>
<td class="text-center">'.$feedback['message'].'</td>
<td class="text-center"><a href="{MOD_LINK}/delete/' . $feedback['id'] . '" onClick="return getConfirm(\'' . _DELETE .' - ' . $feedback['id'] . '?\')">
<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DELETE .'">X</button><br/>
<br/></a></td>
</tr>';
}
echo '
</tbody>
</table>
</div>';
echo '
<div class="panel-heading">';
$all_query = $db->query("SELECT * FROM " . DB_PREFIX . "_feedback ");
$all = $db->numRows($all_query);
echo '
</div>';
}
else 
{ 
echo '<div class="panel-heading">' . 'Список Пуст' . '</div>';			
}
echo '
</section>
</div>
</div><br/>';
$adminTpl->admin_foot();
}


function delete($id) {
global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_feedback` WHERE `id` = " . $id . " LIMIT 1");	
}

switch(isset($url[3]) ? $url[3] : null) {
	default:
	

	case "users":
		feedback_users();
	break;

	case "delete":
		$id = intval($url[4]);
		delete($id);
		header('Location: /'.ADMIN.'/module/feedsend');
	break;	
		
}






