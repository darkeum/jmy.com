<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}


$now = file_get_contents('http://server.jmy.su/index.php?check_version');
$next = file_get_contents('http://server.jmy.su/sequence.php?'.VERSION_ID);

			$allfiles = array(); 
function main() {
	global $adminTpl, $config, $core, $configs, $clear;
}


  
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
				if ($next<>'NU7LL')
				{
					$adminTpl->admin_head(_UPDATE_MAIN);		
					$adminTpl->open();	
					echo '<div style="max-width: 625px" class="center-block mt70">           
            <div class="row table-layout">
              <div class="col-xs-7 pln">
                <h2 class="text-dark mbn confirmation-header"><i class="fa fa-check text-success"></i>JMY CMS v'.$next.'</h2>
              </div>
              <div class="col-xs-5 text-right va-b">
                <div class="meta-links alt"><a href="#">'._UPDATE_URL.'</a> <span class="ph5">|</span> <a href="#" class="active">'._UPDATE_LOG.'</a>
                </div>
              </div>
            </div>
            <!-- Confirmation Panel-->
            <div class="panel mt15">
              <div class="panel-body pt30 p25 pb15">
                <p class="lead">Hello Justin,</p>
                <hr class="alt short mv25">
                <p class="lh25 text-muted fs15">Thank you for registering with blank. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin ac nisi massa. Sed gravida arcu et eros euismod aliquam. Cras at fermentum velit. Praesent ligula nibh, venenatis non vulputate sit amet, bibendum non risus.</p>
                <p class="text-right mt20">
                  <button onclick="location.href=\''.ADMIN.'/update/process_backup\';"  type="button" class="btn btn-primary btn-rounded ph40">'._UPDATE_START.'</button>
                </p>
              </div>
            </div>
            <!-- Registration Links-->
           
          </div>';



					
					echo '<div class="subscription col-md-6 col-md-offset-3 bg-white">
								<center>
									<i class="fa fa-arrow-circle-o-up fa-2x"></i>
									<h5>'._UPDATE_NEW.'</h5>
									<p>'._UPDATE_DESC.'</p>
									<button onclick="location.href=\''.ADMIN.'/update/process_backup\';" class="btn btn-success btn-sm mg-t-md" data-toggle="modal" data-target=".bs-modal">'._UPDATE_START.'</button>
								</center>
						  </div>';
					$adminTpl->close();
					$adminTpl->admin_foot();
				}
				else
				{
					$adminTpl->admin_head(_UPDATE_MAIN);
					$adminTpl->info(_UPDATE_ERROR_0, 'error');
					$adminTpl->admin_foot();
				}
			}
			else
			{
				$adminTpl->admin_head(_UPDATE_MAIN);
				$adminTpl->info(_UPDATE_NO);
				$adminTpl->admin_foot();
			}
		
		break;	
	
	case "process_backup":
		if (VERSION_ID<>$now)
			{
				if (makebackup())
				{
					$adminTpl->admin_head(_UPDATE_MAIN);
					$adminTpl->info(_UPDATE_BACKUP_OK.'<br><br><button onclick="location.href=\''.ADMIN.'/update/process_run\';" type="button" class="btn btn-success">'._UPDATE_B_NEXT.'</button>');
					$adminTpl->admin_foot();
				}
				else
				{
					$adminTpl->admin_head(_UPDATE_MAIN);
					$adminTpl->info(_UPDATE_BACKUP_NO.'<br><br><button onclick="location.href=\''.ADMIN.'/update\';" type="button" class="btn btn-primary">'._UPDATE_B_AGAIN.'</button> <button onclick="location.href=\''.ADMIN.'/update/process_run\';" type="button" class="btn btn-danger">'._UPDATE_B_NEXT.'</button>');
					$adminTpl->admin_foot();
				}
			}
			else
			{
				$adminTpl->admin_head(_UPDATE_MAIN);
				$adminTpl->info(_UPDATE_NO);
				$adminTpl->admin_foot();
			}		
		break;
	
	case "process_run":
	/*
		if (VERSION_ID<>$now)
			{
				set_time_limit(0); 	
				$next_sl = str_replace('.','_',$next);		
				$next_url = file_get_contents('http://server.jmy.su/download_update.php?'.$next_sl);
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
				if ($res === TRUE) {
					$zip->extractTo(ROOT);
					$zip->close();
					 if (file_exists(ROOT.'update.php'))
						{
							include(ROOT.'update.php');
							unlink (ROOT.'update.php');
						}				
					unlink (ROOT.'update_'.$next_sl.'.zip');
					unlink (ROOT.'tmp/backup/lock.update');
					unlink (ROOT.'tmp/backup/time.dat');
					$adminTpl->admin_head(_UPDATE_MAIN);
					$adminTpl->info(_UPDATE_OK);
					$adminTpl->admin_foot();
				}
				else 
				{
					$adminTpl->admin_head(_UPDATE_MAIN);
					$adminTpl->info(_UPDATE_ERROR_0, 'error');
					$adminTpl->admin_foot();
				}
			}
			else
			{
				$adminTpl->admin_head(_UPDATE_MAIN);
				$adminTpl->info(_UPDATE_NO);
				$adminTpl->admin_foot();
			}	*/
		break;
}