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

global $lang;
switch(isset($url[2]) ? $url[2] : null) {
	default:
		$adminTpl->admin_head($lang['polls']);
		echo '<div id="content" class="animated fadeIn">';
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_menu ORDER BY id");			
		if($db->numRows($query) > 0) 
		{
		echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'.$lang['menu_list'].':</span>                
              </div>
			  <div class="panel-body pn"> 
				<form id="tablesForm"  style="margin:0; padding:0" method="POST" action="{ADMIN}/voting/action">
					<table class="table table-striped">
						<thead>
							<tr>
								<th><span class="pd-l-sm"></span>#</th>
								<th>' . $lang['menu_name'] . '</th>
								<th>' . $lang['polls_list_variant'] . '</th>
								<th>' . $lang['polls_list_answer'] . '</th>
								<th class="text-center">' . $lang['status'] . '</th>
								<th>' . $lang['action'] . '</th>								
								<th>
									<div class="checkbox-custom mb15">
										<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
										<label for="all"></label>
									</div>	
								</th>
							</tr>
						</thead>
						<tbody>';				
						while($menu = $db->getRow($query)) 
						{
							if ($menu['active'])
							{
								$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
							}
							else
							{
								$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
							}
							echo '
							<tr>
								<td><span class="pd-l-sm"></span>' . $menu['id'] . '</td>
								<td>' . $menu['title'] . '</td>
								<td>' . $menu['theme'] . '</td>
								<td>' . $menu['type'] . '</td>
								<td class="text-center">' . $status_icon . '</td>
								<td>
									<div class="btn-group">
										<button type="button" onclick="location.href = \'{ADMIN}/menu/edit/'. $menu['id'] .'\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
										<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
										<ul role="menu" class="dropdown-menu">	
										
											<li><a href="'.ADMIN.'/menu/retivate/'.$menu['id'].'">'.(($menu['active'] == 0) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>   
											<li class="divider"></li>
											<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$menu['id'].'\')">' . $lang['delete'] .'</a></li>
										</ul>
									</div>
									<div id="modal-form-'.$menu['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<div class="panel">
										  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
										  <div class="panel-body">
											<h3 class="mt5">' . str_replace('[menu]', $menu['title'], $lang['menu_del_title']) .  '</h3>							
											<hr class="short alt">
											<p>' . str_replace('[menu]', $menu['title'], $lang['menu_del_text']) .  '</p>
										  </div>
										  <div class="panel-footer text-right">
											<button type="button" onclick="location.href = \'{ADMIN}/menu/delete/'.$menu['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
										  </div>
										</div>
									</div>
								</td>
								<td>
									<div class="checkbox-custom mb15">
										<input id="checkbox' . $menu['id'] . '" type="checkbox" name="checks[]" value="' . $menu['id'] . '"><label for="checkbox' . $menu['id'] . '"></label>
									</div>
								</td>
							</tr>';	
						}			
				echo '</tbody>
					  <tfoot class="footer-menu">
						<tr>                    
						  <td colspan="9">
							<nav class="text-right">
								<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . $lang['delete'] . '" />
							 </nav>
						  </td>
						</tr>
					  </tfoot>
				  </table>
				 </form>
				</div>
			</div>';	
		} 
		else 
		{
			$adminTpl->info($lang['polls_empty'], 'empty', null, $lang['polls_list'], $lang['polls_add'], ADMIN.'/voting/add');	
		}
		echo'</div>';		
		$adminTpl->admin_foot();
		break;	
		
	case 'add':
		add();
		break;
		
	case 'save':
		$adminTpl->admin_head($lang['polls_add']);
		$title = filter($_POST['title'], 'title');
		$vars = filter($_POST['vars'], 'html');
		$max = intval($_POST['max']);
		$variants = explode("\n", $vars);
		echo '<div id="content" class="animated fadeIn">';
		if($title && $vars)
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_polls` ( `id` , `title` , `votes` , `max`, `active` ) VALUES (NULL, '" . $db->safesql(processText($title)) . "', '0', '" . $max . "', '1');");
			list($id) = $db->fetchRow($db->query("SELECT id FROM `" . DB_PREFIX . "_polls` WHERE title = '" . $db->safesql(processText($title)) . "' AND max = '" . $max . "'"));
		
			foreach($variants as $var)
			{
				if($var !== '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_poll_questions` ( `id` , `pid` , `variant` , `position` , `vote` ) VALUES (NULL, '" . $id . "', '" . str_replace(',', '||', trim($db->safesql($var))) . "', '', '0');");
				}
			}			
			$adminTpl->info($lang['polls_add_ok'], 'info', null, $lang['info'], $lang['polls_list'], ADMIN.'/voting');
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';
		$adminTpl->admin_foot();
		break;
	
	case 'edit':
		$id = intval($url[3]);
		add($id);		
		break;
		
	case 'save_edit':
		$adminTpl->admin_head($lang['polls_edit']);
		$id = intval($_POST['id']);
		$title = filter($_POST['title']);
		$vars = filter($_POST['vars']);
		$max = intval($_POST['max']);
		$votes = intval($_POST['votes']);
		$variants = explode("\n", $vars);
		echo '<div id="content" class="animated fadeIn">';
		if($title && $vars)
		{
			$db->query("DELETE FROM `" . DB_PREFIX . "_poll_questions` WHERE `pid` = '" . $id . "'");
			$db->query("DELETE FROM `" . DB_PREFIX . "_poll_voting` WHERE `pid` = '" . $id . "'");
			$allVote = 0;
			foreach($variants as $var)
			{
				if($var !== '')
				{
					$lo = explode('|', $var);
					$db->query("INSERT INTO `" . DB_PREFIX . "_poll_questions` ( `id` , `pid` , `variant` , `position` , `vote` ) VALUES (NULL, '" . $id . "', '" . str_replace(',', '||', trim($db->safesql($lo[0]))) . "', '', '" . $lo[1] . "');");
					$allVote = $allVote+$lo[1];
				}
			}
			$db->query("UPDATE `" . DB_PREFIX . "_polls` SET `title` = '" . $title . "', `votes` = '".$allVote."', `max` = '" . $max . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");			
			$adminTpl->info($lang['polls_edit_ok'], 'info', null, $lang['info'], $lang['polls_list'], ADMIN.'/voting');
		}
		else
		{
			$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
		}
		echo '</div>';
		$adminTpl->admin_foot();
		break;
		
	case 'view':
		$id = intval($url[3]);
		if(!empty($id))
		{
			global $adminTpl, $config, $core, $lang, $db;
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_poll_questions` WHERE pid = '" . $id . "'");
			$title = prepareTitle($rows['title']);
			$max = $rows['max'];
			$admhead = $lang['polls_edit'];
			$btn = $lang['update'];
			$variant = '';
			$action = ADMIN.'/voting/save_edit';
			$varinat = '';
			$numb = '';
			while($rowsq = $db->getRow($query))
			{	
				$text=rtrim($rowsq['variant'],"\n\r");
				$varinat .= '"'.$text.'", ';
				$numb .= $rowsq['vote'].', ';
					
			}
			$varinat = substr($varinat, 0, -2);
			$numb = substr($numb, 0, -2);
			$adminTpl->admin_head($lang['polls_view']);						
			echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-info panel-border top">
					<div class="panel-heading"><span class="panel-title">'.$lang['polls_view'].'</span>			
				</div>
				  <div class="panel-body">
					<p class="p2">
					<div id="result" style="min-width: 310px; max-width: 800px; height: 200px; margin: 0 auto"></div>';				
					$adminTpl->footIncludes[] = '
					<script type="text/javascript">
						var pollsHighCharts = function ()
						{				 
							var highColors = [bgWarning, bgPrimary, bgInfo, bgAlert, bgDanger, bgSuccess, bgSystem, bgDark];
							var sparkColors = {
								"primary": [bgPrimary, bgPrimaryLr, bgPrimaryDr],
								"info": [bgInfo, bgInfoLr, bgInfoDr],
								"warning": [bgWarning, bgWarningLr, bgWarningDr],
								"success": [bgSuccess, bgSuccessLr, bgSuccessDr],
								"alert": [bgAlert, bgAlertLr, bgAlertDr]
							};
							var pollsHighCharts = function()
							{ 
								var pollsHighBars = function() 
								{
									var bars1 = $("#result");
									if (bars1.length) 
									{
										$("#result").highcharts({
											colors: highColors,
											credits: false,
											legend: {
												enabled: false,
												y: -5,
												verticalAlign: "top",
												useHTML: true
											},
											chart: {
												spacingLeft: 30,
												type: "bar",
												marginBottom: 0,
												marginTop: 0
											},
											title: {
												text: null
											},
											xAxis: {
												showEmpty: false,
												tickLength: 80,
												lineColor: "#fff",
												tickColor: "#eee",
												offset: 1,
												categories: ['.$varinat.'],
												title: {
												  text: null
												},
												labels: {
												  align: "right",
												}
											},
											yAxis: {
												min: 0,
												gridLineWidth: 0,
												showEmpty: false,
												title: {
												  text: null
												},
												labels: {
												  enabled: false,
												}
											},
											tooltip: {
												valueSuffix: " '.$lang['polls_people'].'"
											},
											plotOptions: {
												bar: {},
												column: {
													colorByPoint: true,
														}
											},
											series: [{
												id: 0,
												name: "'.$lang['polls_user'].'",
												data: ['.$numb.']
												}]
										});
									}
								};	pollsHighBars();
							}
							return {
								init: function () { 
								pollsHighCharts(); 
								}	
							}
						}();					
					</script>';
					$adminTpl->js_code[] ='pollsHighCharts.init(); ';					
				echo '</p>
				  </div>
				</div></div>';
			$adminTpl->admin_foot();
		}
		else
		{
			location(ADMIN.'/voting');
		}	
		break;
		
	case 'delete':
		$id = intval($url[3]);
		deleteVot($id);
		location(ADMIN.'/voting');
		break;
	
	case 'retivate':
		$id = intval($url[3]);
		if(!empty($id))
		{
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$active = 0;
			if ($rows['active']==0)
			{
				$active = 1;
			}
			$db->query("UPDATE `" . DB_PREFIX . "_polls` SET `active` = '" . $active . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");
			location(ADMIN.'/voting');
		}
		else
		{
			location(ADMIN.'/voting');
		}		
		break;
		
	case 'action':
		$checks = $_POST['checks'];
		foreach($checks as $check)
		{
			deleteVot(intval($check));
		}
		location(ADMIN . '/voting/del');
		break;
}

function add($id = null) 
{	
		global $adminTpl, $config, $core, $lang, $db;
		if(isset($id)) 
		{
			$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_polls` WHERE id = '" . $id . "'"));
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_poll_questions` WHERE pid = '" . $id . "'");
			$title = prepareTitle($rows['title']);
			$max = $rows['max'];
			$admhead = $lang['polls_edit'];
			$btn = $lang['update'];
			$variant = '';
			$action = ADMIN.'/voting/save_edit';
			while($rowsq = $db->getRow($query))
			{	
				$text = $rowsq['variant'];															
				$text=rtrim($text,"\n\r");
				$variant .= $text . "|" . $rowsq['vote'] . "\n";		
			}			
		} 
		else 
		{
			$title = isset($_POST['title']) ? filter($_POST['title']) : '';	
			$variant = isset($_POST['vars']) ? filter($_POST['vars']) : '';	
			$max = isset($_POST['max']) ? filter($_POST['max']) : '0';
			$admhead = $lang['polls_add'];
			$btn = $lang['add'];
			$action = ADMIN.'/voting/save';
		}
		$adminTpl->admin_head($admhead);	
		
		
		echo '<div id="content" class="animated fadeIn">
				<section id="content" class="table-layout animated fadeIn">
					<aside data-tray-height="match" class="tray tray-left tray350">
            <p class="mt25"><strong>'.$lang['menu_item_new'].'</strong></p>
          
            <hr class="alt short">
		 <form role="form" class="form-horizontal">
                    <div class="form-group">
                      <label class="col-lg-3 control-label">Static Field</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <p class="form-control-static text-muted">email@example.com</p>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputStandard" class="col-lg-3 control-label">Standard</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <input id="inputStandard" type="text" placeholder="Type Here..." class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputSelect" class="col-lg-3 control-label">Select List</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <select class="form-control">
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="disabledInput" class="col-lg-3 control-label">Disabled</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <input id="disabledInput" type="text" placeholder="A Disabled Form" disabled="" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="textArea1" class="col-lg-3 control-label">Text Area Expand</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <textarea id="textArea1" rows="4" class="form-control textarea-grow"></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="textArea2" class="col-lg-3 control-label">Text Area</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <textarea id="textArea2" rows="3" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="textArea3" class="col-lg-3 control-label">Disabled Text Area</label>
                      <div class="col-lg-8">
                        <div class="bs-component">
                          <textarea id="textArea3" rows="3" disabled="" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                  </form>


          
          </aside>
          <!-- begin: .tray-center-->
          <div class="tray tray-center bg-white">
            <div class="row table-layout">
              <div class="col-xs-5 va-m pln">
                <p><strong>Структура меню</strong></p>
              </div>
              <div class="col-xs-7 va-b prn">
                <menu id="nestable-menu" class="text-right mb10">
                  <div data-toggle="buttons" class="btn-group">
                    <label class="btn btn-default">
                      <input id="option1" data-action="expand-all" type="radio" name="options" autocomplete="off" checked="">Expand All
                    </label>
                    <label class="btn btn-default">
                      <input id="option2" data-action="collapse-all" type="radio" name="options" autocomplete="off">Collapse All
                    </label>
                  </div>
                </menu>
              </div>
            </div>
            <textarea id="nestable-output" class="form-control"></textarea>
            <div class="row mt5">
              <div class="col-md-12">
                <!-- List 1-->
                <div id="nestable" class="dd mb35">
                  <ol class="dd-list">
                    <li data-id="1" class="dd-item">
                      <div class="dd-handle">LifeStyle<span class="text-success pull-right fs11 fw600">Approved</span></div>
                    </li>
                    <li data-id="2" class="dd-item">
                      <div class="dd-handle">Sports<span class="text-success pull-right fs11 fw600">Active</span></div>
                    </li>
                    <li data-id="3" class="dd-item">
                      <div class="dd-handle">Gossip</div>
                      <ol class="dd-list">
                        <li data-id="4" class="dd-item">
                          <div class="dd-handle">Item 5</div>
                          <ol class="dd-list">
                            <li data-id="5" class="dd-item">
                              <div class="dd-handle">Item 6</div>
                            </li>
                            <li data-id="6" class="dd-item">
                              <div class="dd-handle">Item 7</div>
                            </li>
                            <li data-id="7" class="dd-item">
                              <div class="dd-handle">Item 8</div>
                            </li>
                          </ol>
                        </li>
                        <li data-id="8" class="dd-item">
                          <div class="dd-handle">Item 9</div>
                        </li>
                        <li data-id="9" class="dd-item">
                          <div class="dd-handle">Item 10</div>
                        </li>
                      </ol>
                    </li>
                  </ol>
                </div>
                
                <h5 class="text-muted"> Example with Contextual Nodes</h5>
                <hr class="short alt">
                <!-- List 3-->
                <div id="nestable-contextual" class="dd">
                  <ol class="dd-list">
                    <li data-id="13" class="dd-item dd-primary">
                      <div class="dd-handle">LifeStyle<span class="pull-right fs11 fw600">Approved</span></div>
                      <ol class="dd-list pb15">
                        <li data-id="14" class="dd-item">
                          <div class="dd-handle">10 Reasons to Stay Single!</div>
                          <div class="dd-content">
                            <div class="media"><span class="text-warning pull-right fs11 fw600">Article</span><a href="#" class="media-left"><img data-src="holder.js/46x42" alt="holder-img"></a>
                              <div class="media-body">
                                <h4 class="media-heading">Frank Ocean <small class="text-muted">- 08/16/22</small>
                                </h4>
                                <p class="mb5">Last Updated 36 days ago by<a href="#" class="text-system"> Max</a></p>
                              </div>
                            </div>
                          </div>
                        </li>
                        <li data-id="15" class="dd-item">
                          <div class="dd-handle">What Women Love</div>
                          <div class="dd-content">
                            <div class="media"><span class="text-warning pull-right fs11 fw600">Article</span><a href="#" class="media-left"><img data-src="holder.js/46x42" alt="holder-img"></a>
                              <div class="media-body">
                                <h4 class="media-heading">Olivia White <small class="text-muted">- 08/16/22</small>
                                </h4>
                                <p class="mb5">Last Updated 36 days ago by<a href="#" class="text-system"> Max</a></p>
                              </div>
                            </div>
                          </div>
                        </li>
                      </ol>
                    </li>
                    <li data-id="16" class="dd-item dd-info">
                      <div class="dd-handle">Sports<span class="pull-right fs11 fw600">Active</span></div>
                      <ol class="dd-list pb15">
                        <li data-id="17" class="dd-item">
                          <div class="dd-handle">Camping trips for Girls</div>
                          <div class="dd-content">
                            <div class="media"><span class="text-warning pull-right fs11 fw600">Article</span><a href="#" class="media-left"><img data-src="holder.js/46x42" alt="holder-img"></a>
                              <div class="media-body">
                                <h4 class="media-heading">Sara Downs <small class="text-muted">- 08/16/22</small>
                                </h4>
                                <p class="mb5">Last Updated 36 days ago by<a href="#" class="text-system"> Max</a></p>
                              </div>
                            </div>
                            <div class="media br-t mt5 pt10"><a href="#" class="media-left hidden"><img data-src="holder.js/46x42" alt="holder-img"></a>
                              <div class="media-body">
                                <h4 class="media-heading">Attachments<span class="label label-xs label-success ml5">Updated</span></h4>
                                <p class="text-muted"><span class="fa fa-paperclip pr10"></span>The-Grand-Canyon_Cover.jpeg</p>
                                <p class="text-muted mb5"><span class="fa fa-paperclip pr10"></span>military_vet_interview-questions.text</p>
                              </div>
                            </div>
                          </div>
                        </li>
                      </ol>
                    </li>
                  </ol>
                </div>
                <div class="nestable-output hidden">
                  <p><strong>Serialised Output (per list)</strong></p>
                  <textarea id="nestable-output2" class="form-control hidden"></textarea>
                </div>
                <div class="nestable-output hidden">
                  <p><strong>Serialised Output (per list)</strong></p>
                  <textarea id="nestable-output3" class="form-control hidden"></textarea>
                </div>
              </div>
            </div>
          </div>
        </section>
		 </div>		
				';	




$adminTpl->js_code[] = "

   // Nestable Output
        var updateOutput = function (e) {
          var list = e.length ? e : $(e.target),
                  output = list.data('output');
          if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
          } else {
            output.val('JSON browser support required for this demo.');
          }
        };
        // Init Nestable on list 1
        $('#nestable').nestable({
          group: 1
        }).on('change', updateOutput);
        // Init Nestable on list 2
        $('#nestable-alt').nestable({
          group: 2
        }).on('change', updateOutput);
        // Init Nestable on list 3
        $('#nestable-contextual').nestable({
          group: 3
        }).on('change', updateOutput);
        // nestable serialized output functionality
        updateOutput($('#nestable').data('output', $('#nestable-output')));
        updateOutput($('#nestable-alt').data('output', $('#nestable-output2')));
        updateOutput($('#nestable-contextual').data('output', $('#nestable-output3')));
        // nestable menu functionality
        $('#nestable-menu').on('change', function (e) {
          var target = $(e.target),
                  action = target.data('action');
          if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
          }
          if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
          }
        });";







				
		$adminTpl->admin_foot();
}

function deleteVot($id)
{
	global $adminTpl, $db;
	$db->query("DELETE FROM `" . DB_PREFIX . "_poll_questions` WHERE `pid` = '" . $id . "'");
	$db->query("DELETE FROM `" . DB_PREFIX . "_poll_voting` WHERE `pid` = '" . $id . "'");
	$db->query("DELETE FROM `" . DB_PREFIX . "_polls` WHERE `id` = '" . $id . "'");
}
