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
switch(isset($url[2]) ? $url[2] : null) 
{
	default:
		$adminTpl->admin_head($lang['moder']);
		echo '<div id="content" class="animated fadeIn">';		
		$adminTpl->info($lang['moder_info'], 'info', null, $lang['moder']);
		echo '</div>';		
		$adminTpl->admin_foot();
	break;
		
	case 'mod':
		$mod = isset($url[3]) ? $url[3] : '';
		if(!empty($mod) && file_exists(ROOT.'usr/modules/' . $mod . '/admin/moderation.php'))
		{
			if(file_exists(ROOT . 'langs/'.$core->lang.'/modules/'.$mod.'/'.$core->lang.'.admin.lng'))
			{
				include(ROOT . 'langs/'.$core->lang.'/modules/'.$mod.'/'.$core->lang.'.admin.lng');
			}			
			require_once(ROOT.'usr/modules/' . $mod . '/admin/moderation.php');
		}
	break;
	case 'com':
		$com = isset($url[3]) ? $url[3] : '';
		if(!empty($com) && file_exists(ROOT.'root/components/moderation/' . $com . '.moderation.php'))
		{						
			require_once(ROOT.'root/components/moderation/' . $com . '.moderation.php');
		}
	break;
}