<html lang="{LANG}">
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
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/address.css">
	<link rel="stylesheet" type="text/css" href="http://static.livedemo00.template-help.com/wt_58708/plugins/css/magnific-popup.css">	
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/typeahead.js-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700">
    <link rel="stylesheet" type="text/css" href="/{ADM_THEME}/assets/css/theme.css">
	<!--[if lt IE 10]>
		<div style="background: #212121; padding: 10px 0; box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3); clear: both; text-align:center; position: relative; z-index:1;"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="/{ADM_THEME}/assets/images/ie8-panel/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
		<script src="/{ADM_THEME}/assets/js/html5shiv.min.js"></script>
	<![endif]-->
	  <!-- core scripts-->
    <script src="/{ADM_THEME}/assets/js/core.min.js"></script>
    <!-- Theme Javascript-->
    <script src="/{ADM_THEME}/assets/js/utility.js"></script>
    <script src="/{ADM_THEME}/assets/js/demo.js"></script>
    <script src="http://livedemo00.template-help.com/wt_58708/assets/js/main.js"></script>
  <script src="http://livedemo00.template-help.com/wt_58708/assets/js/demo/widgets.js"></script>

   <script type="text/javascript">
   
    function modal_o(id) {
          // Inline Admin-Form example
          $.magnificPopup.open({
            removalDelay: 500, //delay removal by X to allow out-animation,
            items: {
              src: id
            },
            // overflowY: 'hidden', //
            callbacks: {
              beforeOpen: function (e) {
                var Animation = 'mfp-zoomIn';
                this.st.mainClass = Animation;
              }
            },
            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
          });
        };
   var Stacks = {
          stack_top_right: {
            "dir1": "down",
            "dir2": "left",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_top_left: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_left: {
            "dir1": "right",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_right: {
            "dir1": "left",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bar_top: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_bar_bottom: {
            "dir1": "up",
            "dir2": "right",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_context: {
            "dir1": "down",
            "dir2": "left",
            "context": $("#stack-context")
          },
        }

   // PNotify Plugin Event Init
       function notif(style, title_in, text_in) {
          var noteStyle = style;
          var noteShadow = true;
          var noteOpacity = '1';
          var noteStack = 'stack_bar_bottom';
          var width = "290px";
          // If notification stack or opacity is not defined set a default
          var noteStack = noteStack ? noteStack : "stack_top_right";
          var noteOpacity = noteOpacity ? noteOpacity : "1";
          // We modify the width option if the selected stack is a fullwidth style
          function findWidth() {
            if (noteStack == "stack_bar_top") {
              return "100%";
            }
            if (noteStack == "stack_bar_bottom") {
              return "70%";
            } else {
              return "290px";
            }
          }
   
      
	   new PNotify({
            title: title_in,
            text: text_in,
            shadow: noteShadow,
            opacity: noteOpacity,
            addclass: noteStack,
            type: noteStyle,
            stack: Stacks[noteStack],
            width: findWidth(),
            delay: 1400
          });
		 }
 </script>
  </head>  
  <body data-spy="scroll" data-target="#nav-spy" data-offset="300" class="form-editors-page {BODY_CLASS}">   
    <!-- Start: Main-->
    <div id="main">
      <!-- Start: Header-->	
      <header class="navbar navbar-fixed-top bg-success">
        <div class="navbar-branding dark bg-success">
			<a href="/{ADMIN}" class="navbar-brand text-uppercase"><i class="fa fa-check-circle mg-r-xs"></i> JMY<b>CMS</b></a>
			<span id="toggle_sidemenu_l" class="fa fa-bars"></span>
		</div>
        <ul class="nav navbar-nav navbar-left"></ul>
        <form role="search" action="/{ADMIN}/search" class="navbar-form navbar-left navbar-search alt">
			<span class="hide visible-md-inline-block visible-lg-inline-block fa fa-search fs18"></span>
			<div class="form-group">
				<input type="text"  name="search" placeholder="[alang:_PANEL_SEARCH]" class="form-control">
			</div>
        </form>
        <ul class="nav navbar-nav navbar-right">		  
		  <li>
            <div class="navbar-btn btn-group">
              <button class="btn btn-sm request-fullscreen"><span class="fa fa-arrows-alt fs15"></span></button>
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
					  <div class="panel-menu"><span class="panel-icon"><i class="fa fa-clock-o"></i></span><span class="panel-title fw600"> [alang:_PANEL_SUNMENU_NOTIF]</span></div>
					  <div class="w100p panel-body panel-scroller scroller-navbar scroller-overlay scroller-pn pn">
						<ol class="timeline-list">
						  {NOTIF}
						</ol>
					  </div>
					  <div class="panel-footer text-center p7"><a href="#" class="link-unstyled"> View All</a></div>
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
							<optgroup label="[alang:_PANEL_SUNMENU_STATUS]">
								<option value="3" {HIDE_STATUS_3}>[alang:_PANEL_SUNMENU_ALWAYS]</option>
								<option value="2" {HIDE_STATUS_2}>[alang:_OFFLINE]</option>
								<option value="1" {HIDE_STATUS_1}>[alang:_ONLINE]</option>
							</optgroup>
						</select>
					</div>
					<div class="pull-right mr10">
						<div class="btn-group">
							<button type="button" class="multiselect dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" style="max-width: 100px;" aria-expanded="false">[alang:_PANEL_SUNMENU_VIEW] <b class="caret-right"></b></button></div>
                </div>
				</li>
				
              <li class="list-group-item"><a href="#" class="animated animated-short fadeInUp"><span class="fa fa-envelope"></span> Messages<span class="label label-warning">2</span></a></li>
              <li class="list-group-item"><a href="#" class="animated animated-short fadeInUp"><span class="fa fa-user"></span> Friends<span class="label label-warning">6</span></a></li>
              <li class="list-group-item"><a href="#" class="animated animated-short fadeInUp"><span class="fa fa-bell"></span> Notifications</a></li>
              <li class="list-group-item"><a href="#" class="animated animated-short fadeInUp"><span class="fa fa-gear"></span> Settings</a></li>
              <li class="dropdown-footer"><a href="pages_login.html"><span class="fa fa-power-off pr5"></span> [alang:_PANEL_SUNMENU_EXIT]</a></li>
            </ul>
          </li>
        </ul>
      </header>
      <!-- Start: Sidebar-->
      <aside id="sidebar_left" class="nano nano-light affix has-scrollbar sidebar-light">
        <!-- Start: Sidebar Left Content-->
        <div class="sidebar-left-content nano-content">
          <!-- Start: Sidebar Header-->
          <header class="sidebar-header">
            <!-- Sidebar Widget - Search (hidden)-->
            <div class="sidebar-widget search-widget hidden">
              <div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input id="sidebar-search" type="text" placeholder="Search..." class="form-control"/>
              </div>
            </div>
          </header>
          <ul class="nav sidebar-menu">           
            <li {MENU_CHOOSE:main}> 
				<a href="/{ADMIN}"><span class="glyphicon glyphicon-home"></span><span class="sidebar-title">[alang:_PANEL_MENU_MAIN]</span></a>
            </li> 
			[ACTIVE_MODULE:news]
            <li>				
				<a href="#" class="accordion-toggle {MENU_OPEN:news,cats,fm,xfields}"><span class="glyphicon glyphicon-fire"></span><span class="sidebar-title">[alang:_PANEL_MENU_NEWS]</span><span class="caret"></span></a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:news}>
						<a href="/{ADMIN}/module/news">
							<span class="glyphicon glyphicon-modal-window"></span>[alang:_PANEL_MENU_NEWS_MANAGER]
						</a>
					</li>
					<li {MENU_CHOOSE:cats}>
						<a href="/{ADMIN}/cats">
							<span class="glyphicon glyphicon-list"></span>[alang:_PANEL_MENU_NEWS_CAT]
						</a>
					</li>
					<li {MENU_CHOOSE:xfields}>
						<a href="/{ADMIN}/xfields">
							<span class="glyphicon glyphicon-star"></span>[alang:_PANEL_MENU_NEWS_XFIELDS]
						</a>
					</li>
					<li {MENU_CHOOSE:fm}>
						<a href="/{ADMIN}/fm">
							<span class="glyphicon glyphicon-folder-open"></span>[alang:_PANEL_MENU_NEWS_FM]
						</a>
					</li>
				</ul>
            </li>
			[/ACTIVE_MODULE]
			[ACTIVE_MODULE:user]
            <li>
				<a href="#" class="accordion-toggle {MENU_OPEN:user,groups,comments,voting}">
					<span class="glyphicon glyphicon-user"></span>
					<span class="sidebar-title">[alang:_PANEL_MENU_USER]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:user}>
						<a href="/{ADMIN}/user"><span class="glyphicon glyphicon-list-alt"></span>[alang:_PANEL_MENU_USER_MANAGER]</a>
					</li>
					<li {MENU_CHOOSE:groups}>
						<a href="/{ADMIN}/groups"><span class="glyphicon glyphicon-tag"></span>[alang:_PANEL_MENU_USER_GROUP]</a>
					</li>
					<li {MENU_CHOOSE:comments}>
						<a href="/{ADMIN}/comments"><span class="glyphicon glyphicon-comment"></span>[alang:_PANEL_MENU_USER_COMMENT]</a>
					</li>
					<li {MENU_CHOOSE:voting}>
						<a href="/{ADMIN}/voting"><span class="glyphicon glyphicon-plus-sign"></span>[alang:_PANEL_MENU_USER_POLL]</a>
					</li>                
				</ul>
            </li>
			[/ACTIVE_MODULE]
            <li>
				<a href="#" class="accordion-toggle {MENU_OPEN:board,blog,gallery,guestbook,content}">
					<span class="glyphicon glyphicon-th-large"></span>
					<span class="sidebar-title">[alang:_PANEL_MENU_COM]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					[ACTIVE_MODULE:board]
					<li {MENU_CHOOSE:board}>
						<a href="/{ADMIN}/module/board"><span class="fa fa-bullhorn"></span>[alang:_PANEL_MENU_COM_FORUM]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:blog]
					<li {MENU_CHOOSE:blog}>
						<a href="/{ADMIN}/module/blog"><span class="fa fa-stack-exchange"></span>[alang:_PANEL_MENU_COM_BLOG]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:gallery]
					<li {MENU_CHOOSE:gallery}>
						<a href="/{ADMIN}/module/gallery"><span class="fa fa-picture-o"></span>[alang:_PANEL_MENU_COM_GALLERY]</a>
					</li>
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:guestbook]
					<li {MENU_CHOOSE:guestbook}>
						<a href="/{ADMIN}/module/guestbook"><span class="fa fa-comments-o"></span>[alang:_PANEL_MENU_COM_GUESTBOOK]</a>
					</li> 
					[/ACTIVE_MODULE]
					[ACTIVE_MODULE:content]
					<li {MENU_CHOOSE:content}>
						<a href="/{ADMIN}/module/content"><span class="fa fa-square-o"></span>[alang:_PANEL_MENU_COM_STATIC]</a>
					</li>  
					[/ACTIVE_MODULE]
              </ul>
            </li> 
			<li {MENU_CHOOSE:config}> 
				<a href="/{ADMIN}/config">
					<span class="glyphicon glyphicon-cog"></span>
					<span class="sidebar-title">[alang:_PANEL_MENU_CONFIG]  </span>
				</a>
            </li> 
			<li>
				<a href="#" class="accordion-toggle {MENU_OPEN:modules,blocks,templates}">
					<span class="glyphicon glyphicon-send"></span>
					<span class="sidebar-title">[alang:_PANEL_MENU_EXP]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:modules}>
						<a href="/{ADMIN}/modules"><span class="fa fa-cloud"></span>[alang:_PANEL_MENU_EXP_MODULES]</a>
					</li>
					<li {MENU_CHOOSE:blocks}>
						<a href="/{ADMIN}/blocks"><span class="fa fa-code"></span>[alang:_PANEL_MENU_EXP_BLOCKS]</a>
					</li>
					<li {MENU_CHOOSE:templates}>
						<a href="/{ADMIN}/templates"><span class="fa fa-pencil-square-o"></span>[alang:_PANEL_MENU_EXP_TPL]</a>
					</li>					
              </ul>
            </li>
			{MENU_MODULES}
			<li>
				<a href="#" class="accordion-toggle">
					<span class="glyphicon glyphicon-question-sign"></span>
					<span class="sidebar-title">[alang:_PANEL_MENU_OTHER]</span>
					<span class="caret"></span>
				</a>
				<ul class="nav sub-nav">
					<li {MENU_CHOOSE:user}>
						<a href="/{ADMIN}/module/sitemap"><span class="glyphicon glyphicon-list-alt"></span>[alang:_PANEL_MENU_OTHER_MAP]</a>
					</li>
					<li {MENU_CHOOSE:smiles}>
						<a href="/{ADMIN}/smiles"><span class="glyphicon glyphicon-tag"></span>[alang:_PANEL_MENU_OTHER_SMILES]</a>
					</li>
					<li {MENU_CHOOSE:db}>
						<a href="/{ADMIN}/db"><span class="glyphicon glyphicon-comment"></span>[alang:_PANEL_MENU_OTHER_BD]</a>
					</li>
					<li {MENU_CHOOSE:log}>
						<a href="/{ADMIN}/log"><span class="glyphicon glyphicon-plus-sign"></span>[alang:_PANEL_MENU_OTHER_LOG]</a>
					</li>   
					<li {MENU_CHOOSE:update}>
						<a href="/{ADMIN}/update"><span class="glyphicon glyphicon-plus-sign"></span>[alang:_PANEL_MENU_OTHER_UPDATE]</a>
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
        <!-- Start: Topbar-Dropdown-->
        <div id="topbar-dropmenu" class="alt">
          <div class="topbar-menu row">
			[ACTIVE_MODULE:news]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/news" class="metro-tile bg-primary light">
					<span class="glyphicon glyphicon-fire text-muted"></span>
					<span class="metro-title">[alang:_PANEL_MENU_NEWS_MANAGER]</span>
				</a>
			</div>
			[/ACTIVE_MODULE]
			[ACTIVE_MODULE:user]
			<div class="col-xs-4 col-sm-2">
				<a href="/{ADMIN}/module/user" class="metro-tile bg-info light">
					<span class="glyphicon glyphicon-user text-muted"></span>
					<span class="metro-title">Пользователи</span>
				</a>
			</div>
			[/ACTIVE_MODULE]
         
            <div class="col-xs-4 col-sm-2"><a href="#" class="metro-tile bg-success light"><span class="glyphicon glyphicon-edit text-muted"></span><span class="metro-title">Статические страницы</span></a></div>
            <div class="col-xs-4 col-sm-2"><a href="#" class="metro-tile bg-system light"><span class="glyphicon glyphicon-comment text-muted"></span><span class="metro-title">Форум</span></a></div>
			<div class="col-xs-4 col-sm-2"><a href="#" class="metro-tile bg-alert light"><span class="glyphicon glyphicon-picture text-muted"></span><span class="metro-title">Галлерея</span></a></div>
            <div class="col-xs-4 col-sm-2"><a href="#" class="metro-tile bg-warning light"><span class="fa fa-gears text-muted"></span><span class="metro-title">Настройки</span></a></div>            
          </div>
        </div>
        <!-- Start: Topbar-->
		[CHECK_ACTIVE]
		 <header id="topbar" class="text-center bg-white alt ph10 br-b-ddd">			
			<div>		
				{SUBNAV}  				
			</div>
			{TOPBAR}  			       
        </header>
		[/CHECK_ACTIVE]      
        <!-- Begin: Content-->
		{MODULE}       
      </section>
      <!-- Start: Right Sidebar-->
      <aside id="sidebar_right" class="nano affix">
        <!-- Start: Sidebar Right Content-->
        <div class="sidebar-right-content nano-content">
          <div class="tab-block sidebar-block br-n">
            <ul class="nav nav-tabs tabs-border nav-justified hidden">
              <li class="active"><a href="#sidebar-right-tab1" data-toggle="tab">Tab 1</a></li>
              <li><a href="#sidebar-right-tab2" data-toggle="tab">Tab 2</a></li>
              <li><a href="#sidebar-right-tab3" data-toggle="tab">Tab 3</a></li>
            </ul>
            <div class="tab-content br-n">
              <div id="sidebar-right-tab1" class="tab-pane active">
                <h5 class="title-divider text-muted mb20">Server Statistics<span class="pull-right">2013<i class="fa fa-caret-down ml5"></i></span></h5>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 44%" class="progress-bar progress-bar-primary"><span class="fs11">DB Request</span></div>
                </div>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 84%" class="progress-bar progress-bar-info"><span class="fs11 text-left">Server Load</span></div>
                </div>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 61%" class="progress-bar progress-bar-warning"><span class="fs11 text-left">Server Connections</span></div>
                </div>
                <h5 class="title-divider text-muted mt30 mb10">Traffic Margins</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">132</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-success-dark mn"><i class="fa fa-caret-up"></i> 13.2%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt25 mb10">Database Request</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">212</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-success-dark mn"><i class="fa fa-caret-up"></i> 25.6%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt25 mb10">Server Response</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">82.5</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-danger mn"><i class="fa fa-caret-down"></i> 17.9%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt40 mb20">Server Statistics<span class="pull-right text-primary fw600">USA</span></h5>
              </div>
              <div id="sidebar-right-tab2" class="tab-pane"></div>
              <div id="sidebar-right-tab3" class="tab-pane"></div>
            </div>
          </div>
        </div>
      </aside>
    </div>
	   <style>
      /* demo styles -summernote */
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
    
     
         {JS_CODE}
         
      });
    </script>

	
  </body>
</html>