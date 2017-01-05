<?php 

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

require dirname(__FILE__) . '/define.php';
require_once ROOT . 'lib/php_funcs.php';
require_once ROOT . 'lib/global.php';
require_once ROOT . 'root/functions.php';

$lang = array();
if (!empty($_COOKIE['jmy_lang'])) 
	{
		$langs = $_COOKIE['jmy_lang'];	
	}
else 
	{
		$langs = 'ru';
	}
if(file_exists(ROOT . 'install/langs/'.$langs.'/'.$langs.'.install.lng'))
	{
		include(ROOT . 'install/langs/'.$langs.'/'.$langs.'.install.lng');
	}	

function head() 
{
	ob_start();
}

function foot($n_p = null, $header = true) 
{
	global $information, $title, $lang;
	$content = ob_get_contents();
	ob_end_clean();
	$meta = '';
	$img_bar = '';
	$nav_bar = '';
	$step = '';
	switch(isset($_GET['step']) ? ($_GET['step']) : null)
	{
		default:
			$step = 'Выбор языка / Choose language';
			break;	
			
		case 'language_set_ru':
			$step = 'Установка языка / install language';			
			$meta = '<meta http-equiv="refresh" content="0; URL=\'/install.php?step=0\'" />';
			break;
			
		case 'language_set_en':
			$step = 'Установка языка / install language';
			$meta = '<meta http-equiv="refresh" content="0; URL=\'/install.php?step=0\'" />';
			break;	

		case '0':
			$step = $lang['step_0_agreement'];
			break;	
			
		case '1':
			$step = $lang['step_1_title'];			
			break;
			
		case "2":
			$step = $lang['step_2_title'];
			break;
			
		case "chmod":
			$step = $lang['chmod_title'];
			break;
		
		case "3":
			$step = $lang['step_3_title'];
			break;			
		
		case "4":
			$step = $lang['step_4_title'];
			break;
		
		case "5":
			$step = $lang['step_5_title'];		
			break;
	}
	
	$html = file_get_contents('install/tpl/install.html');
	$html = str_replace('{%META%}', $meta, $html);
	$html = str_replace('{%CONTENT%}', $content, $html);
	$html = str_replace('{%THEME%}', '/install/tpl', $html);
	$html = str_replace('{%URL%}', $_SERVER['HTTP_HOST'], $html);
	$html = str_replace('{%VERSION%}', VERSION_ID, $html);
	$html = str_replace('{%INFORMATION%}', $information, $html);
	$html = str_replace('{%STEP%}', $step, $html);
	$html = str_replace('{%IMG_BAR%}', $img_bar, $html);
	$html = str_replace('{%TITLE%}', $title.$lang['install_jmy'], $html);
	$html = str_replace('{%NAV_BAR%}', '<b>' . $nav_bar . '</b>', $html);
	$html = str_replace('{%NO_PADDING%}', $n_p, $html);
	$html = str_replace('{%HEAD_VIEW%}', ($header) ? 'block' : 'none', $html);

	echo $html;
}

