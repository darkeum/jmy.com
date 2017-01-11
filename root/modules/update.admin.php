<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

global $adminTpl, $config, $core, $configs, $clear, $lang;

$flag = true;
$now = file_get_contents('https://server.jmy.su/index.php?check_version');
$next = file_get_contents('https://server.jmy.su/sequence.php?'.VERSION_ID);

$allfiles = array();  

function makebackup() 
  {
		global $allfiles;
		$makebackup=false;
		$mainFile = ROOT.'etc/db.config.php';
		include($mainFile);
		set_time_limit(0); 		 
		$db_names = array();
		$db_names[] = $dbname;
		$host = $dbhost;
		$user = $dbuser;
		$password = $dbpass;
		$source_dirs = array();
		$source_dirs[] = $_SERVER['DOCUMENT_ROOT'];	
		$url_var = explode('/' , $_SERVER['DOCUMENT_ROOT']);
		$last_folder = end($url_var);
		$offset_dirs = strlen($last_folder);     
		$dump_dir = ROOT."tmp/backup/"; 
		$delay_delete = 35 * 24 * 3600; 
		$filezip = "backup_".date("Y-m-d").".zip";  
		$db_files = array();   
		for ($i = 0; $i < count($db_names); $i++)
			{
				$filename = $db_names[$i].".sql"; 
				$db_files[] = $dump_dir."/".$filename; 
				$fp = fopen($dump_dir."/".$filename, "a"); 
				$db = new mysqli($host, $user, $password, $db_names[$i]);
				$db->query("SET NAMES 'utf-8'"); 
				$result_set = $db->query("SHOW TABLES"); 
				while (($table = $result_set->fetch_assoc()) != false) 
				{      
					$table = array_values($table);
					if ($fp) 
					  {
						$result_set_table = $db->query("SHOW CREATE TABLE `".$table[0]."`"); 
						$query = $result_set_table->fetch_assoc();
						$query = array_values($query);
						fwrite($fp, "\n".$query[1].";\n");
						$rows = "SELECT * FROM `".$table[0]."`";
						$result_set_rows = $db->query($rows);
						while (($row = $result_set_rows->fetch_assoc()) != false)
							{
								$query = "";						  
								foreach ($row as $field) 
								{
									if (is_null($field)) $field = "NULL";
									else $field = "'".$db->real_escape_string($field)."'"; 
									if ($query == "") $query = $field;
									else $query .= ", ".$field;
								}
								$query = "INSERT INTO `".$table[0]."` VALUES (".$query.");";
								fwrite($fp, $query);
							}
						}
				}
				fclose($fp); 
				$db->close(); 
			}  
			$zip = new ZipArchive(); 
			
			if ($zip->open($dump_dir."/".$filezip, ZipArchive::CREATE) === true) 
			{
				for ($i = 0; $i < count($source_dirs); $i++) 
				{				
					if (is_dir($source_dirs[$i])) recoursiveDir($source_dirs[$i]);
					else $allfiles[] = $source_dirs[$i]; 
					foreach ($allfiles as $val)
					{ 
						$local = substr($val, $offset_dirs);
						$zip->addFile($val, $local);
					}
				}   
				for ($i = 0; $i < count($db_files); $i++) 
				{					
				  $local = substr($db_files[$i], strlen($dump_dir) + 1);
				  $zip->addFile($db_files[$i], $local);
				}
				$zip->close();	
				return true;
			}
			for ($i = 0; $i < count($db_files); $i++) unlink($db_files[$i]); 
  }
  
  
function recoursiveDir($dir)
	{
		global $allfiles;
		if ($files = glob($dir."/{,.}*", GLOB_BRACE)) {
		  foreach($files as $file){
			$b_name = basename($file);
			if (($b_name == ".") || ($b_name == "..")) continue;
			if (is_dir($file)) recoursiveDir($file);
			else $allfiles[] = $file;
		  }
		}
	 }
	 
