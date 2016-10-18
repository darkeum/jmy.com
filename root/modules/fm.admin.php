<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2015 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}

function main()
	{
		global $adminTpl, $config, $core, $configs, $clear, $fm_conf;
	}

function remdir($DIR)
	{
		$handle = opendir ($DIR);
		while($file = readdir ($handle)) 
			{
				if ($file == "." or $file == "..")
					{
						continue;
                    }
				if (filetype("$DIR/$file") == "file")
					{
						if (is_writable("$DIR/$file") && is_writable($DIR)) 
							{
								unlink("$DIR/$file");
							}
					}
				else
					{
						remdir("$DIR/$file");
                    }
			}            
		closedir($handle);
		rmdir($DIR);
	}
	
function size_dir($DIR,$size)
	{
		$handle = opendir ($DIR);
		while ($file = readdir ($handle))
			{
				if ($file == "." or $file == "..")
					{
                        continue;
                    }    
				if (filetype("$DIR/$file") == "file")
					{
						$size = $size + filesize("$DIR/$file");
					}
				else 
					{
                        $size = size_dir("$DIR/$file",$size);  
                    }
            }
		closedir($handle);
		return $size;
	}
	
function upload()
	{
		global $adminTpl, $config, $core, $configs, $clear, $fm_conf;
		if (isset($_POST['DIR']))
			{
				$dir=$_POST['DIR'];
				echo $dir;
			}	
		if (isset($_FILES["FILE"]["tmp_name"]))
			{
				$file=$_FILES["FILE"]["tmp_name"];
				echo $file;
			}	
		if (empty($_POST['DIR'])) 
			{
				$DIR=$fm_conf['path'];
			}
		else
			{
				$DIR=$_POST['DIR'];
			}
        if (isset($dir) && empty($dir) || isset($file) && empty($file)) {
			$adminTpl->admin_head('Файловый менеджер');		
			$adminTpl->info('Ошибка: Все поля нужно заполнить', 'error');
			$adminTpl->admin_foot();
            exit;
        }
       if (isset($dir) && !empty($dir) && !is_dir($dir))  {
			$adminTpl->admin_head('Файловый менеджер');		
			$adminTpl->info('Ошибка: Директория не существует', 'error');
			$adminTpl->admin_foot();
            exit;
        }
       if (isset($dir) && !empty($dir) && !is_writable($dir))
		{
			$adminTpl->admin_head('Файловый менеджер');		
			$adminTpl->info('Ошибка: Отсутствуют права доступа к директории', 'error');
			$adminTpl->admin_foot();
            exit;
        }
        if (isset($dir) && !empty($dir) && isset($file) && !empty($file)) {
              $file_name = $_FILES["FILE"]["name"];
              $dir=trim($dir);
				if (@move_uploaded_file($file, "$dir/$file_name")) 
				{
                    chmod("$dir/$file_name",0755);
                    

					$adminTpl->admin_head('Файловый менеджер');		
					$adminTpl->info('Файл: '.$file_name.' успешно загружен.');
					$adminTpl->admin_foot();
                    exit;

				}
				else 
				{		
					$adminTpl->admin_head('Файловый менеджер');	
					$adminTpl->info('Ошибка: невозможно загрузить файл:'. $file_name , 'error');
					$adminTpl->admin_foot();
                    exit;
				}
     }
	 echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>Загрузка файлов</b></div><div class="panel-body"><div class="switcher-content">	 
	 <form action="{ADMIN}/fm/upload" method="post" name="news" role="form" class="form-horizontal parsley-form" enctype="multipart/form-data" data-parsley-validate="" novalidate=""><div class="form-group">
					<label class="col-sm-3 control-label">Директория загрузки:</label>
					<div class="col-sm-4">
						<input type="text" name="DIR" value="'.$DIR.'"  class="form-control" id="altname"  data-parsley-required="true" data-parsley-trigger="change" \">

					</div>
		</div>	
		<div class="form-group">
			<label class="col-sm-3 control-label">Файл:</label>
			<div class="col-sm-4">
				<input type="file" name="FILE" size="40">
			</div>
		</div>
		<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="Загрузить">						
					</div>
		</div></form></div></div></section></div></div>
	 
	 
	 ';
     
   }    
   function ok($text) {
	   global $adminTpl, $config, $core, $configs, $clear, $fm_conf;
					$adminTpl->admin_head('Файловый менеджер');		
					$adminTpl->info($text);
					$adminTpl->admin_foot();
	   
   }
    function mfile() {
        global $fm_conf;
		if (isset($_POST['DIR']))
			{
				$DIR=$_POST['DIR'];
			}	
		if (isset($_POST['dir']))
			{
				$dir=$_POST['dir'];
			}	
		if (isset($_POST['ftext']))
			{
				$ftext=$_POST['ftext'];
			}    
		if (isset($_POST['file_name']))
			{
				$file_name=$_POST['file_name'];
			}   
        
        if (empty($DIR)) {
            $DIR=$fm_conf['path'];
        }
        if (isset ($dir) && $dir=='' || isset ($file_name) && $file_name=='' ) {
            ok('Ошибка: нужно заполнить поля: "Директория" и "Название файла"');
            exit;
        }
        if (isset ($file_name) && preg_match("#[\/\:\*\?\"<>\|]#", $file_name)) {
            ok('Ошибка: имя не должно содержать / \ : * ? \" <> | ');
            exit;
        }
        if (isset ($dir) && !is_dir($dir))  {
            ok('Ошибка: Директория не существует');
            exit;
        }
        if (isset ($dir) && !is_writable($dir)){
            ok('Ошибка: Отсутствуют права доступа к директории');
            exit;
        }
        if (isset ($dir) && isset ($file_name) && file_exists("$dir/$file_name") && $dir!=='' && $file_name!=='') {
            ok('Ошибка: Такой файл уже существует');
            exit;
        }
        if (isset ($dir) && $dir!=='' || isset ($file_name) && $file_name!=='' ) {
        $asp=preg_replace("#(.*)\.#","",$file_name);
        $asp=strtolower($asp);
       
           $op = fopen ("$dir/$file_name", "w");
					fputs ($op,$ftext);
					fclose ($op);
               ok("Файл: $dir/$file_name успешно создан");
               exit;
        }
		 echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>Создание файла</b></div><div class="panel-body"><div class="switcher-content">	 
	 <form action="{ADMIN}/fm/create" method="post" name="news" role="form" class="form-horizontal parsley-form" enctype="multipart/form-data" data-parsley-validate="" novalidate="">
	 <div class="form-group">
					<label class="col-sm-3 control-label">Директория загрузки:</label>
					<div class="col-sm-4">
						<input type="text" name="dir" value="'.$DIR.'"  class="form-control" id="altname"  data-parsley-required="true" data-parsley-trigger="change" \">

					</div>
		</div>	
		 <div class="form-group">
					<label class="col-sm-3 control-label">Имя файла:</label>
					<div class="col-sm-4">
						<input type="text" name="file_name"  class="form-control" id="altname"  data-parsley-required="true" data-parsley-trigger="change" \">

					</div>
		</div>	
		<div class="form-group">
			<label class="col-sm-3 control-label">Содержание файла:</label>
			<div class="col-sm-4">
				<textarea cols=50 rows=10 name=ftext ></textarea>
			</div>
		</div>
		<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="Создать">						
					</div>
		</div></form></div></div></section></div></div>
	 
	 
	 ';
       
    }
 function rall($r) {
      global $fm_conf,$do;
            
           if (!file_exists($r)){
               ok("Ошибка: Файл: $r не найден");
                exit;
           }
           if (!is_writable($r)) {
               ok("Ошибка: Отсутствуют права доступа к директории");
                exit;
           }
           $asp=preg_replace("#(.*)\.#","",$r);
           $asp=strtolower($asp);

if (isset($_POST['ftext']))
			{
				$ftext=$_POST['ftext'];
			}    
              if (isset($ftext)) {
                     $ftext=stripslashes($ftext);
                     $fp=fopen($r,"w");
                         fputs($fp,$ftext);
                         fclose($fp);
                         ok("Файл успешно изменён");
                         exit;
                   }
           $fp=fopen($r,"r");
           $frtext = fread($fp, filesize($r));
                     fclose($fp);
           $frtext=htmlspecialchars($frtext);
           $frtext=stripslashes($frtext);
		   echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>Редактор</b></div><div class="panel-body"><div class="switcher-content">	 
	 <form method="post" name="news" role="form" class="form-horizontal parsley-form"  data-parsley-validate="" novalidate="">
	
		<input type="hidden" name="action" value="edit">
														<input type="hidden" name="DIR" value="'.$r.'">
	
		<div class="form-group">
			<label class="col-sm-3 control-label">Редактирование файла:</label>
			<div class="col-sm-4">
				<textarea cols=50 rows=10 name=ftext >'.$frtext.'</textarea>
			</div>
		</div>
		<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-4">
						<input name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="Принять">						
					</div>
		</div></form></div></div></section></div></div>
	 
	 
	 ';           

   }
   function rname($rname) 
		{
			global $adminTpl, $config, $core, $configs, $fm_conf, $do;
			if (!file_exists($rname) && !is_dir($rname)) 
				{
					$adminTpl->admin_head('Файловый менеджер');		
					$adminTpl->info('Ошибка: Файл/директория: '.$rname.' не найден!', 'error');
					$adminTpl->admin_foot();					
					exit;
				}
				$dir=preg_replace("#\/([^\/]+)$#","",$rname);
				if (isset($_POST['nname']))
					{
						$nname = $_POST['nname'];
					}				
				if (isset($nname) && !empty($nname)) 
					{
						if (!is_writable($rname) && !is_writable($dir)) 
						{
							$adminTpl->admin_head('Файловый менеджер');
							$adminTpl->info('Ошибка: Отсутствуют права доступа к файлу/директории: '.$rname, 'error');	
							$adminTpl->admin_foot();							
							exit;
						 }
						if (preg_match("#[\/\:\*\?\"<>\|]#", $nname))
							{
								$adminTpl->admin_head('Файловый менеджер');
								$adminTpl->info('Ошибка: имя не должно содержать / \ : * ? \" <> | ', 'error');	
								$adminTpl->admin_foot();
								exit;
							}
						rename($rname, "$dir/$nname");
						$adminTpl->admin_head('Файловый менеджер');
						$adminTpl->info('Имя файла/директории успешно изменено');
						$adminTpl->admin_foot();
						exit;
					}
					$name=preg_replace("#(.*)\/#","",$rname);
				echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">
                              <header class="panel-heading">Переименовать файл/директоию</header>
                                                <div class="panel-body">
													<form class="form-inline" method="POST">
														<input type="hidden" name="action" value="rename">
														<input type="hidden" name="DIR" value="'.$rname.'">
														<b>Введите новое имя файла/директории: </b><br><br>
														<div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Введите новое имя файла/директории: </label>
                                                            <input type="text" name="nname" class="form-control" size=40" value="'.$name.'">
                                                        </div>
														<button type="submit" class="btn btn-info">Переименовать</button>
                                                    </form>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';    
				
		}
 
 function makedir() {
     global $fm_conf,$do;
		if (isset($_POST['DIR']))
					{
						$mkdir = $_POST['DIR'];
					}	
		if (isset($_POST['nname']))
					{
						$name = $_POST['nname'];
					}				
   
       if (!is_dir($mkdir)) {
           ok("Ошибка: Директория $mkdir не найдена");
           exit;
       }
       if (!is_writable($mkdir)) {
           ok("Ошибка: Отсутствуют права доступа к директории");
           exit;
       }
         if (isset($name) && !empty($name)) {
         if (preg_match("#[\/\:\*\?\"<>\|]#", $name)) {
              ok('Ошибка: имя не должно содержать / \ : * ? \" <> | ');
              exit;
              }
         if (is_dir("$mkdir/$name")){
              ok('Ошибка: такая директория уже существует');
              exit;
            }
              mkdir("$mkdir/".trim($name)."");
              ok("Директория: $mkdir/$name успешно создана");
              exit;
         }
		echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">
                              <header class="panel-heading">Создание папки</header>
                                                <div class="panel-body">
													<form class="form-inline" method="POST">					
														<b>Введите имя директории:</b><br><br>
														<div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Введите имя директории: </label>
                                                            <input type="text" name="nname" class="form-control" size=40" value="">
															<input type="hidden" name="DIR" value="'.$mkdir.'">	
                                                        </div>
														<button type="submit" class="btn btn-info">Создать</button>
                                                    </form>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';        
 }
  function fcopy($fcopy,$sost=false) {
     global $fm_conf,$do;
	 if (isset($_POST['dir']))
					{
						$dir=$_POST['dir'];
					}	
       
       if (!file_exists($fcopy)) {
           ok("Ошибка: Файл $fcopy не найдена");
           exit;
       }
       if (!is_writable($fcopy)) {
           ok("Ошибка: Отсутствуют права доступа к директории");
           exit;
       }
         if (isset($dir) && !empty($dir)) {
			$file_name=preg_replace("#(.+)\/([^\/]+)$#","\\2",$fcopy);
				 if (file_exists("$dir/$file_name")){
					  ok('Ошибка: такаой файл уже существует');
					  exit;
					}
			
				 if (!is_dir("$dir")){
					  ok('Ошибка: директория копирования не существует');
					  exit;
					}
              copy($fcopy,"$dir/$file_name");
              if ($sost) 
			  {
					$td = 'change';
					unlink($fcopy);
					ok("Файл $file_name успешно перемещён");
               }
               else {
				    $td = 'copy';
					ok("Файл $file_name успешно копирован");
               }
              chmod("$dir/$file_name",0755);

              exit;
         }
		 $td ='';
		 if ($sost) {
				  $td = 'change';
               }
               else {
				    $td = 'copy';
               }
			    $file_name1=preg_replace("#(.+)\/([^\/]+)$#","\\2",$fcopy);
			   $fcopydir= str_replace($file_name1, '', $fcopy);
		 echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">
                              <header class="panel-heading">Переместить/копировать</header>
                                                <div class="panel-body">
													<form class="form-inline" method="POST">	

														<input type="hidden" name="action" value="'.$td.'">
														<input type="hidden" name="DIR" value="'.$fcopy.'">

													
														<b>Куда переместить/копировать:</b><br><br>
														<div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Куда переместить/копировать: </label>
                                                            <input type="text" name="dir" class="form-control" size=40" value="'.$fcopydir.'">
                                                        </div>
														<button type="submit" class="btn btn-info">Выполнить</button>
                                                    </form>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';   
 }
  function deldir($del) {
     global $fm_conf,$do;
       if (!file_exists($del) && !is_dir($del))  {
             ok("Ошибка: Файл/директория: $del не найден");
             exit;
        }
      if (!is_writable($del)) {
           ok("Ошибка: Отсутствуют права доступа к файлу/директории");
           exit;
       }
         if (filetype("$del") == "file"){
             unlink($del);
             ok("Удаление файла успешно завершено");
             exit;
      }
       else {
           remdir($del);
           ok("Удаление директории успешно завершено");
           exit;
       }
 }
 
  function sfile($DIR,$search){
	$handle = opendir ($DIR);
	while ($file = readdir ($handle)){
		if ($file == "." or $file == ".."){
            continue;
            }
		if (filetype("$DIR/$file") == "file"){
			if (preg_match("#\[([0-9]+)-([0-9]+)\]#", $search)){
				$fsize = filesize ("$DIR/$file");
				$frsize =preg_replace("#\[([0-9]+)-([0-9]+)\]#", "\\1", $search);
				$flsize =preg_replace("#\[([0-9]+)-([0-9]+)\]#", "\\2", $search);
                 $frsize=$frsize*1024;
                 $flsize=$flsize*1024;
				if ($fsize >= $frsize && $fsize <= $flsize){					
								echo '<tr>
										<td>											
											<form id="form_id_'.$file.$DIR.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$DIR.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.$DIR.'\').submit();return false;">'.$file.'</a>
											</form>
										</td>
										<td>'.$DIR.'</td>
									</tr>';					
				}
			}
			if (strpos("$file", $search)){
								echo '<tr>
										<td>											
											<form id="form_id_'.$file.$DIR.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$DIR.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.$DIR.'\').submit();return false;">'.$file.'</a>
											</form>
										</td>
										<td>'.$DIR.'</td>
																										
									</tr>';
              }
            else if (strstr("$file", $search)){
								echo '<tr>
										<td>											
											<form id="form_id_'.$file.$DIR.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$DIR.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.$DIR.'\').submit();return false;">'.$file.'</a>
											</form>
										</td>
										<td>'.$DIR.'</td>
																										
									</tr>';
              }
		}
		else{
           if (strstr("$file", $search)){
            echo '<tr>
										<td>											
											<form id="form_id_'.$file.$DIR.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$DIR.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.$DIR.'\').submit();return false;"><img src="media/filetypes/folder.png" ><span class="pd-l-sm"></span>'.$file.'</a>
											</form>
										</td>
										<td>'.$DIR.'</td>																									
									</tr>';
              }
        sfile("$DIR/$file", $search);
        }
	}
}
  function search() {
     global $fm_conf,$do;
	 $sfile="";
	 if (isset($_POST['sfile']))
					{
					$sfile=$_POST['sfile'];
					}	

echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">
                              <header class="panel-heading">Поиск</header>
                                                <div class="panel-body">
													<form class="form-inline" method="POST">					
														<b>Для поиска по диопазону размера используйте следующий синтаксис: [от-до] ,где от-начальный размер,до-конечный,в кб </b><br><br>
														<div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Введите новое имя файла/директории: </label>
                                                            <input type="text" name="sfile" class="form-control" size=40" value="'.$sfile.'">
                                                        </div>
														<button type="submit" class="btn btn-info">Искать</button>
                                                    </form>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';    
									
  
        if (isset($sfile) && !empty($sfile)) {
			
			echo '<div class="row">
							<div class="col-lg-12">
								<section class="panel">
									<div class="panel-heading">										
										Результат поиска								
									</div>
									<div class="panel-body no-padding">					
									<table class="table no-margin">
										<thead>
											<tr>												
												<th class="col-md-4"><span class="pd-l-sm"></span>Файл/Папка</th>
												<th class="col-md-8">Ссылка</th>
												
											</tr>
										</thead>
										<tbody>';    
          sfile($fm_conf['path'],$sfile);
		  	echo '</tbody></table></section></div></div>';
    }
 }
     
   function chemod($chmod)
		{
			global $adminTpl, $config, $core, $configs, $fm_conf, $do;
			if (isset($_POST['nchmod']))
					{
						$nchmod=$_POST['nchmod'];
					}			
			if (!file_exists($chmod) && !is_dir($chmod)) 
				{
					$adminTpl->admin_head('Файловый менеджер');		
					$adminTpl->info('Ошибка: Директория '.$chmod.' не найдена', 'error');
					$adminTpl->admin_foot();
					exit;
				}
			if (!is_writable($chmod)) 
				{
					$adminTpl->admin_head('Файловый менеджер');		
					$adminTpl->info('Ошибка: Отсутствуют права доступа к директории!', 'error');
					$adminTpl->admin_foot();
					exit;
				}
			if (isset($nchmod) && !empty($nchmod))
				{
					$file_name=preg_replace("#(.+)\/([^\/]+)$#","\\2",$chmod);
					$nchmod=trim($nchmod);
					if (chmod($chmod,base_convert($nchmod,8,10))) 
						{
							$adminTpl->admin_head('Файловый менеджер');		
							$adminTpl->info('Права на '.$file_name.' успешно изменены!');
							$adminTpl->admin_foot();
							exit;
						}
					else 
					{
						$adminTpl->admin_head('Файловый менеджер');		
						$adminTpl->info('Невозможно сменить права на '.$file_name, 'error');
						$adminTpl->admin_foot();
						exit;
					}
				}			
			echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">
                              <header class="panel-heading">Права доступа:</header>
                                                <div class="panel-body">
													<form class="form-inline" method="POST">
														<input type="hidden" name="action" value="chemod">
														<input type="hidden" name="DIR" value="'.$chmod.'">
														<b>Новые права: </b><br><br>
														<div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Введите новое имя файла/директории: </label>
                                                            <input type="text" name="nchmod" class="form-control" size=40" value="'.substr(sprintf('%o', fileperms($chmod)), -3).'">
                                                        </div>													
														<button type="submit" class="btn btn-info">Сохранить</button><br>
														<font style="font-size:11px;">Права должны иметь вид 3 цифр, например 755</font>
                                                    </form>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';    
		}		
	function afile()
		{
			global $fm_conf;			
			if (isset($_POST['action']))
				{
					$action = $_POST['action'];
					if (isset($_POST['DIR']))
					{
						$DIR = $_POST['DIR'];
					}	
					if (isset($_POST['file']))
					{
						$file = $_POST['file'];
					}	
					switch($action)
					{
						case "rename":
							rname($DIR);
							break;	
						case "del":
							deldir($DIR);
							break;			
						case "change":
							fcopy($DIR,'false');
							break;
						case "copy":
							fcopy($DIR,'true');
							break;
						case "chemod":
							chemod($DIR);
							break;
						case "edit":
							rall($DIR);
							break;
					}
				}	
		}
		
	function lists() 
		{
			global $fm_conf,$do;
			if (isset($_POST['rname']))
				{
					$rname = $_POST['rname'];
					rname($rname);
				}				
			if (isset($_POST['del']))
				{
					$del = $_POST['del'];
					deldir($del);
				}
			if (isset($_POST['r']))	
				{
					$r = $_POST['r'];
					rall($r);
				}
			if (isset($_POST['mkdir']))	
				{
					$mkdir = $_POST['mkdir'];
					makedir($mkdir);
				}
			if (isset($_POST['chmod']))
				{
					$chmod = $_POST['chmod'];
					chmod($chmod);
				}
			if (isset($_POST['fcopy']))	
				{
					$fcopy = $_POST['fcopy'];
					if (isset($_POST['done'])) 
					{
						fcopy($fcopy,'true');
					}
					else 
					{
						fcopy($fcopy);
					}
				}
			if (isset($_POST['DIR'])) $DIR=trim(@$_POST['DIR']);     			
			if (empty($DIR)) 
			{

                $dir=$fm_conf['path'];
            }
			else
			{
                $dir=$DIR;
            }
			echo '<div class="row">
					<div class="col-lg-12">
						<section class="panel">

                                                <div class="panel-body">
												<table width="100%">
												<tbody><tr>
												<td>
													 <form id="form_create" role="form" method="POST" action="{ADMIN}/fm/create">
														<input type="hidden" name="DIR" value="'.$dir.'">													
													 </form>
													 <form id="form_makedir" role="form" method="POST" action="{ADMIN}/fm/makedir">
														<input type="hidden" name="DIR" value="'.$dir.'">													
													 </form>
													<a style="cursor: pointer" class="btn btn-success btn-outline" onclick="document.getElementById(\'form_create\').submit();return false;">Создать файл</a>
													 <a style="cursor: pointer" class="btn btn-primary btn-outline" onclick="document.getElementById(\'form_makedir\').submit();return false;">Создать папку</a>
													
																									
												</td>
												<td>
                                                    <form class="form-inline" role="form" align="right" method="POST" action="{ADMIN}/fm/">
													 <div class="form-group">
                                                            <label class="sr-only" for="exampleInputEmail2">Директория:</label>
                                                            <input type="text" name="DIR" class="form-control" value="'.$dir.'">
                                                        </div>
														<button type="submit" class="btn btn-default">Перейти</button>
                                                    </form>
												</td>
											</tr>
										</tbody></table>
                                                </div>
                                            </section>
                                        </div>
                                    </div>';      
            if ($dir!=='' && !is_dir($dir))
				{
					$adminTpl->info('Ошибка: Директория не существует!');
				}
			else
				{
					echo '
					 <script>
					  function del(url, fn) {
					  var a = confirm(\"Вы действительно хотите удалить \"+fn+\"?\");
					  if (a == true){
					  window.location=url;
						  }
					   }
					 </script>
					<div class="row"><div class="col-lg-12"><section class="panel">';
					$pr_dir=preg_replace("#\/([^\/]+)$#","",$dir);
					echo '<div class="row">
							<div class="col-lg-12">
								<section class="panel">
									<div class="panel-heading">										
										<form id="form_back" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$pr_dir.'">
												Каталог: <b>'.$dir.'</b>  <a style="cursor: pointer" onclick="document.getElementById(\'form_back\').submit();return false;">[Назад]</a>
										</form>										
									</div>
									<div class="panel-body no-padding">					
									<table class="table no-margin">
										<thead>
											<tr>												
												<th class="col-md-4"><span class="pd-l-sm"></span>Файл/Папка</th>
												<th class="col-md-2">Размер</th>
												<th class="col-md-2">Создан</th>
												<th class="col-md-2">Изменен</th>
												<th class="col-md-3">' . _ACTIONS . '</th>
											</tr>
										</thead>
										<tbody>';    
					$handle = opendir ($dir);
					while ($file = readdir($handle))
					{
						if ($file == "." or $file == "..")
							{
								continue;
                            }
						if (filetype("$dir/$file") !== "file")
							{
								$size=0;
								$size=size_dir("$dir/$file",$size);
								$size=round($size/1024, 2);
								echo '<tr>
										<td>											
											<form id="form_id_'.$file.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$dir.'/'.$file.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.'\').submit();return false;"><img src="media/filetypes/folder.png" ><span class="pd-l-sm"></span>'.$file.'</a>
											</form>
										</td>
										<td>'.$size.' кб</td>
										<td>'.date('d.m.Y', filectime("$dir/$file")).'</td>
										<td>'.date('d.m.Y', filemtime("$dir/$file")).'</td>
										<td>
											<form id="form_afile_'.$file.'" role="form"  method="POST" action="{ADMIN}/fm/afile">
												<input type="hidden" name="DIR" value="'.$dir.'/'.$file.'">
												<input type="hidden" name="file" value="'.$file.'">
												<select onchange="document.getElementById(\'form_afile_'.$file.'\').submit();return false;" name="action">
													<option selected>Выбор действия</option>
													<option value="rename">Переименовать</option>
													<option value="del" onClick="return getConfirm(\'Вы действительно хотите забанить - ?\')">Удалить</option>
													<option value="change">Переместить</option>
													<option value="copy">Копировать</option>
													<option value="chemod">Сменить Права</option>
												</select>
											</form>
										</td>										
									</tr>';
							}
					}
					$handle = opendir ($dir);
					while ($file = readdir($handle))
					{
						if ($file == "." or $file == "..")
							{
								continue;
							}
						if (filetype("$dir/$file") == "file")
							{
								echo '<tr>
										<td>											
											<form id="form_id_'.$file.'" role="form"  method="POST" action="{ADMIN}/fm/">
												<input type="hidden" name="DIR" value="'.$dir.'/'.$file.'">
												<span class="pd-l-sm"></span>
												<a style="cursor: pointer" onclick="document.getElementById(\'form_id_'.$file.'\').submit();return false;"><img src="media/filetypes/blank.png" ><span class="pd-l-sm"></span>'.$file.'</a>
											</form>
										</td>
										<td>'.round(filesize("$dir/$file")/1024, 2).' кб</td>
										<td>'.date('d.m.Y', filectime("$dir/$file")).'</td>
										<td>'.date('d.m.Y', filemtime("$dir/$file")).'</td>
										<td>
											<form id="form_afile_'.$file.'" role="form"  method="POST" action="{ADMIN}/fm/afile">
												<input type="hidden" name="DIR" value="'.$dir.'/'.$file.'">
												<input type="hidden" name="file" value="'.$file.'">
												<select onchange="document.getElementById(\'form_afile_'.$file.'\').submit();return false;" name="action">
													<option selected>Выбор действия</option>
													<option value="rename">Переименовать</option>
													<option value="del" onClick="return getConfirm(\'Вы действительно хотите забанить - ?\')">Удалить</option>
													<option value="change">Переместить</option>
													<option value="copy">Копировать</option>
													<option value="chemod">Сменить Права</option>
													<option value="edit">Редактировать</option>
												</select>
											</form>
										</td>										
									</tr>';								
							}
					}
					closedir($handle);                    
					echo '</table></section></div></div>';
				}
		}







switch(isset($url[2]) ? $url[2] : null) {
	default:
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		lists();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;	
		
	case "create":
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		mfile();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;
	
	case "makedir":
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		makedir();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;
		
	case "search":
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		search();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;
		
	case "afile":
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		afile();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;
	
	case "upload":
		loadconfig('fm');
		$adminTpl->admin_head('Файловый менеджер');		
		$adminTpl->open();				
		upload();		
		$adminTpl->close();
		$adminTpl->admin_foot();		
		break;
		
	case "clear":
		foreach(glob(ROOT.'tmp/*.log') as $file) @unlink($file);		
		location(ADMIN.'/log/ok');
		break;
}






?>