function language()
{
	global $information, $title, $lang;
	$title = 'Выбор языка / Choose language | ';
	head();
	echo '<style>
			.bg-ccc, .bg-ccc footer 
			{
				background-color: #ccc;
				color: #fff;
			}
		</style>';
	echo '<div align="center">		
	Для продолжения выберите язык / To continue, select the language</div><br /><br />';
	echo '<div class="col-sm-6">
			<section class="panel no-border profile-panel overflow-hidden">
				<div class="panel-body bg-ccc">
					<div class="watermark fa fa-globe"></div>
					<div class="mg-b-sm text-center text-white">
						<div class="mg-b-md">
							<img src="install/tpl/images/ru.png" class="avatar bordered-avatar img-circle pic" alt="Русский язык" title="Русский язык">
						</div>
						<p class="mb5 mt5">Русский язык</P>
						<small>Автор: Комаров Иван</small>
					</div>
				</div>
				<div class="panel-footer bg-white no-border">
					<div align="center">
						<button type="button" class="btn btn-success" onclick="window.location = \'install.php?step=language_set_ru\';" />Продолжить</button>
					</div>
				</div>
			</section>
		</div>
		<div class="col-sm-6">
			<section class="panel no-border profile-panel overflow-hidden">
				<div class="panel-body bg-ccc">
					<div class="watermark fa fa-globe"></div>
					<div class="mg-b-sm text-center text-white">
						<div class="mg-b-md">
							<img src="install/tpl/images/en.png" class="avatar bordered-avatar img-circle pic" alt="English" title="English">
						</div>
						<p class="mb5 mt5">English</P>
						<small>Author: Anton Pavlov</small>
					</div>
				</div>
				<div class="panel-footer bg-white no-border">
					<div align="center">
						<button type="button" class="btn btn-success" onclick="window.location = \'install.php?step=language_set_en\';" />Continue</button>
					</div>
				</div>
			</section>
		</div>';	
	foot(null, false);
}

function language_set($ll='ru')
{
	global $information, $title, $lang;
	setcookie ("jmy_lang", $ll,time()+3600);	
	$title = 'Установка языка / install language | ';
	head();
	echo 'Идёт установка языковых параметров, если установка автоматически не продолжилась, то нажмите на кнопку. / There is a setting language preferences. If the installation does not automatically continued, press the button.<br /><br />';	
	echo '<div align="center"><button type="button" class="btn btn-success" onclick="window.location = \'install.php?step=0\';" />Нажмите для продолжения / Tap to continue</button></div>';
	foot();
}

function license()
{	
	global $information, $title, $lang, $langs;
	$title = $lang['step_0_welcome'].' | ';
	head();
	echo $lang['step_0_welcome_text'].'<br /><br />';
	echo '<iframe style="height:250px; overflow:auto; width:750px; min-width: 650px; margin:0 auto; border:1px dashed #ccc; padding:5px;"  src="https://server.jmy.su/index.php?license_'.$langs.'" width="750" height="250" scrolling="auto" frameborder="0" allowtransparency="true" allowFullScreen="true" allowScriptAccess="always"></iframe>';
	echo '<br /><br /><div align="center"><button type="button" class="btn btn-success" onclick="window.location = \'install.php?step=1\';" />'.$lang['step_0_agreement_ok'].'</button></div>';
	foot();
}

function step1() {
global $information, $title, $lang, $langs;
	$title = $lang['step_1_title'].' | ';
	head();
	if (!is_writable('./etc/db.config.php'))
	{
		echo '<div class="alert alert-warning alert-dismissable">
				<strong>'.$lang['install_attention'].'</strong><br />'.$lang['step_1_error'].'
			</div><br />
			<div align="center"><button type="button" class="btn btn-success" onclick="window.location = \'install.php?step=1\';" /><i class="fa fa-refresh"></i> '.$lang['install_refresh'].'</button></div>';
	}
	else
	{
		echo $lang['step_1_desc'].'
		<form class="mt25" action="install.php?step=2" method="post">
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_1_mysql_server'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="dbhost"  data-parsley-required="true" data-parsley-trigger="change" value="localhost" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_1_mysql_user'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="dbuser" data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['step_1_mysql_user_pre'].'" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_1_mysql_name'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="dbname" data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['step_1_mysql_name_pre'].'" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_1_mysql_pass'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="dbpass" data-parsley-required="true" data-parsley-trigger="change" placeholder="'.$lang['step_1_mysql_pass_pre'].'" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_1_mysql_prefix'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="prefix" data-parsley-required="true" data-parsley-trigger="change" value="JMY_" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<br />
			<div align="center"><input type="submit" value="'.$lang['install_next'].'" class="btn btn-success" /></div>		
		</form>	';
	}
	foot();
}