switch(isset($url[2]) ? $url[2] : null) {
	default:				
			if (VERSION_ID<>$now)
			{
				if ($next<>'NULL')
				{	
					$what_new = file_get_contents('https://server.jmy.su/history.php?'.$next);
					$adminTpl->admin_head($lang['updates']);		
					$adminTpl->open();	
					echo '<div style="max-width: 625px" class="center-block mt70">           
							<div class="row table-layout">
								<div class="col-xs-7 pln">
									<h2 class="text-dark mbn confirmation-header">
										<i class="fa fa-check text-success"></i>JMY CMS v'.$now.'
									</h2>
								</div>
								<div class="col-xs-5 text-right va-b">
									<div class="meta-links alt">
										<a target="_blank" href="https://jmy.su/">'.$lang['official'].'</a> 
										<span class="ph5">|</span> 
										<a href="'.$core->fullURL().'#" class="active" onclick="modal_o(\'#modal_wn\')">'.$lang['updates_whatnew'].'</a>
									</div>
								</div>
							</div>          
							<div class="panel mt15">
								<div class="panel-body pt30 p25 pb15">
									<p class="lead">'.$lang['updates_avalible'].'</p>
									<hr class="alt short mv25">'.$lang['updates_avalible_text'].'</p>
									<p class="text-right mt20">
										<button onclick="location.href=\''.ADMIN.'/update/process_backup\';"  type="button" class="btn btn-primary btn-rounded ph40">'.$lang['updates_start'].'</button>
									</p>
								</div>
							</div>
						</div>
						<div id="modal_wn" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
								<div class="panel-heading">
									<span class="panel-icon"><i class="fa fa-check-square-o"></i></span>
									<span class="panel-title">'.$lang['updates_whatnew'].'</span>
								</div>
								<div class="panel-body">
									<h3 class="mt5">JMY CMS v'.$next.'</h3>							
									<hr class="short alt">
									<p>'.$what_new.'</p>
								</div>						 
							</div>
					</div>';
					$adminTpl->close();
					$adminTpl->admin_foot();
				}
				else
				{				
					$adminTpl->admin_head($lang['updates']);
					echo '<div id="content" class="animated fadeIn">';
					$adminTpl->info($lang['updates_error_0'], 'error', null, $lang['error'], $lang['support'], ADMIN.'/support');
					echo '</div>';
					$adminTpl->admin_foot();
					
				}
			}
			else
			{
					$what_new = file_get_contents('https://server.jmy.su/history.php?'.VERSION_ID);
					$adminTpl->admin_head($lang['updates']);		
					$adminTpl->open();	
					echo '<div style="max-width: 625px" class="center-block mt70">           
							<div class="row table-layout">
								<div class="col-xs-7 pln">
									<h2 class="text-dark mbn confirmation-header">
										<i class="fa fa-check text-success"></i>JMY CMS v'.VERSION_ID.'
									</h2>
								</div>
								<div class="col-xs-5 text-right va-b">
									<div class="meta-links alt">
										<a target="_blank" href="https://jmy.su/">'.$lang['official'].'</a> 
										<span class="ph5">|</span> 
										<a href="'.$core->fullURL().'#" class="active" onclick="modal_o(\'#modal_wn\')">'.$lang['updates_whatnew'].'</a>
									</div>
								</div>
							</div>          
							<div class="panel mt15">
								<div class="panel-body pt30 p25 pb15">
									<p class="lead">'.$lang['updates_no'].'</p>
									<hr class="alt short mv25">'.$lang['updates_no_text'].'</p>	
									<p class="text-right mt20">
										<button onclick="location.href=\''.ADMIN.'/update/refresh\';"  type="button" class="btn btn-primary btn-rounded ph40">'.$lang['updates_refresh'].'</button>
									</p>
								</div>
							</div>
						</div>
						<div id="modal_wn" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
								<div class="panel-heading">
									<span class="panel-icon"><i class="fa fa-check-square-o"></i></span>
									<span class="panel-title">'.$lang['updates_whatnew'].'</span>
								</div>
								<div class="panel-body">
									<h3 class="mt5">JMY CMS v'.VERSION_ID.'</h3>							
									<hr class="short alt">
									<p>'.$what_new.'</p>
								</div>						 
							</div>
					</div>';
					$adminTpl->close();
					$adminTpl->admin_foot();
			}		
		break;	
	
	case "process_backup":
		if (VERSION_ID<>$now)
			{
				if (true)
				{
					$adminTpl->admin_head($lang['updates']);
					echo '<div id="content" class="animated fadeIn">';
					$adminTpl->info($lang['updates_backup'], 'info', null, $lang['info']);
					echo '</div>					
							<script type="text/javascript">
								var i = 5;
								function time(){
									if (i >= 0) document.getElementById("time").innerHTML = i;
									i--;
									if (i == -1) location.href = "/'.ADMIN.'/update/process_run";
								}
								time();
								setInterval(time, 1000);
							</script>';						
					$adminTpl->admin_foot();					
				}
				else
				{
					$adminTpl->admin_head($lang['updates']);
					echo '<div id="content" class="animated fadeIn">';
					$adminTpl->info($lang['updates_error_1'], 'error', null, $lang['error'], $lang['updates_again'], ADMIN.'/update');
					echo '</div>';					
					$adminTpl->admin_foot();
				}
			}
			else
			{
				 header('Location: /'.ADMIN.'/update');
			}		
		break;
	
	case "process_run":	
		if (VERSION_ID<>$now)
		{
			process_run(VERSION_ID);
			if ($flag)
			{
				$adminTpl->admin_head($lang['updates']);					
				echo '<div id="content" class="animated fadeIn">';
				$adminTpl->info($lang['updates_compl'], 'info', null, $lang['info']);
				echo '</div>					
					<script type="text/javascript">
						var i = 5;
						function time(){
							if (i >= 0) document.getElementById("time").innerHTML = i;
								i--;
							if (i == -1) location.href = "/'.ADMIN.'/update";
						}
						time();
						setInterval(time, 1000);
					</script>';						
				$adminTpl->admin_foot();
			}
			else
			{
				$adminTpl->admin_head($lang['updates']);
				echo '<div id="content" class="animated fadeIn">';
				$adminTpl->info($lang['updates_error_0'], 'error', null, $lang['error'], $lang['support'], ADMIN.'/support');
				echo '</div>';
				$adminTpl->admin_foot();
			}
		}
		else
		{
			header('Location: /'.ADMIN.'/update');
		}	
		
		
		break;

		case "refresh":	
			unlink (ROOT.'tmp/update/time.dat');
			header('Location: /'.ADMIN.'/update');
		break;
}

