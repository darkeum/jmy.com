<html lang="{LANG}">
  <head>
	<!-- META -->
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="author" content="JMY CMS" />
	<title>[alang:_APANEL] | [alang:_LOGIN]</title>
	<base href="{URL}">
	<!-- FAVICON -->
	<link rel="shortcut icon" href="{ADM_THEME}/assets/images/favicon.ico" />	
	<!-- MAIN CSS -->
	<link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/admin-forms.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/theme.css">	
	<!--[if lt IE 10]>
		<div style="background: #212121; padding: 10px 0; box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3); clear: both; text-align:center; position: relative; z-index:1;"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="/{ADM_THEME}/assets/images/ie8-panel/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
		<script src="/{ADM_THEME}/assets/js/html5shiv.min.js"></script>
	<![endif]-->
  </head>
  <body class="external-page sb-l-c sb-r-c">
    <div id="main" class="animated fadeIn">
      <section id="content_wrapper">
        <div id="canvas-wrapper">
          <canvas id="demo-canvas"></canvas>
        </div>
        <section id="content">
          <div id="login1" class="admin-form theme-info">           
            <div class="panel panel-info mt10 br-n">
              <div class="panel-heading heading-border bg-white">
                <div class="section row mn">
					{SOCIAL}
                  <div class="col-sm-4"><a href="#" class="button btn-social facebook span-left mr5 btn-block"><span><i class="fa fa-facebook"></i></span>Facebook</a></div>
                  <div class="col-sm-4"><a href="#" class="button btn-social vk span-left mr5 btn-block"><span><i class="fa fa-vk"></i></span>Вконтакте</a></div>
                  <div class="col-sm-4"><a href="#" class="button btn-social googleplus span-left btn-block"><span><i class="fa fa-google-plus"></i></span>Google+</a></div>
                </div>
              </div>
			<form role="form" name="form1" method="post" action="">
                <div class="panel-body bg-light p30">
                  <div class="row">
                    <div class="col-sm-7 pr30">
                      <div class="section row hidden">
                        <div class="col-md-4"><a href="#" class="button btn-social facebook span-left mr5 btn-block"><span><i class="fa fa-facebook"></i></span>Facebook</a></div>
                        <div class="col-md-4"><a href="#" class="button btn-social twitter span-left mr5 btn-block"><span><i class="fa fa-twitter"></i></span>Twitter</a></div>
                        <div class="col-md-4"><a href="#" class="button btn-social googleplus span-left btn-block"><span><i class="fa fa-google-plus"></i></span>Google+</a></div>
                      </div>
                      <div class="section">
                        <label for="username" class="field-label text-muted fs18 mb10">[alang:_USERNAME]</label>
                        <label for="username" class="field prepend-icon">
                          <input id="username" type="text" name="nick" placeholder="[alang:_PRE_USERNAME]" class="gui-input">
                          <label for="username" class="field-icon"><i class="fa fa-user"></i></label>
                        </label>
                      </div>
                      <div class="section">
                        <label for="username" class="field-label text-muted fs18 mb10">[alang:_PASSWORD]</label>
                        <label for="password" class="field prepend-icon">
                          <input id="password" type="password" name="password" placeholder="[alang:_PRE_PASSWORD]" class="gui-input">
                          <label for="password" class="field-icon"><i class="fa fa-lock"></i></label>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-5 br-l br-grey pl30">
                      <h3 class="mb25">[alang:_APANEL]</h3>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang:_APLOGIN_LINE_1]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang:_APLOGIN_LINE_2]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang:_APLOGIN_LINE_3]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang:_APLOGIN_LINE_4]</p>
                    </div>
                  </div>
                </div>
                <div class="panel-footer clearfix p10 ph15">
                  <button type="submit" class="button btn-primary mr10 pull-right">[alang:_SIGNIN]</button>
                  <label class="switch ib switch-primary pull-left input-align mt10">
                    <input id="remember" type="checkbox" name="remember" checked="">
                    <label onClick="notif('primary', '_AJAX_INFO', '_AJAX_COMPL');" for="remember" data-on="[alang:_YES]" data-off="[alang:_NO]"></label> <span onClick="notif('primary', '_AJAX_INFO', '_AJAX_COMPL');">[alang:_REMEMBER]</span>
                  </label>
                </div>
              </form>
            </div>
			<div class="login-links">             
              <p><center>{LICENSE}</center></p>
            </div>
          </div>		  
        </section>	
      </section>	 
    </div>
    <script src="{ADM_THEME}/assets/js/core.min.js"></script>
    <script src="{ADM_THEME}/assets/js/utility.js"></script>
    <script src="{ADM_THEME}/assets/js/demo.js"></script>
    <script src="{ADM_THEME}/assets/js/main.js"></script>
    <script type="text/javascript">
      jQuery(document).ready(function () {
        "use strict";
        Core.init();
        Demo.init();
        CanvasBG.init({
          Loc: {
            x: window.innerWidth / 2,
            y: window.innerHeight / 3.3
          },
        });
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
        };
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
		 };
		
      });
    </script>
  </body>
</html>