function step2() 
{
global $information, $title, $lang;
	$title = $lang['step_2_title'].' | ';
	head();
	$dbhost = $_POST['dbhost'];
	$dbuser = $_POST['dbuser'];
	$dbpass = $_POST['dbpass'];
	$dbname = $_POST['dbname'];
	$prefix = !empty($_POST['prefix']) ? $_POST['prefix'] : 'JMY_';	
    $resource = mysql_pconnect($dbhost, $dbuser, $dbpass);
    if ($resource) 
	{
        if (!mysql_select_db($dbname)) 
		{
			echo '<div class="alert alert-warning alert-dismissable">
					<strong>'.$lang['install_attention'].'</strong><br />'.$lang['step_2_error_2'].' <i>' . $dbname . '</i>.
				</div>
				<br /><div align="center"> <a href="install.php?step=1" class="btn btn-danger">'.$lang['install_back'].'</a></div>';	
			$stop = 1;
		}
		else
		{
			if(isset($_POST['goCreate']) && isset($_POST['do']))
			{
				@mysql_query('SET NAMES utf8');
				if($_POST['do'] == 'install')
				{					
					$sql_create = file_get_contents('install/sql/sql_create.sql');
					$sql_create_massiv = split(";", $sql_create);
					echo $lang['step_2_desc'].'<br /><br />
									<table class="table">
                                        <thead>
                                            <tr>
                                                <th><span class="pd-l-sm"></span>'.$lang['install_action'].'</th>
                                                <th>'.$lang['step_2_table'].'</th>                                               
                                                <th class="text-right">'.$lang['install_status'].'</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
					foreach($sql_create_massiv as $query)
					{
						if(preg_match('#CREATE#i', $query)) 
						{
							preg_match('#`\[prefix\](.*)`#i', $query, $name);
							if(@mysql_query(str_replace('[prefix]', $prefix, $query) . ";", $resource))
							{
								echo '<tr>								
									<td><span class="pd-l-sm"></span>'.$lang['step_2_create'].'</td>
									<td><b>' . str_replace('[prefix]', $prefix, $name[0]) . '</b></td>
									<td class="text-right"><span class="label label-success ml5">'.$lang['step_2_success'].'</span></td>
								</tr>';								
								$success = 1;
							}
							else
							{
								echo '<tr>								
									<td><span class="pd-l-sm"></span>'.$lang['step_2_create'].'</td>
									<td><b>' . str_replace('[prefix]', $prefix, $name[0]) . '</b></td>
									<td class="text-right"><span class="label label-danger ml5">'.$lang['install_error'].'</span></td>
								</tr>';
								$error = 1;
							}
						}
					}
					echo '  </tbody>
                                    </table>';
					if (!empty($error))
					{
					echo '<br /><div align="center"> <a href="install.php?step=1" class="btn btn-danger">'.$lang['install_back'].'</a></div><br />';
					}
					else
					{
					echo '<br /><div align="center"> <a href="install.php?step=chmod" class="btn btn-success">'.$lang['install_next'].'</a></div><br />';
					
					}					
					$sql_insert = file_get_contents('install/sql/sql_insert.sql');
					$sql_insert_massiv = split(";", $sql_insert);					
					foreach($sql_insert_massiv as $query)
					{
						if(preg_match('#INSERT#i', $query)) 
						{
							@mysql_query(str_replace('[prefix]', $prefix, $query), $resource);
						}
					}					
					if(isset($_POST['test_content']) && $_POST['test_content'] == 1)
					{
						$sql_content = file_get_contents('install/sql/sql_content.sql');
						$sql_content_massiv = split(";", $sql_content);
						
						foreach($sql_content_massiv as $query)
						{
							if(preg_match('#INSERT#i', $query)) 
							{
								@mysql_query(str_replace('[prefix]', $prefix, $query), $resource);
							}
						}
					}					
					$all_count = count($sql_insert_massiv)+count($sql_create_massiv);
				}				
				$content .= '$dbhost = \'' . $dbhost . '\';' . "\n";
				$content .= '$dbuser = \'' . $dbuser . '\';' . "\n";
				$content .= '$dbpass = \'' . $dbpass . '\';' . "\n";
				$content .= '$dbname = \'' . $dbname . '\';' . "\n". "\n";
				$content .= '$prefix = \'' . (mb_substr($prefix, -1) == '_' ? mb_substr($prefix, 0, -1) : $prefix) . '\';' . "\n";
				$content .= '$user_prefix = \'' . (mb_substr($prefix, -1) == '_' ? mb_substr($prefix, 0, -1) : $prefix) . '\';' . "\n";
				$content .= '$user_db = \'' . $dbname . '\';' . "\n";
				save_conf('etc/db.config.php', $content);
			}
			else
			{
				echo '<form action="install.php?step=2" method="post">
							<div class="alert alert-success alert-dismissable">
								<strong>'.$lang['install_information'].'</strong><br />'.$lang['step_2_ok'].'
                            </div>
							<input type="hidden" name="dbhost" value="' . $dbhost . '" />
							<input type="hidden" name="dbuser" value="' . $dbuser . '" />
							<input type="hidden" name="dbpass" value="' . $dbpass . '" />
							<input type="hidden" name="prefix" value="' . $prefix . '" />
							<input type="hidden" name="dbname" value="' . $dbname . '" />
							<input type="hidden" name="goCreate" value="1" />
							<input type="hidden" name="do" value="install" />
							<input type="hidden" name="test_content" value="0" />	
							<br />
							<div align="center"><input type="submit" value="'.$lang['install_next'].'" class="btn btn-success" /></div>
						</form>';
			}
		}
	} 
	else 
	{
		echo '<div class="alert alert-warning alert-dismissable">
				<strong>'.$lang['install_attention'].'</strong><br />'.$lang['step_2_error_1'].'<br />
             </div>
			<br /><div align="center"> <a href="install.php?step=1" class="btn btn-danger">'.$lang['install_back'].'</a></div>';
		$stop = 1;
    }
	foot();
}