function process_run($version_id)
{	
	global $flag;
	$now = file_get_contents('https://server.jmy.su/index.php?check_version');
	if ($version_id<>$now)
	{
		set_time_limit(0); 	
		$next = file_get_contents('https://server.jmy.su/sequence.php?'.$version_id);
		$next_sl = str_replace('.','_',$next);		
		$next_url = file_get_contents('https://server.jmy.su/download_update.php?'.$next_sl);
		$curl = curl_init($next_url);
		$fp =fopen('update_'.$next_sl.'.zip','w');
		curl_setopt($curl, CURLOPT_FILE, $fp);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_exec($curl);
		curl_close($curl);
		fflush($fp);
		fclose($fp);				
		$zip = new ZipArchive;
		$file = realpath(ROOT.'update_'.$next_sl.'.zip');
		$res = $zip->open($file);
		if ($res === TRUE) 
		{
			$zip->extractTo(ROOT);
			$zip->close();
			if (file_exists(ROOT.'update.php'))
				{
					include(ROOT.'update.php');
					unlink (ROOT.'update.php');
				}				
			unlink (ROOT.'update_'.$next_sl.'.zip');
			unlink (ROOT.'tmp/update/lock.update');
			unlink (ROOT.'tmp/update/time.dat');
			if ($next <> $now)
				{
					process_run($next);
				}
				else
				{
					return true;
				}
		}
		else 
			{
				$flag = false;
			}	
	}
	else
	{
		return true;
	}
}