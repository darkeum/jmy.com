<!DOCTYPE html>
<html lang="{LANG}" xml:lang="{LANG}">
  <head>
	{META}
	<meta name="theme-color" content="#535a6c">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="icon" href="/{ADM_THEME}/assets/images/favicon.ico" type="image/x-icon">
    <!-- Stylesheets-->
	<link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/admin-forms.css">
	<link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/summernote.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/bootstrap-editable.css">
	<link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/bootstrap-switch.min.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/address.css">
	<link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/magnific-popup.css">	
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/typeahead.js-bootstrap.css">    
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/theme.css">
	<link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/tagmanager.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/core.css">
	<!-- Fonts-->
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700">
	<!--[if lt IE 10]>
		<div style="background: #212121; padding: 10px 0; box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3); clear: both; text-align:center; position: relative; z-index:1;"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="/{ADM_THEME}/assets/images/ie8-panel/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
		<script src="/{ADM_THEME}/assets/js/html5shiv.min.js"></script>
	<![endif]-->
	<!-- Core scripts-->
    <script src="/{ADM_THEME}/assets/js/core.min.js"></script>
    <!-- Theme Javascript-->
    <script src="/{ADM_THEME}/assets/js/utility.js"></script>
    <script src="/{ADM_THEME}/assets/js/demo.js"></script>		
    <script src="/{ADM_THEME}/assets/js/main.js"></script>
	<script src="/{ADM_THEME}/assets/js/widgets.js"></script>
	<script src="/{ADM_THEME}/assets/js/script.js"></script>
	<script src="/{ADM_THEME}/assets/js/bootstrap-switch.min.js"></script>	
  </head>  
  <body data-spy="scroll" data-target="#nav-spy" data-offset="300" class="form-editors-page {BODY_CLASS}"> 
    <div id="main">    
      <header class="navbar navbar-fixed-top bg-success">
        <div class="navbar-branding dark bg-success">
			<a href="/{ADMIN}" class="navbar-brand text-uppercase"><i class="fa fa-check-circle mg-r-xs"></i> JMY<b>CMS</b></a>
			<span id="toggle_sidemenu_l" class="fa fa-bars"></span>
		</div>
        <ul class="nav navbar-nav navbar-left"></ul>
        <form role="search" action="/{ADMIN}/search" class="navbar-form navbar-left navbar-search alt">
			<span class="hide visible-md-inline-block visible-lg-inline-block fa fa-search fs18"></span>
			<div class="form-group">
				<input type="text"  name="search" placeholder="[alang_old:_PANEL_SEARCH]" class="form-control">
			</div>
        </form>
        <ul class="nav navbar-nav navbar-right">
		  <li>
            <div class="navbar-btn btn-group">
              <a href="{URL}" class="btn btn-sm" title="Перейти на сайт"><span class="fa fa-globe fs15"></span></a>
            </div>
          </li>		
		  <li>
            <div class="navbar-btn btn-group">
              <button class="btn btn-sm request-fullscreen" title="Во весь экран"><span class="fa fa-arrows-alt fs15"></span></button>
            </div>
          </li>
		  <li>
            <div class="navbar-btn btn-group">
				<a href="{URL_FULL}#" class="topbar-menu-toggle btn btn-sm"><span class="fa fa-magic"></span></a>
			</div>
          </li>
		  <li class="dropdown menu-merge mr20">
            <div class="navbar-btn btn-group">
				<button data-toggle="dropdown" class="btn btn-sm dropdown-toggle">
					<span class="fa fa-bell-o fs14 va-m"></span>
					<span class="badge badge-danger">{NOTIF_NUMB}</span>
				</button>
				<div role="menu" class="dropdown-menu dropdown-persist w350 animated animated-shorter fadeIn">
					<div class="panel mbn">
					  <div class="panel-menu"><span class="panel-icon"><i class="fa fa-clock-o"></i></span><span class="panel-title fw600"> [alang_old:_PANEL_SUNMENU_NOTIF]</span></div>
					  <div class="w100p panel-body panel-scroller scroller-navbar scroller-overlay scroller-pn pn">
						<ol class="timeline-list">
						  {NOTIF}
						</ol>
					  </div>
					  <div class="panel-footer text-center p7"><a href="#" class="link-unstyled"> Смотреть Все</a></div>
					</div>
				</div>
			</div>
          </li>		
          <li><img width="70" height="70" src="{AVATAR}" alt="avatar"></li>
          <li class="dropdown menu-merge">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle fw600 p15">
				<span class="fa fa-angle-down"></span>
			</a>
            <ul role="menu" class="dropdown-menu list-group dropdown-persist w250">
				<li class="dropdown-header clearfix">
					<div class="pull-left ml10">
						<select id="user-status">
							<optgroup label="[alang_old:_PANEL_SUNMENU_STATUS]">
								<option value="3" {HIDE_STATUS_3}>[alang_old:_PANEL_SUNMENU_ALWAYS]</option>
								<option value="2" {HIDE_STATUS_2}>[alang_old:_OFFLINE]</option>
								<option value="1" {HIDE_STATUS_1}>[alang_old:_ONLINE]</option>
							</optgroup>
						</select>
					</div>
					<div class="pull-right mr10">
						<div class="btn-group">
							<button type="button" class="multiselect dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" style="max-width: 100px;" aria-expanded="false">[alang_old:_PANEL_SUNMENU_VIEW] <b class="caret-right"></b></button></div>
					</div>
				</li>				
				<li class="list-group-item"><a href="/pm" class="animated animated-short fadeInUp"><span class="fa fa-envelope"></span> Сообщения<span class="label label-warning">{MESSAGES_NUMB}</span></a></li>
				<li class="list-group-item"><a href="/profile" class="animated animated-short fadeInUp"><span class="fa fa-user"></span> Друзья<span class="label label-warning">{FRIENDS_NUMB}</span></a></li>
				<li class="list-group-item"><a href="/profile" class="animated animated-short fadeInUp"><span class="fa fa-bell"></span> 	Профиль</a></li>
				<li class="list-group-item"><a href="/{ADMIN}/config" class="animated animated-short fadeInUp"><span class="fa fa-gear"></span> Настройки</a></li>
				<li class="dropdown-footer">
					<a href="/profile/logout"><span class="fa fa-power-off pr5"></span> [alang_old:_PANEL_SUNMENU_EXIT]</a>
				</li>
            </ul>
          </li>
        </ul>
      </header>
      <aside id="sidebar_left" class="nano nano-light affix has-scrollbar sidebar-light">
        <div class="sidebar-left-content nano-content">
          <header class="sidebar-header">
            <div class="sidebar-widget search-widget hidden">
              <div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input id="sidebar-search" type="text" placeholder="Search..." class="form-control"/>
              </div>
            </div>
          </header>
          <ul class="nav sidebar-menu">           
            <li {MENU_CHOOSE:main}> 
				<a href="/{ADMIN}"><span class="glyphicon glyphicon-home"></span><span class="sidebar-title">[alang_old:_PANEL_MENU_MAIN]</span></a>
            </li> 
			[ACTIVE_MODULE:news]
            <li>				
				<a href="#" class="accordion-toggle {MENU_OPEN:news,cats,fm,xfields}"><span class="glyphicon glyphicon-fire"></span><span class="sidebar-title">[alang_old:_PANEL_MENU_NEWS]</span><span class="caret"></span></a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:news}>
						<a href="/{ADMIN}/module/news">
							<span class="glyphicon glyphicon-modal-window"></span>[alang_old:_PANEL_MENU_NEWS_MANAGER]
						</a>
					</li>
					<li {MENU_CHOOSE:cats}>
						<a href="/{ADMIN}/cats">
							<span class="glyphicon glyphicon-list"></span>[alang_old:_PANEL_MENU_NEWS_CAT]
						</a>
					</li>
					<li {MENU_CHOOSE:xfields}>
						<a href="/{ADMIN}/xfields">
							<span class="glyphicon glyphicon-star"></span>[alang_old:_PANEL_MENU_NEWS_XFIELDS]
						</a>
					</li>					
				</ul>
            </li>
			[/ACTIVE_MODULE]		
            <li>
				<a href="#" class="accordion-toggle {MENU_OPEN:user,groups,comments,publications,voting}">
					<span class="glyphicon glyphicon-user"></span>
					<span class="sidebar-title">[alang_old:_PANEL_MENU_USER]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:user}>
						<a href="/{ADMIN}/user"><span class="glyphicon glyphicon-list-alt"></span>[alang_old:_PANEL_MENU_USER_MANAGER]</a>
					</li>
					<li {MENU_CHOOSE:groups}>
						<a href="/{ADMIN}/groups"><span class="glyphicon glyphicon-tag"></span>[alang_old:_PANEL_MENU_USER_GROUP]</a>
					</li>
					<li {MENU_CHOOSE:comments}>
						<a href="/{ADMIN}/comments"><span class="glyphicon glyphicon-comment"></span>[alang_old:_PANEL_MENU_USER_COMMENT]</a>
					</li>
					<li {MENU_CHOOSE:publications}>
						<a href="/{ADMIN}/publications"><span class="glyphicon glyphicon-edit"></span>[alang:moder]</a>
					</li>
					<li {MENU_CHOOSE:voting}>
						<a href="/{ADMIN}/voting"><span class="glyphicon glyphicon-plus-sign"></span>[alang_old:_PANEL_MENU_USER_POLL]</a>
					</li>                
				</ul>
            </li>
            <li>
				<a href="#" class="accordion-toggle {MENU_OPEN:board,blog,gallery,guestbook,content}">
					<span class="glyphicon glyphicon-th-large"></span>
					<span class="sidebar-title">[alang_old:_PANEL_MENU_COM]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					[ACTIVE_MODULE:board]
					<li {MENU_CHOOSE:board}>
						<a href="/{ADMIN}/module/board"><span class="fa fa-bullhorn"></span>[alang_old:_PANEL_MENU_COM_FORUM]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:blog]
					<li {MENU_CHOOSE:blog}>
						<a href="/{ADMIN}/module/blog"><span class="fa fa-stack-exchange"></span>[alang_old:_PANEL_MENU_COM_BLOG]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:gallery]
					<li {MENU_CHOOSE:gallery}>
						<a href="/{ADMIN}/module/gallery"><span class="fa fa-picture-o"></span>[alang_old:_PANEL_MENU_COM_GALLERY]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:guestbook]
					<li {MENU_CHOOSE:guestbook}>
						<a href="/{ADMIN}/module/guestbook"><span class="fa fa-comments-o"></span>[alang_old:_PANEL_MENU_COM_GUESTBOOK]</a>
					</li> 
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:content]
					<li {MENU_CHOOSE:content}>
						<a href="/{ADMIN}/module/content"><span class="fa fa-square-o"></span>[alang_old:_PANEL_MENU_COM_STATIC]</a>
					</li>  
					[/ACTIVE_MODULE]
              </ul>
            </li> 
			<li {MENU_CHOOSE:config}> 
				<a href="/{ADMIN}/config">
					<span class="glyphicon glyphicon-cog"></span>
					<span class="sidebar-title">[alang_old:_PANEL_MENU_CONFIG]  </span>
				</a>
            </li> 
			<li>
				<a href="#" class="accordion-toggle {MENU_OPEN:modules,blocks,templates}">
					<span class="glyphicon glyphicon-send"></span>
					<span class="sidebar-title">[alang_old:_PANEL_MENU_EXP]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:modules}>
						<a href="/{ADMIN}/modules"><span class="fa fa-cloud"></span>[alang_old:_PANEL_MENU_EXP_MODULES]</a>
					</li>
					<li {MENU_CHOOSE:blocks}>
						<a href="/{ADMIN}/blocks"><span class="fa fa-code"></span>[alang_old:_PANEL_MENU_EXP_BLOCKS]</a>
					</li>
					<li {MENU_CHOOSE:templates}>
						<a href="/{ADMIN}/templates"><span class="fa fa-pencil-square-o"></span>[alang_old:_PANEL_MENU_EXP_TPL]</a>
					</li>					
              </ul>
            </li>
			{MENU_MODULES}
			<li>
				<a href="#" class="accordion-toggle {MENU_OPEN:sitemap,smiles,db,log,update}">
					<span class="glyphicon glyphicon-question-sign"></span>
					<span class="sidebar-title">[alang_old:_PANEL_MENU_OTHER]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:sitemap}>
						<a href="/{ADMIN}/module/sitemap"><span class="glyphicon glyphicon-list-alt"></span>[alang_old:_PANEL_MENU_OTHER_MAP]</a>
					</li>
					<li {MENU_CHOOSE:smiles}>
						<a href="/{ADMIN}/smiles"><span class="fa fa-smile-o"></span>[alang_old:_PANEL_MENU_OTHER_SMILES]</a>
					</li>
					<li {MENU_CHOOSE:db}>
						<a href="/{ADMIN}/db"><span class="glyphicon glyphicon-tasks"></span>[alang_old:_PANEL_MENU_OTHER_BD]</a>
					</li>
					<li {MENU_CHOOSE:log}>
						<a href="/{ADMIN}/log"><span class="glyphicon glyphicon-info-sign"></span>[alang_old:_PANEL_MENU_OTHER_LOG]</a>
					</li>   
					<li {MENU_CHOOSE:update}>
						<a href="/{ADMIN}/update"><span class="glyphicon glyphicon-refresh"></span>[alang_old:_PANEL_MENU_OTHER_UPDATE]</a>
					</li>   
              </ul>
            </li>
            <li class="sidebar-label pt35"></li>           
            <li><a href="documentation/"><span class="glyphicon glyphicon-bullhorn"></span><span class="sidebar-title">Поддержка</span></a>
            </li>
			<li><a href="documentation/"><span class="glyphicon glyphicon-paperclip"></span><span class="sidebar-title">Документация</span></a>
            </li>			
          </ul>
        </div>
      </aside>
      <section id="content_wrapper">
        <div id="topbar-dropmenu" class="alt">
          <div class="topbar-menu row">
			[ACTIVE_MODULE:news]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/news" class="metro-tile bg-primary light">
					<span class="glyphicon glyphicon-fire text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_NEWS]</span>
				</a>
			</div>
			[/ACTIVE_MODULE]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/user" class="metro-tile bg-info light">
					<span class="glyphicon glyphicon-user text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_USER]</span>
				</a>
			</div>
			[ACTIVE_MODULE:content]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/content" class="metro-tile bg-success light">
					<span class="glyphicon glyphicon-edit text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_COM_STATIC]</span>
				</a>
			</div>
			[/ACTIVE_MODULE]   
			[ACTIVE_MODULE:board]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/board" class="metro-tile bg-system light">
					<span class="glyphicon glyphicon-comment text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_COM_FORUM]</span>
				</a>
			</div>
			[/ACTIVE_MODULE]  
			[ACTIVE_MODULE:gallery]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/gallery" class="metro-tile bg-alert light">
					<span class="glyphicon glyphicon-picture text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_COM_GALLERY]</span>
				</a>
			</div>
			[/ACTIVE_MODULE] 				
            <div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/config" class="metro-tile bg-warning light">
					<span class="fa fa-gears text-muted"></span>
					<span class="metro-title">[alang_old:_PANEL_MENU_CONFIG]</span>
				</a>
			</div>            
          </div>
        </div>
		[CHECK_ACTIVE]
		<header id="topbar" class="text-center bg-white alt ph10 br-b-ddd">			
			<div>		
				{SUBNAV}  				
			</div>
			{TOPBAR}  			       
        </header>
		[/CHECK_ACTIVE]      
		{MODULE}       
      </section>     
    </div>
	<style>
      .btn-toolbar > .btn-group.note-fontname {
        display: none;
      }
    </style>
	{FOOT}
    <script type="text/javascript">
      jQuery(document).ready(function () {
        "use strict";
        // Init Theme Core
        Core.init();
        // Init Demo JS
        Demo.init(); 
		$('input[id="switch"]').bootstrapSwitch('state', true, true);	
        {JS_CODE}         
      });
    </script>
  </body>
</html>