function checkChmod()
{
global $information, $title, $lang;
	$dirs = array(
	'./tmp/',
	'./tmp/archives/',
	'./tmp/cache/',
	'./tmp/mysql/',
	'./files/avatars/',
	'./files/board/',	
	'./files/news/',
	'./files/thumb/',
	'./files/user/',
	'./files/avatars/users/',
	'./files/',
	'./etc/',
	'./usr/tpl/',
	'./usr/modules/',
	'./usr/blocks/',
	'./usr/plugins/');
	$title = $lang['chmod_title'].' | ';
	head();
	echo $lang['chmod_desc'].'<br /><br />
		<table class="table no-margin">
            <thead>
                <tr>
                    <th><span class="pd-l-sm"></span>'.$lang['chmod_folder'].'</th>
                    <th>'.$lang['chmod_rights'].'</th>
                    <th class="text-right">'.$lang['install_status'].'</th>
                </tr>
            </thead>
            <tbody>';
	foreach($dirs as $dir)
	{
		@chmod($dir, 0777);
		$chm = @decoct(@fileperms($dir)) % 1000;
		if(is_writable($dir))
		{
			$status = '<span class="label label-success ml5">'.$lang['chmod_allowed'].'</span>';
		}
		else
		{
			$status = '<span class="label label-danger ml5">'.$lang['chmod_forbidden'].'</span>';
		}
		echo '<tr>
				<td><span class="pd-l-sm"></span>'.$dir.'</td>						
				<td> [' . $chm . ']</td>
				<td class="text-right">'.$status. '</td>	
			  </tr>';
	}	
	foreach(scandir('./etc/') as $file)
	{
		if(preg_match('#.config.php#i', $file))
		{
			$file = './etc/'.$file;
			@chmod($file, 0666);
			$chm = @decoct(@fileperms($file)) % 1000;
			if(is_writable($dir))
			{
				$status = '<span class="label label-success ml5">'.$lang['chmod_allowed'].'</span>';
			}
			else
			{
				$status = '<span class="label label-danger ml5">'.$lang['chmod_forbidden'].'</span>';
			}
			echo '<tr>
					<td><span class="pd-l-sm"></span>'.$dir.'</td>						
					<td> [' . $chm . ']</td>
					<td class="text-right">'.$status. '</td>	
				  </tr>';
		}
	}
	echo '</tbody></table>
	<br /><br /><div align="center"> <a href="install.php?step=3" class="btn btn-success">'.$lang['chmod_next'].'</a></div><br />';
	foot();
}


