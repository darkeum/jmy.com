<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2015 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @revision	   25.02.2015
*/
 
 
if (!defined('ACCESS') && !$core->auth->isAdmin && $url[0] !== ADMIN) {
    header('Location: /');
	exit;
}

define('ADMIN_ACCESS', true);
define('COOKIE_VISIT', md5(getenv("REMOTE_ADDR")) . '-admin_visit');
define('SESS_AUTH', md5(getenv("REMOTE_ADDR")) . '-auth');
define('SESS_COUNT', md5(getenv("REMOTE_ADDR")) . '-counter');
session_start();

require ROOT . 'etc/admin.config.php';
require ROOT . 'root/functions.php';
require ROOT . 'root/ajax_funcs.php';
require ROOT . 'root/admin_tpl.class.php';

$core->loadLangFile('root/langs/{lang}.navigation.php');

if(!empty($admin_conf['ipaccess']))
{
	$IPs_arr = explode("\n", $admin_conf['ipaccess']);
	$parse_ip = @ip2long(getRealIpAddr()); 
	foreach($IPs_arr as $IPs) 
	{ 
		$IPs = explode('|', $IPs);
		if(count($IPs) == 2)
		{
			if($parse_ip <= @ip2long($IPs[0]) && $parse_ip <= @ip2long($IPs[1]))
			{
				$_SESSION[SESS_AUTH] = null;
				$_SESSION[SESS_COUNT] = 0;
				setcookie(COOKIE_AUTH, '', time(), '/');
				setcookie(COOKIE_PAUSE, '', time(), '/');
				location();
			}
		}
	}
}