function step3() 
{
global $information, $title, $lang;
	$title = $lang['step_3_title'].' | ';
	head();
	require_once ROOT . 'etc/global.config.php';
	if ($_SERVER['HTTPS'])
	{
		$url_site = 'https://'.$_SERVER['HTTP_HOST'];
	}
	else
	{
		$url_site = 'http://'.$_SERVER['HTTP_HOST'];
	}
	echo $lang['step_3_desc'].'<br /><br /><form action="install.php?step=4" method="post">
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_url'].':</label>
				<div class="col-sm-10">
					<input type="text" id="url" class="form-control" name="url" value="'.$url_site.'">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_name'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="name"  data-parsley-required="true" data-parsley-trigger="change" value="JMY CMS" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_charset'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="charset" data-parsley-required="true" data-parsley-trigger="change" value="' . $config['charset'] . '"  data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_gzip'].'</label>
				<div class="col-sm-10">
					'.radio("gzip", 1).'	
				</div>
			</div>	
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_rewrite'].'</label>
				<div class="col-sm-10">
				'.radio("mod_rewrite", 1).'
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_admin_email'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="email" data-parsley-required="true" data-parsley-trigger="change" value="" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>	
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_admin_login'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="nick" data-parsley-required="true" data-parsley-trigger="change" value="admin" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">'.$lang['step_3_admin_pass'].':</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="password" data-parsley-required="true" data-parsley-trigger="change" data-parsley-id="5887">	
					<ul class="parsley-errors-list" id="parsley-id-5887"></ul>
				</div>
			</div>
			<br />
			<div align="center"><input type="submit" value="'.$lang['install_next'].'" class="btn btn-success" /></div>	
		</form>';
	foot();
}


function genmycode($lenght)
{
    $symbols = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z','2','3','4','5','6','7','8','9');
    for($i=0;$i<$lenght;$i++)
    {
        $code[] = $symbols[rand(0,sizeof($symbols)-1)];
    }
	$code = array_unique($code);	
    return implode('', $code);
}