function admin_main() 
{
	global $adminTpl,  $db, $core, $config;
	$last_visit = time();
	$last_ip = $_SERVER['REMOTE_ADDR'];
	$query = '';
	
	if(!isset($_COOKIE[COOKIE_VISIT]) && !isset($_SESSION[SESS_AUTH])) 
	{
		if($db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR']) . "', '" . $core->auth->user_id . "', '" . str_replace('{nick}', $core->auth->user_info['nick'], _LOG_WRITE) . "', '1')")) 
		{
			setcookie(COOKIE_VISIT, time(), time() + 86400, '/');
		}		
		$last_visit = time();
		$last_ip = $_SERVER['REMOTE_ADDR'];
	} 
	else 
	{
		$query = $db->query("SELECT * FROM " . DB_PREFIX . "_logs ORDER BY time DESC");
		$i = 0;
		while($log = $db->getRow($query)) {
			$i++;			
			if($i == 1)
			{
				$last_visit = $log['time'];
				$last_ip = $log['ip'];
			}			
			$logs[$log['level']][$log['uid']][$log['time']] = $log['ip'] . '-' . $log['history'];
		}
	}	
	$adminTpl->admin_head(_MAIN_PAGE);
	$adminTpl->js_code = "      
        var highColors = [bgSystem, bgSuccess, bgWarning, bgPrimary];
        // Chart data
        var seriesData = [{
          name: 'Phones',
          data: [0, 9, 17, 22, 19, 11.5, 5.2, 9, 17, 22, 19, 11.5, 5.2, 9, 17, 22, 19, 11.5, 5.2]
        }];
        var ecomChart = $('#ecommerce_chart1');
        if (ecomChart.length) {
          ecomChart.highcharts({
            chart: {
              type: 'areaspline',
              marginTop: 30,
              backgroundColor: 'transparent',
            },
            credits: {
              enabled: false
            },
            title: {
              text: ''
            },
            yAxis: {
              title: {
                text: ''
              },
              gridLineColor: '#f0f2f6',
              gridLineWidth: 2,
              labels: {
                formatter: function () {
                  return this.value;
                },
                style: {
                  color: '#d1d4da',
                  \"textTransform\": \"uppercase\",
                  \"fontSize\": \"12px\",
                  \"letterSpacing\": 0.02
                }
              }
            },
            xAxis: {
              type: 'datetime',
              labels: {
                overflow: 'justify',
                style: {
                  color: '#d1d4da',
                  \"textTransform\": \"uppercase\",
                  \"fontSize\": \"10px\",
                  \"letterSpacing\": 0.02
                },
                y: 30
              },
              lineWidth: 0,
              tickWidth: 0,
              formatter: function () {
                return this.value; // clean, unformatted number for year
              }
            },
            tooltip: {
              valueSuffix: ' $'
            },
            plotOptions: {
              areaspline: {
                lineWidth: 1,
                states: {
                  hover: {
                    lineWidth: 1
                  }
                },
                marker: {
                  enabled: false
                },
                pointInterval: 86400000, // one day
                pointStart: Date.UTC(2016, 8, 24, 0, 0, 0)
              }
            },
            series: [{
              name: 'Hestavollane',
              showInLegend: false,
              lineColor: 'rgba(0,0,0,0)',
              fillColor: {
                linearGradient: {
                  x1: 0, y1: 0,
                  x2: 0, y2: 1
                },
                stops: [[0.0, '#5ddcff'],
                  [0.5, '#5ddcff'],
                  [1.0, '#5cbbe3']]
              },
              data: [0, 850, 900, 1200, 1500, 1000, 1300, 1500, 2300, 2500, 2600, 2200, 3000, 3600, 3500, 2500, 2000, 0]
      
            }],
            navigation: {
              menuItemStyle: {
                fontSize: '10px'
              }
            }
          });
      
      
          $('#ecommerce_chart1-new-data').click(function (e) {
            e.preventDefault();
            $('.chart-1').removeClass('active-default')
            $(this).addClass('active-success');
      
            var chart = $('#ecommerce_chart1').highcharts();
            chart.series[0].setData([0, 850, 900, 1200, 1100, 1000, 1200, 1400, 2200, 2300, 2600, 2200, 2700, 3100, 3000, 2400, 2000, 0]);
          });
      
          $('#ecommerce_chart1-new-data-2').click(function (e) {
            e.preventDefault();
            $('.chart-1').removeClass('active-success');
            $(this).addClass('active-default');
      
            var chart = $('#ecommerce_chart1').highcharts();
            chart.series[0].setData([0, 450, 800, 1300, 1600, 1200, 1100, 1500, 2300, 2400, 2500, 2500, 2800, 2300, 2100, 1500, 1300, 300]);
          });
        }
      
        var seriesData2 = [{
          name: 'Phones',
          data: [5.0, 9, 17, 22, 19, 11.5, 5.2, 9.5, 11.3, 15.3, 19.9, 24.6]
        }];
      
        var ecomChart2 = $('#ecommerce_chart2');
        if (ecomChart2.length) {
          ecomChart2.highcharts({
            chart: {
              zoomType: 'x',
              backgroundColor: 'transparent',
            },
            credits: false,
            title: {
              text: ''
            },
            yAxis: {
              title: {
                text: ''
              },
              gridLineColor: '#f0f2f6',
              gridLineWidth: 2,
              labels: {
                formatter: function () {
                  return this.value;
                },
                style: {
                  color: '#d1d4da',
                  \"textTransform\": \"uppercase\",
                  \"fontSize\": \"12px\",
                  \"letterSpacing\": 0.02
                }
              }
            },
            xAxis: {
              type: 'datetime',
              categories: ['Jan', 'Feb', 'Mar', 'Apr',
                'May', 'Jun', 'Jul', 'Aug',
                'Sep', 'Oct', 'Nov', 'Dec'
              ],
              tickWidth: 0,
              lineWidth: 0,
              labels: {
                overflow: 'justify',
                style: {
                  color: '#d1d4da',
                  \"textTransform\": \"uppercase\",
                  \"fontSize\": \"10px\",
                  \"letterSpacing\": 0.02
                },
                y: 30
              }
            },
            legend: {
              enabled: false
            },
            plotOptions: {
              area: {
                fillColor: {
                  linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                  },
                  stops: [
                    [0, 'rgba(67, 199, 215, .7)'],
                    [0.5, 'rgba(67, 199, 215, .3)'],
                    [1, 'rgba(67, 199, 215, 0)']
                  ]
                },
                marker: {
                  radius: 6,
                  lineWidth: 4,
                  lineColor: '#fff'
                },
                lineWidth: 3,
                threshold: null
              }
            },
      
            series: [{
              type: 'area',
              name: 'Orders',
              data: [0, 1400, 900, 1200, 1500, 1000, 1300, 1500, 2900, 2500, 2600, 2200],
              color: '#43c7d7'
            }]
          });
      
          $('#ecommerce_chart2-new-data').click(function (e) {
            e.preventDefault();
            $('.filter-range').removeClass('bg-whitesmoke');
            $(this).addClass('bg-whitesmoke');
      
            var chart = $('#ecommerce_chart2').highcharts();
            chart.series[0].setData([0, 1400, 900, 1200, 1500, 1000, 1300, 1500, 2900, 2500, 2600, 2200]);
          });
      
          $('#ecommerce_chart2-new-data-2').click(function (e) {
            e.preventDefault();
            $('.filter-range').removeClass('bg-whitesmoke');
            $(this).addClass('bg-whitesmoke');
      
            var chart = $('#ecommerce_chart2').highcharts();
            chart.series[0].setData([1400, 1200, 0, 900, 1500, 1300, 1000, 2900, 1500, 2600, 2500, 2200]);
          });
      
          $('#ecommerce_chart2-new-data-3').click(function (e) {
            e.preventDefault();
            $('.filter-range').removeClass('bg-whitesmoke');
            $(this).addClass('bg-whitesmoke');
      
            var chart = $('#ecommerce_chart2').highcharts();
            chart.series[0].setData([100, 400, 900, 1100, 1500, 1400, 1600, 1100, 2000, 2100, 1600, 2000]);
          });
        }
      
        var ecomChart3 = $('#ecommerce_chart3');
        if (ecomChart3.length) {
          ecomChart3.highcharts({
            chart: {
              zoomType: 'x',
              backgroundColor: 'transparent',
            },
            credits: false,
            title: {
              text: ''
            },
            yAxis: {
              title: {
                text: ''
              },
              gridLineColor: '#f0f2f6',
              gridLineWidth: 0,
              tickWidth: 0,
              lineWidth: 0,
              labels: {
                enabled: false
              }
            },
            xAxis: {
              labels: {
                enabled: false
              },
              tickWidth: 0,
              lineWidth: 0,
              gridLineWidth: 0
            },
            legend: {
              enabled: false
            },
            plotOptions: {
              area: {
                fillColor: {
                  linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                  },
                  stops: [
                    [0, 'rgba(67, 199, 215, .7)'],
                    [0.5, 'rgba(67, 199, 215, .3)'],
                    [1, 'rgba(67, 199, 215, 0)']
                  ]
                },
                marker: {
                  radius: 3,
                  lineWidth: 0,
                  lineColor: '#fff'
                },
                lineWidth: 2,
                threshold: null
              }
            },
      
            series: [{
              type: 'area',
              name: 'Orders',
              data: [0, 1400, 900, 1200, 1500, 1000, 1300, 1500, 2900, 2500, 2600, 2200],
              color: '#43c7d7'
            }]
          });
        }
		
	
         

		
        // Widget VectorMap
        function runVectorMaps() {
          // Jvector Map Plugin
          var runJvectorMap = function () {
            // Data set
            var mapData = [900, 700, 350, 500];
            // Init Jvector Map
            $('#WidgetMap').vectorMap({
              map: 'world_mill_en',
              backgroundColor: 'transparent',
              series: {
                markers: [{
                  attribute: 'r',
                  scale: [3, 7],
                  values: mapData
                }]
              },
              regionStyle: {
                initial: {
                  fill: '#eaedf1'
                },
                hover: {
                  fill: bgInfo
                }
              },
            });
            // Manual code to alter the Vector map plugin to
            // allow for individual coloring of countries
            var states = ['DE', 'US', 'CA', 'FR', 'HU'];
      
            var colors = [bgInfo, bgInfo, bgInfo, bgInfo, bgInfo];
            var colors2 = [bgInfo, bgInfo, bgInfo, bgInfo, bgInfo];
            $.each(states, function (i, e) {
              $(\"#WidgetMap path[data-code=\" + e + \"]\").css({
                fill: colors[i]
              });
            });
            $('#WidgetMap').find('.jvectormap-marker')
                    .each(function (i, e) {
                      $(e).css({
                        fill: colors2[i],
                        stroke: colors2[i]
                      });
                    });
          }
          if ($('#WidgetMap').length) {
            runJvectorMap();
          }
        }
     ";
	echo '
	<section id="content" class="table-layout animated fadeIn">
	<div class="tray tray-center">
            <div class="tray-inner">
              <div class="row flex-column-reverse-before-md">
                <div class="col-sm-12">
                  <div class="p30">
                    <!-- dashboard tiles-->
                    <h2 class="ib mn mr20">'._PANEL_SUNMENU_STATS.'</h2>
                    <div class="row text-center mt35">
                      <div class="col-sm-6 col-md-3 br-lg-r">
                        <h2 class="mn fs47 ib pr20 monserrat">'.$core->sum_row('news').'</h2>
                        <div class="reveal-xlg-inline-block text-center text-xlg-left">
                          <p class="fs15 text-shady-lady mb2">'._PANEL_STATS_NUM_NEWS.'</p>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-sm-0">
                        <h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('users').'</h2>
                        <div class="reveal-xlg-inline-block text-center text-xlg-left">
                          <p class="fs15 text-shady-lady mb2">'._PANEL_STATS_NUM_USER.'</p>
                        </div>
                      </div>
                      <div class="clearfix visible-sm-block"></div>
                      <div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-md-0">
                        <h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('guestbook').'</h2>
                        <div class="reveal-xlg-inline-block text-center text-xlg-left">
                          <p class="fs15 text-shady-lady mb2">'._PANEL_STATS_NUM_COMM.'</p>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-3 mt-30 mt-md-0">
                        <h2 class="mn monserrat fs47 ib pr20">'.$core->sum_row('blog_posts').'</h2>
                        <div class="reveal-xlg-inline-block text-center text-xlg-left">
                          <p class="fs15 text-shady-lady mb2">'._PANEL_STATS_NUM_BLOG.'</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <hr class="mt-35">
                </div>
                <div class="col-sm-12">
                  <!-- Admin-panels-->
                  <div class="admin-panels">
                    <!-- dashboard activity-->
                    <div id="p01" class="panel mbn mt-40 mt-md-0">
                      <div class="panel-body pbn">
                        <div class="row">
                          <!-- Chart Column-->
                          <div class="col-md-6">
                            <div class="pl30 pr40">
                              <h3 class="ib mn mr20">'._PANEL_STATS_VISIT.'</h3>
                              <div class="float-sm-right mt-10 mt-sm-0">
								<a href="#" id="visit" class="btn btn-xs btn-success chart-1 active-success">'._PANEL_STATS_VISITOR.'</a>
								<a href="#" id="view" class="btn btn-xs btn-default ml4 chart-1">'._PANEL_STATS_VIEWS.'</a></div>
                              <div id="ecommerce_chart1" style="height: 380px;"></div>
                            </div>
                          </div>
                          <!-- Multi Text Column-->
                          <div class="col-md-6 mt-30 mt-md-0">
                            <div class="pr30 pl40">
                              <h3 class="ib mn mr20">'._PANEL_STATS_USERS.'</h3>
                              <div class="mt-10 mt-sm-0 pull-sm-right">
                                <div class="btn-group">
                                  <button type="button" data-toggle="dropdown" class="btn btn-xs btn-info dropdown-toggle">'._PANEL_STATS_FILTER.'<span class="caret ml15"></span></button>
                                  <ul role="menu" class="dropdown-menu">
                                    <li><a id="ecommerce_chart2-new-data" href="#" class="filter-range bg-whitesmoke">Type 1</a></li>
                                    <li><a id="ecommerce_chart2-new-data-2" href="#" class="filter-range">Type 2</a></li>
                                    <li><a id="ecommerce_chart2-new-data-3" href="#" class="filter-range">Type 3</a></li>
                                  </ul>
                                </div>
                              </div>
                              <div id="ecommerce_chart2" style="height: 380px;" class="pt20"></div>                             
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="mt37">
              <div class="row text-center text-sm-left">
                <div class="col-lg-6 admin-grid">
                  <div class="pl30 pr30">
                    <h3 class="mtn">'._MAIN_LAST_COMM.'</h3>				
						<div id="last_comm">
                             <div class="panel-heading" >'._AJAX_LOAD.'</div>
								<script type="text/javascript">ajaxGet(\'' . ADMIN . '/ajax/last_comm\', \'last_comm\');</script>	
						</div> 				 
                  </div>
                </div>
                <div class="p-md-11 col-lg-6 admin-grid mt-60 mt-lg-0">
                  <div class="pl30 pr30">
                    <h3 class="mtn">'._MAIN_LAST_USER.'</h3>
						<div id="last_user">
                             <div class="panel-heading" >'._AJAX_LOAD.'</div>
								<script type="text/javascript">ajaxGet(\'' . ADMIN . '/ajax/last_user\', \'last_user\');</script>	
							</div>   
						</div>
					</div>
				</div>
              <hr>              
              <div class="row">
                <!-- Three Pane Widget-->
                <div class="col-md-12 admin-grid">
                  <div class="panel">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="pl30 pr50">
                            <h3 class="ib mn mr20">'._PANEL_STATS_SERVER_STATS.':</h3>
                            <div class="pull-right">
                              <div class="btn-group">';
							   $notif = "notif(\'primary\', \'"._AJAX_INFO."\', \'"._AJAX_COMPL."\');";
                               echo '<button onclick="ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init(); '.$notif.'\', \'server_stats\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>'._AJAX_RELOAD.'</button>
                              </div>
                            </div>
                            <div id="server_stats" class="mt50 row text-center">
                             <div class="panel-heading" >'._AJAX_LOAD.'</div>
								<script type="text/javascript">ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init();\', \'server_stats\');</script>	
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mt-30 mt-md-0">
                          <div class="pl30 pr30">
                            <div>
                              <h3 class="ib mn mr20">'._PANEL_STATS_SEO.':</h3>
                              <div class="pull-right">
                                <div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>'._AJAX_RELOAD.'</button>
                                </div>
                              </div>
                            </div>
                            <div class="row mt60 text-center">
                              <div class="col-xs-12 col-sm-6 col-md-4 mt-30 mt-sm-0">
                                <div id="ecommerce_chart3" style="height: 120px;"></div>
                                <p class="fs15 text-shady-lady mt10">Яндекс ТИЦ</p>
                              </div>
							   <div class="col-xs-12 col-sm-6 col-md-4 mt-30 mt-sm-0">
                                <div id="ecommerce_chart2" style="height: 120px;"></div>
                                <p class="fs15 text-shady-lady mt10">Google PR</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="mt10">
              <div class="row">
                <!-- Three Pane Widget-->
                <div class="col-md-12 admin-grid">
                  <div class="panel mtn">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-6 mt-30 mt-md-0">
                          <div class="pl30 pr30">
                            <h2 class="ib mn mr20">Региональная статистика</h2>
                            <div class="clearfix mt30">
                              <div class="pull-left">
                                <div class="btn-group btn-group-xs d-sm-flex"><a href="#" class="btn btn-info">Пользователи</a><a href="#" class="btn btn-gray">
                                    Посетители
                                    </a></div>
                              </div>
                             
                            </div>
                            <div id="WidgetMap" style="width: 100%; height: 300px;" class="mt40 jvector-colors hide-jzoom"></div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="pl30 pr30">
                            <h2 class="ib mn mr20">Лента событий</h2>
                            <div class="panel-scroller scroller-lg scroller-thick scroller-blue scroller-pn pn mt30">
                              <table class="table list-table">
                                <tbody>
                                  <tr>
                                    <td><span class="icon text-warning fa fa-bell"></span>Поступила публикация нового материала от пользователя zzverr.</td>
                                    <td class="text-right">	сейчас</td>
                                  </tr>
								  <tr>
                                    <td><span class="icon text-warning fa fa-bell"></span>Поступила новый комметарий от пользователя zzverr.</td>
                                    <td class="text-right">	сейчас</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">сейчас</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">18 сен, 09:31</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">	03 сен, 08:54</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">	01 сен, 13:49</td>
                                  </tr>
                                   <tr>
                                    <td><span class="icon text-warning fa fa-bell"></span>Поступила публикация нового материала от пользователя zzverr.</td>
                                    <td class="text-right">	01 сен, 10:05</td>
                                  </tr>
                                   <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">	01 сен, 08:59</td>
                                  </tr>
                                   <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>Вход admin в панель управления.</td>
                                    <td class="text-right">	29 авг, 08:59</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>New order received. Please take care of it.</td>
                                    <td class="text-right">2 hours</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>New order received. Please take care of it.</td>
                                    <td class="text-right">2 hours</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>New order received. Please take care of it.</td>
                                    <td class="text-right">2 hours</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>New order received. Please take care of it.</td>
                                    <td class="text-right">2 hours</td>
                                  </tr>
                                  <tr>
                                    <td><span class="icon text-info fa fa-bullhorn"></span>New order received. Please take care of it.</td>
                                    <td class="text-right">2 hours</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="mt25">              
            </div>
          </div>
          <!-- begin: .tray-right-->
          <aside data-tray-height="match" class="tray tray-right tray270 pn hidden">
            <!-- store activity timeline-->
            <ol class="timeline-list pl5 mt5">
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Michael</b> Added a new item to his store:<a href="#">Ipod</a></div>
                <div class="timeline-date">1:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Sara</b> Added a new item to his store:<a href="#">Notebook</a></div>
                <div class="timeline-date">3:05am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-success"><span class="fa fa-usd"></span></div>
                <div class="timeline-desc"><b>Admin</b> created a new invoice for:<a href="#">Software</a></div>
                <div class="timeline-date">4:15am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-warning"><span class="fa fa-pencil"></span></div>
                <div class="timeline-desc"><b>Laura</b> edited her work experience</div>
                <div class="timeline-date">5:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-success"><span class="fa fa-usd"></span></div>
                <div class="timeline-desc"><b>Admin</b> created a new invoice for:<a href="#">Apple Inc.</a></div>
                <div class="timeline-date">7:45am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Michael</b> Added a new item to his store:<a href="#">Ipod</a></div>
                <div class="timeline-date">8:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Sara</b> Added a new item to his store:<a href="#">Watch</a></div>
                <div class="timeline-date">9:35am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-system"><span class="fa fa-fire"></span></div>
                <div class="timeline-desc"><b>Admin</b> created a new invoice for:<a href="#">Software</a></div>
                <div class="timeline-date">4:15am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-warning"><span class="fa fa-pencil"></span></div>
                <div class="timeline-desc"><b>Laura</b> edited her work experience</div>
                <div class="timeline-date">5:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-success"><span class="fa fa-usd"></span></div>
                <div class="timeline-desc"><b>Admin</b> created a new invoice for:<a href="#">Software</a></div>
                <div class="timeline-date">4:15am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-warning"><span class="fa fa-pencil"></span></div>
                <div class="timeline-desc"><b>Laura</b> edited her work experience</div>
                <div class="timeline-date">5:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-success"><span class="fa fa-usd"></span></div>
                <div class="timeline-desc"><b>Admin</b> created a new invoice for:<a href="#">Apple Inc.</a></div>
                <div class="timeline-date">7:45am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Michael</b> Added a new item to his store:<a href="#">Ipod</a></div>
                <div class="timeline-date">8:25am</div>
              </li>
              <li class="timeline-item">
                <div class="timeline-icon bg-dark light"><span class="fa fa-tags"></span></div>
                <div class="timeline-desc"><b>Sara</b> Added a new item to his store:<a href="#">Watch</a></div>
                <div class="timeline-date">9:3</div>
              </li>
            </ol>
          </aside>
	 </section>
	
	
	';	
	foreach(glob(ROOT.'usr/modules/*/admin/moderation.php') as $listed) require_once($listed);
	/*
	unset($component_array);
	if ($config['dbCache']== 1 OR $config['cache'] == 1) 
	{
		echo '<div class="row"><div class="col-lg-12"><section class="panel"><div class="panel-heading no-border"><b>' . _MAIN_CACHE . '</b></div><div class="panel-body"><div class="switcher-content"><p>' . _MAIN_CACHE_INFO . '<br><div style="float:right"><small>[ <a href="index.php?url='.ADMIN.'/do/clearCache">'. _MAIN_CLEARCACHE .'</a> ]</small></div></p></div></div></section></div></div>';
	}		
	echo '<div class="row">
		<div class="col-lg-12">
			<section>
			<ul id="myTab" class="nav nav-tabs">				
				<li class="pull-right">
					<a href="#profile2" data-toggle="tab">'._MAIN_LAST_COMM.'</a>
				</li>
				<li class="active pull-right">
					<a href="#home2" data-toggle="tab">'._MAIN_LAST_USER.'</a>
				</li>
			</ul>
			<section class="panel">
				<div class="panel-body no-padding">
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane active" id="home2">';	
	$query = $db->query("SELECT c.*, u.nick, u.group, u.last_visit FROM ".DB_PREFIX."_comments as c LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u on (c.uid=u.id) ORDER BY date DESC LIMIT 5");
	if($db->numRows($query) > 0) 
	{
	echo '<div class="panel-heading">'._MAIN_LAST_USER.'</div>
							<table class="table table-striped no-margin">
								<thead>
									<tr>
										<th><span class="pd-l-sm"></span>ID</th>
										<th class="col-md-4">' . _COMMENT . '</th>
										<th class="col-md-1">' . _MODULE . '</th>
										<th class="col-md-2">' . _DATE . '</th>
										<th class="col-md-2">' . _USER . '</th>
										<th class="col-md-1">' . _LINKS . '</th>
										<th class="col-md-4">' . _ACTIONS . '</th>
									</tr>
								</thead>
								<tbody>';
		while($commment = $db->getRow($query)) 
		{
			$tt = str(htmlspecialchars(strip_tags($commment['text'])), 30);
			$active = ($commment['status'] == 1) ? '<a href="javascript:void(0)" onclick="adminCommentStatus(\'' . $commment['id'] . '\', 0);" title="' . _DEACTIVATE . '"><button type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DEACTIVATE . '">D</button></a>' : '<a href="javascript:void(0)" onclick="adminCommentStatus(\'' . $commment['id'] . '\', 1);" title="' . _ACTIVATE . '"><button type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _ACTIVATE . '">A</button></a>';
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $commment['id'] . '</td>
				<td>' . (($tt != '') ? $tt : '<font color="red">'._NO_TEXT.'</font>') . '</td>
				<td>' . commentLink($commment['module'], $commment['post_id']) . '</td>
				<td>' . formatDate($commment['date'], true) . '</td>
				<td>' . (($commment['uid'] != 0) ? '<a href="profile' . $commment['nick'] . '" title="' . $commment['nick'] . '">' . $commment['nick'] . '</a>' : $commment['gname']) . '</td>
				<td>' . (eregStrt('href', $commment['text']) || eregStrt('\[url', $commment['text']) ? "<font color=\"red\">"._YES."</font>" : "<font color=\"green\">"._NO."</font>") . '</td>
				<td>
				'.$active.'
				<button onclick="location.href=\'{ADMIN}/comments/edit/'.$commment['id'].'\';" type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._EDIT.'">E</button>
				<button onclick="location.href=\'{ADMIN}/comments/delete/'.$commment['id'].'\';" type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._DELETE.'">X</button>				
				</td>
				</tr>
				';
				
		}
		echo '</tbody></table>';
	} 
	else 
	{
		echo '<div class="panel-heading">'._MAIN_EMPTY_COMM.'</div>';
	}
	echo '</div><div class="tab-pane" id="profile2">';		
	$query = $db->query('SELECT u.*, g.name FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` as u LEFT JOIN `' . USER_DB . '`.`' . USER_PREFIX . '_groups` as g on(u.group = g.id) ORDER BY regdate DESC LIMIT 5');
	if($db->numRows($query) > 0) 
	{
	echo '<div class="panel-heading">'._MAIN_LAST_COMM.'</div>
							<table class="table table-striped no-margin">
								<thead>
									<tr>
										<th><span class="pd-l-sm"></span>ID</th>
										<th class="col-md-2">' . _NICK . '</th>
										<th class="col-md-2">' . _GROUP . '</th>
										<th class="col-md-3">' . _REGDATE . '</th>
										<th class="col-md-3">' . _LASTDATE . '</th>										
										<th class="col-md-4">' . _ACTIONS . '</th>
									</tr>
								</thead>
								<tbody>';
		while($user = $db->getRow($query)) 
		{
			echo '			
			<tr>
				<td><span class="pd-l-sm"></span>' . $user['id'] . '</td>
				<td> <a href="profile/' . $user['nick'] . '">' . $user['nick'] .'</a></td>
				<td>' . $user['name'] . '</td>
				<td>' . formatDate($user['regdate'], true) . '</td>
				<td>' . formatDate($user['last_visit']) . '</td>
				<td>
				<a href="{ADMIN}/user/edit/'.$user['id'].'">
				<button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._EDIT.'">E</button>
				</a>
				<a href="{ADMIN}/user/ban/'.$user['id'].'" onclick="return getConfirm(\'Забанить пользователя?\')"><button type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._BAN.'">B</button></a>				
				<a href="{ADMIN}//user/delete/'.$user['id'].'" onclick="return getConfirm(\'Удалить пользователя?\')">
				<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._DELETE.'">X</button>
				</a>
				</td>
				</tr>';
		}
	echo "</tbody></table>";
	} 
	echo "</div></div></div></section></section></div></div>";
	echo '<div class="row">
			<div class="col-lg-8">			
				<section>
					<ul id="myTab" class="nav nav-tabs">
						<li class="pull-right">
							<a href="#false2" data-toggle="tab">' . _FALSELOG . '</a>
						</li>
						<li class="active pull-right">
							<a href="#log2" data-toggle="tab">'. _LASTLOG .'</a>
						</li>
					</ul>
				<section class="panel">
					<div class="panel-body no-padding">
						<div id="myTabContent" class="tab-content">
							<div class="tab-pane active" id="log2">';
	if(isset($logs[1]))
	{
		$log1 = 0;
		echo '<div class="panel-heading">'. _LASTLOG .'</div>
							<table class="table table-striped no-margin">
								<thead>
									<tr>										
										<th class="col-md-4"><span class="pd-l-sm"></span>' . _NICK . '</th>
										<th class="col-md-2">' . _GROUP . '</th>
										<th class="col-md-2">' . _REGDATE . '</th>										
									</tr>
								</thead>
								<tbody>';
		foreach($logs[1] as $uid => $arr) 
		{
			foreach($arr as $time => $info) 
			{
				$log1++;
				if($log1 <= 7) 
				{			
					$log_true = explode('-', $info);
					echo '
					<tr>		
						<td><span class="pd-l-sm"></span>' . $log_true[1] .'</td>
						<td>' . $log_true[0] . '</td>
						<td>' . formatDate($time, true) . '</td>
					</tr>';
				}
			}
			
			
		}
			echo '</tbody></table>';
			echo '<br><div align="right"><a href="/index.php?url={ADMIN}/logs/clear" class="btn btn-warning btn-xs">' . _CLEAN . '</a><span class="pd-l-sm"></span></div><br>';
	} 
	else 
	{
		echo '<div class="panel-heading">Информация отсутствует.</div>';
	}	
	echo '</div><div class="tab-pane" id="false2">';   
   
   if(isset($logs[2])) 
   {
		$log2 = 0;
		echo '<div class="panel-heading">' . _FALSELOG . '/div>
							<table class="table table-striped no-margin">
								<thead>
									<tr>										
										<th class="col-md-2"><span class="pd-l-sm"></span>' . _NICK . '</th>
										<th class="col-md-2">' . _GROUP . '</th>
										<th class="col-md-2">' . _REGDATE . '</th>										
									</tr>
								</thead>
								<tbody>';
		foreach($logs[2] as $uid => $arr) 
		{
			foreach($arr as $time => $info)
			{
				$log2++;
				if($log2 <= 7)
				{			
					$log_true = explode('-', $info);
					echo '
					<tr>			
						<td><span class="pd-l-sm"></span>' . $log_true[1] .'</td>
						<td>' . $log_true[0] . '</td>
						<td>' . formatDate($time, true) . '</td>
					</tr>';
				}
			}
			
			
		}
			echo "</tbody></table>";
			echo '<br><div align="right"><a href="/index.php?url={ADMIN}/logs/clear" class="btn btn-warning btn-xs">' . _CLEAN . '</a><span class="pd-l-sm"></span></div><br>';
	} 
	else 
	{	
		echo '<div class="panel-heading">Информация отсутствует.</div>';
	}	
	echo '</div></div></div></section></section></div>';
	list($weekUsrs) = $db->fetchRow($db->query("SELECT Count(id) FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE regdate > '" . (time()-604900) . "'"));
	list($weekComm) = $db->fetchRow($db->query("SELECT Count(id) FROM " . DB_PREFIX . "_comments WHERE date > '" . (time()-604900) . "'"));
	echo '<div class="col-lg-4">
			<section class="panel">
				<div class="panel-heading no-border">
					<b>Статистика</b>
				</div>
				<div class="panel-body">
					<div class="switcher-content">
						<p>
							<b>Комментариев на этой неделе:</b> ' . $weekComm . '<br>
							<b>Пользователей на этой неделе:</b> ' . $weekUsrs . '
						</p>
					</div>
				</div>
			</section>';
	echo "</div>";	
	echo "</div></div>";*/
	$adminTpl->admin_foot($last_visit, $last_ip);
	
}
function init_login() 
{
global $adminTpl, $admin_conf, $core;
	if($core->auth->isUser && $core->auth->isAdmin)
	{
		if(isset($_SESSION[SESS_AUTH]) && $_SESSION[SESS_AUTH] == 'ok' OR $admin_conf['sessions'] == 0)
		{
			return false;
		} 
		else 
		{
			return true;
		}
	}
	else
	{
		return true;
	}
}

function login() 
{
global $adminTpl, $core, $config, $db, $admin_conf;
	require ROOT . 'etc/social.config.php';			
	if ($social['admin'] != '0')
	{
		$s_list = '<br><center>'.social_list().'</center>';
	}
	else
	{
		$s_list = '';
	}
	$adminTpl->sep = '';
	if(isset($_POST['nick']))
	{
		$nick = filter($_POST['nick'], 'nick');
		$password = md5(md5($_POST['password']));
		if(!empty($nick) && !empty($_POST['password']))
		{
			$access = $db->getRow($db->query("SELECT id, password, tail FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE `nick` = '" . $db->safesql($nick) . "' AND `group`='1'"));
			$no_head = true;
			
			if (md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']) == $access['password']) 
			{
				if($core->auth->isUser && $core->auth->isAdmin)
				{
					$_SESSION[SESS_AUTH] = 'ok';
				}
				else
				{
					$_SESSION[SESS_AUTH] = 'ok';
					$newHash = md5(@$_SERVER['HTTP_USER_AGENT'].$config['uniqKey']);
					setcookie(COOKIE_AUTH, engine_encode(serialize(array('id' => $access['id'], 'nick' => $nick, 'password' => md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']), 'hash' => $newHash))), time() + COOKIE_TIME, '/');
				}
				
				if(isset($_SESSION[SESS_AUTH])) {
					$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace('[nick]', $nick, _GOOD_LOGIN) . "', '1')");
					if(eregStrt(ADMIN, $_SERVER['HTTP_REFERER']))
					{
						location($_SERVER['HTTP_REFERER']);
					}
					else
					{
						location(ADMIN);
					}
				}
			}
			else
			{
				if (!isset($_SESSION[SESS_COUNT])) 
				{
					$_SESSION[SESS_COUNT] = 0;
				}
				
				$counter = $_SESSION[SESS_COUNT]++;
				$turns = 5-$counter;
				$adminTpl->loadFile('login');
				
				if($counter == 3) 
				{
					$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace(array('[nick]', '[pass]'), array($nick, str($_POST['password'], 4)), _BAD_LOGIN) . "', '2')");
				}
				
				if($turns <= 0) 
				{
					$adminTpl->setVar('STOP', '<div id="stop">' . _NO_TURNS . '</div>');
				} 
				else 
				{
					$adminTpl->setVar('STOP', '<div id="stop">' . str_replace('{turns}', $turns, _FALSE_TURN) . '</div>');
				}
				
				$stop='';
			}
		}
		else
		{
			$stop='<div id="stop">' . _EMPTY_LOGIN . '</div>';
		}
	}
	else
	{
		$stop= '';
		
	}
	$adminTpl->loadFile('login');
	$adminTpl->setVar('STOP', $stop);
	$adminTpl->setVar('URL', $config['url']);
	$adminTpl->setVar('ADM_THEME', 'usr/tpl/admin');			
	$adminTpl->setVar('SOCIAL', $s_list);	
	$adminTpl->setVar('LICENSE', 'Powered by <a href="http://jmy.su" target="_blank" title="JMY CORE">JMY CORE</a>');	
	$adminTpl->end();
	
}

if(init_login()) 
{
	login();
} 
else 
{
	require ROOT . 'root/list.php';	
	switch(isset($url[1]) ? $url[1] : null) {
		default:
			if(isset($url[1]))
			{
				if(isset($component_array[$url[1]]) OR isset($services_array[$url[1]]))
				{
					if(checkAdmControl($url[1]))
					{
						require ROOT . 'root/modules/' . $url[1] . '.admin.php';
					}
					else
					{
						noadmAccess();
					}
				}
				else
				{
					if(checkAdmControl('index'))
					{
						admin_main();
					}
					else
					{
						noadmAccess();
					}
				}
			}
			else
			{
				if(checkAdmControl('index'))
				{
					admin_main();
				}
				else
				{
					noadmAccess();
				}
			}
		break;
		
		case 'do':
			$switch = filter($url[2]);
			switch($switch) {
				case 'logout':
					$_SESSION[SESS_AUTH] = null;
					$_SESSION[SESS_COUNT] = 0;
					$core->auth->logout();
					header('Location: /');
					break;
				
				case 'tic':
					echo yandex_tic($_SERVER['HTTP_HOST']);
					break;
				
				case 'pr':
					echo getPageRank($_SERVER['HTTP_HOST']);
					break;					
					
				case 'clearCache':
					if(checkAdmControl('index'))
					{
						ajaxInit();
						full_rmdir(ROOT . 'tmp/mysql');
						full_rmdir(ROOT . 'tmp/cache');
						@mkdir(ROOT . 'tmp/mysql', 0777);
						@mkdir(ROOT . 'tmp/cache', 0777);
						echo _CACHE_CLEANED;
						header('Location: /' . ADMIN);
					}
					break;
				
				
			}
		break;
		
		case 'module':
			define('ADMIN_SWITCH', true);
			$mod = $url[2];
			if(file_exists(ROOT . 'usr/modules/' . $mod . '/admin/index.php')) 
			{
				if($core->checkModule($mod)=='1') 
				{
					if(checkAdmControl($mod))
					{
						require ROOT . 'usr/modules/' . $mod . '/admin/index.php';
					}
					else
					{
						noadmAccess();
					}					
				}
				else
				{
					nomodActive();
				}				
			} 
			else 
			{
				header('Location: /' . ADMIN);
			}
			break;
		
		case 'logs':
		global $adminTpl,  $db;
			ajaxInit();
			$type = $url[2];
			$num = isset($url[3]) ? intval($url[3]) : '';
			
			switch($type) 
			{
				case "clear":
					$db->query("TRUNCATE TABLE " . DB_PREFIX . "_logs");
					echo _TABLECLEANED;
					header('Location: /' . ADMIN);
					break;
			}
			break;
			
		case 'ajax':
		global $adminTpl,  $db;
			ajaxInit();
			$type = $url[2];
			$num = isset($url[3]) ? intval($url[3]) : '';
			
			switch($type) 
			{
				case "server_stats":
					$free = disk_free_space('/');
					$full = disk_total_space('/');
					$space = round(($full-$free)/($full/100));
					echo '	<div class="col-sm-4 mt-30 mt-sm-0">
                              <div id="c1" value="'.$space.'" data-circle-color="system" class="info-circle info-circle-percent"></div>
                                <p class="fs15 text-shady-lady mt10">'._PANEL_STATS_SERVER_SPACE.'</p>
                              </div>
                              <div class="col-sm-4 mt-30 mt-sm-0">
                                <div id="c2" value="'.getServerCPULoad().'" data-circle-color="success" class="info-circle info-circle-percent"></div>
                                <p class="fs15 text-shady-lady mt10">'._PANEL_STATS_SERVER_CPU.'</p>
                              </div>
                              <div class="col-sm-4 mt-30 mt-sm-0">
                                <div id="c3" value="'.getServerRAM().'" data-circle-color="danger" class="info-circle info-circle-percent"></div>
                                <p class="fs15 text-shady-lady mt10">'._PANEL_STATS_SERVER_RAM.'</p>
                              </div>';
				break;
				
				case "last_comm":
				$query = $db->query("SELECT c.*, u.nick, u.group, u.last_visit FROM ".DB_PREFIX."_comments as c LEFT JOIN `" . USER_DB . "`.`" . USER_PREFIX . "_users` as u on (c.uid=u.id) ORDER BY date DESC LIMIT 5");
					if($db->numRows($query) > 0) 
					{
							echo "
<script type=\"text/javascript\">
							// Form Skin Switcher
    
		</script>
		";
						while($commment = $db->getRow($query)) 
						{
							$tt = str(htmlspecialchars(strip_tags($commment['text'])), 30);
							$active = ($commment['status'] == 1) ? '<a href="javascript:void(0)" onclick="adminCommentStatus(\'' . $commment['id'] . '\', 0);" title="' . _DEACTIVATE . '"><button type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DEACTIVATE . '">D</button></a>' : '<a href="javascript:void(0)" onclick="adminCommentStatus(\'' . $commment['id'] . '\', 1);" title="' . _ACTIVATE . '"><button type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _ACTIVATE . '">A</button></a>';
							echo '
							<div class="box-sm box-offset-20 mt-30">
                      <div class="box__left"><img height="60" width="60" src="'.avatar($commment['uid']).'" class="br4"></div>
                      <div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">
                        <div class="fs18 ib text-black"><a href="profile' . $commment['nick'] . '" title="' . $commment['nick'] . '">' . $commment['nick'] . '</a></div>
                        <div class="float-sm-right text-mischka text-bold monserrat fs12 mt7">' . formatDate($commment['date'], true) . '</div>
                        <p class="mt7">' . (($tt != '') ? $tt : '<font color="red">'._NO_TEXT.'</font>') . '</p>
                      </div>
                    </div>
                    <div class="mt15 clearfix">
                      <div class="float-right list-inline">
                        <li><a onclick="modal_o(\'#modal-form\')" class="text-primary text-uppercase fs12 text-bold monserrat">Быстрое редактирование</a></li>
                        <li><a href="#" class="text-primary text-uppercase fs12 text-bold monserrat">Просмотреть</a></li>
                        <li><a href="#" class="text-danger text-uppercase fs12 text-bold monserrat">Удалить</a></li>
                      </div>
					 

					  
				<div id="modal-form" class="popup-basic admin-form mfp-with-anim mfp-hide">
            <div class="panel">
              <div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Leave a comment</span></div>
              <form id="comment" method="post" action="/">
                <div class="panel-body p25">
                  <div class="section row">
                    <div class="col-md-6">
                      <label for="firstname" class="field prepend-icon">
                        <input id="firstname" type="text" name="firstname" placeholder="First name..." class="gui-input">
                        <label for="firstname" class="field-icon"><i class="fa fa-user"></i></label>
                      </label>
                    </div>
                    <div class="col-md-6">
                     <label for="datetimepicker1" class="col-md-3 control-label">Default Field</label>
                      <div class="col-md-8">
                        <input id="datetimepicker1" type="text" class="form-control">
                      </div>

                    </div>
                  </div>
                  
                  <div class="section">
                    <label for="comment" class="field prepend-icon">
                      <textarea id="comment" name="comment" placeholder="Your comment" class="gui-textarea"></textarea>
                      <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer"><strong>Hey You:</strong> We expect a great comment...</span>
                    </label>
                  </div>
                </div>
                <div class="panel-footer">
                  <button type="submit" class="button btn-primary">Post Comment</button>
                </div>
              </form>
            </div>
          </div>
	  
					  
					  
					  
					  
					  
                    </div>';
							/*
							<tr>
								<td><span class="pd-l-sm"></span>' . $commment['id'] . '</td>
								<td></td>
								<td>' . commentLink($commment['module'], $commment['post_id']) . '</td>
								<td>' . formatDate($commment['date'], true) . '</td>
								<td>' . (($commment['uid'] != 0) ? '<a href="profile' . $commment['nick'] . '" title="' . $commment['nick'] . '">' . $commment['nick'] . '</a>' : $commment['gname']) . '</td>
								<td>' . (eregStrt('href', $commment['text']) || eregStrt('\[url', $commment['text']) ? "<font color=\"red\">"._YES."</font>" : "<font color=\"green\">"._NO."</font>") . '</td>
								<td>
								'.$active.'
								<button onclick="location.href=\'{ADMIN}/comments/edit/'.$commment['id'].'\';" type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._EDIT.'">E</button>
								<button onclick="location.href=\'{ADMIN}/comments/delete/'.$commment['id'].'\';" type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="'._DELETE.'">X</button>				
								</td>
								</tr>*/
								
						}
						echo '</tbody></table>';
					} 
					else 
					{
						echo '<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">'._MAIN_EMPTY_COMM.'</div>';
					}
				break;
			
			
				case 'last_user':		
					if ($core->auth->isAdmin)
					{						
						$query = $db->query('SELECT u.*, g.name FROM `' . USER_DB . '`.`' . USER_PREFIX . '_users` as u LEFT JOIN `' . USER_DB . '`.`' . USER_PREFIX . '_groups` as g on(u.group = g.id) ORDER BY regdate DESC LIMIT 5');
							if($db->numRows($query) > 0) 
							{				
								while($user = $db->getRow($query)) 
								{
									echo '
									<div class="mt-60 mt-sm-30 d-sm-flex">
										<div><img height="60" width="60" src="'.avatar($user['id']).'" class="br4"></div>
										<div class="pl30 pr30">
											<div>
											  <div>
												<div class="fs18 ib text-black">' . $user['nick'] .'</div>
												<p class="mt7">'.(empty($user['signature']) ? _MAIN_EMPTY_SIGN : $user['signature']).'</p>
											  </div>
											</div>
										</div>
										<div class="text-mischka text-uppercase text-bold monserrat fs12 d-sm-flex flex-direction-column mla min-w145">
											<div><span class="bull bg-'.($core->isOnline($user['id']) ? 'success' : 'info') .'"></span><span>' . formatDate($user['last_visit']) . '</span></div>
											<div class="btn-group btn-group-xs mt15 d-sm-flex">
												<a href="{ADMIN}/user/edit/'.$user['id'].'" class="btn btn-info mw70">'._EDIT_SHORT.'</a>
												<a href="{ADMIN}/user/delete/'.$user['id'].'" class="btn btn-danger mw70">'._DELETE.'</a>
											</div>
										</div>
									</div>';
								}
							}
					}
					else
					{
						header('Location: /' . ADMIN);
					}
				break;
			
				case 'last_comm':
					global $adminTpl, $db, $config;
					ajaxInit();
					if ($core->auth->isAdmin)
					{
						$type = $url[2];
						echo $type; 
					}
					else
					{
						header('Location: /' . ADMIN);
					}
				break;
			
				case 'hide_admin':
					global $adminTpl, $db, $config;
					ajaxInit();
					if ($core->auth->isAdmin)
					{
						$type = $url[2];
						echo $type; 
					}
					else
					{
						header('Location: /' . ADMIN);
					}
				break;
				
				case 'addition':
					$type = $url[2];
					switch($type) 
					{
						case "tic":
							echo yandex_tic('http://'.$_SERVER['HTTP_HOST']);
							break;
					}
				break;	
			}
				break;
	}
}