function step4() 
{
global $information, $title, $lang;
	$title = $lang['step_4_title'].' | ';
	head();
	if(!empty($_POST['name']) && !empty($_POST['charset']) && !empty($_POST['gzip']) && !empty($_POST['nick']) && !empty($_POST['password']))
	{
		require_once ROOT . 'etc/global.config.php';
		$content = "\$config = array();\n";
		foreach($config as $k => $val) 
		{
			if($k !== 'name' && $k !== 'charset' && $k !== 'gzip' && $k !== 'url' && $k !== 'uniqKey' && $k !== 'mod_rewrite') 
			{
				if(!is_array($val)) 
				{
					$content .= "\$config['".$k."'] = \"".$val."\";\n";
				} 
				else 
				{
					foreach($val as $karr => $varr) 
					{
						$content .= "\$config['".$k."']['".$karr."'] = \"".$varr."\";\n";
					}
				}
			}
		}
		$content .= "\$config['name'] = \"".$_POST['name']."\";\n";
		$content .= "\$config['charset'] = \"".$_POST['charset']."\";\n";
		$content .= "\$config['gzip'] = \"".$_POST['gzip']."\";\n";
		$content .= "\$config['mod_rewrite'] = \"".$_POST['mod_rewrite']."\";\n";
		$content .= "\$config['url'] = \"".$_POST['url']."\";\n";
		$content .= "\$config['uniqKey'] = \"" . genmycode(10) . "\";\n";
		$content .= "\$config['support_mail'] = \"" . $_POST['email'] . "\";\n";		
		save_conf('etc/global.config.php', $content);		
		require_once ROOT . 'etc/db.config.php';		
		$resource = mysql_pconnect($dbhost, $dbuser, $dbpass);	
	    if ($resource) 
		{
	        if (mysql_select_db($dbname)) 
			{
				@mysql_query('SET NAMES utf8');
				$tail = gencode(10);
				list($news) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM " . $prefix . "_news", $resource));
				@mysql_query("INSERT INTO " . $prefix . "_users (`nick` , `password` , `tail` , `email` ,  `group` , `user_news` , `active` ) VALUES ('" . $_POST['nick'] . "', '" . md5(mb_substr(md5(md5($_POST['password'])), 0, -mb_strlen($tail)) . $tail) . "', '" . $tail . "', '" . $_POST['email'] . "', '1', '" . $news . "', '1');", $resource);
				list($uid) = mysql_fetch_array(mysql_query("SELECT id FROM " . $prefix . "_users WHERE nick='" . $_POST['nick'] . "' LIMIT 1", $resource));
				@mysql_query("INSERT INTO `" . $prefix . "_board_users` (`uid`) VALUES ('" . $uid . "');", $resource);
	        }
		}
		echo '<div class="alert alert-success alert-dismissable">
					<strong>'.$lang['step_4_ok'].'</strong>
					<br />'.$lang['step_4_login'].': <strong>' . $_POST['nick'] . '</strong>
					<br />'.$lang['step_4_pass'].': <strong>' . $_POST['password'].'</strong>
                </div>
				<br /><div align="center"> <a href="install.php?step=5" class="btn btn-success">'.$lang['install_next'].'</a></div>';	
	}
	else
	{
		echo '<div class="alert alert-warning alert-dismissable">
				<strong>'.$lang['install_attention'].'</strong><br />'.$lang['step_4_error'].'
			</div>
			<br /><div align="center"> <a href="install.php?step=3" class="btn btn-danger">'.$lang['install_back'].'</a></div>';
	
	}
	foot();
}

function step5() 
{
global $information, $title, $lang;
	$title = $lang['step_5_title'].' | ';
	head();
	echo $lang['step_5_desc'].'<br /><br /><div class="alert alert-warning alert-dismissable">
				<strong>'.$lang['install_attention'].'</strong><br />'.$lang['step_5_install'].'
			</div>
			<div class="btn-group">
                  <button type="button" class="btn btn-success">'.$lang['step_5_site'].'</button>
                  <button type="button" data-toggle="dropdown" class="btn btn-success dropdown-toggle"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                  <ul role="menu" class="dropdown-menu">
                    <li><a href="administration/">'.$lang['step_5_panel'].'</a></li>
                    <li><a href="https://jmy.su">'.$lang['step_5_jmy'].'</a></li>                   
                  </ul>
                </div>';
			delcache('plugins');		
			@fopen(ROOT . 'install/lock.install', 'w');	
			foot();
}
if(!file_exists('install/lock.install'))
{
	switch(isset($_GET['step']) ? ($_GET['step']) : null) 
	{
		default:
			language();
			break;
			
		case 'language_set_en':
			language_set('en');
		break;
		
		case 'language_set_ru':
			language_set('ru');
		break;
		
		case '0':
			license();
		break;
			
		case '1':
			step1();
			break;
		
		case "2":
			step2();
			break;
		
		case "3":
			step3();
			break;
			
		case "chmod":
			checkChmod();
			break;		

		case "4":
			step4();
			break;		

		case "5":
			step5();
			break;
	}
}
else
{
	Header('Location: /');
}
