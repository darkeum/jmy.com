<!DOCTYPE html>
<html lang="{LANG}" xmlns="http://www.w3.org/1999/xhtml" xml:lang="{LANG}">
  <head>
	<!-- META -->
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="author" content="JMY CORE" />
	<title>[alang:control_panel] | [alang:auth]</title>
	<base href="{URL}" />
	<!-- FAVICON -->
	<link rel="shortcut icon" href="{ADM_THEME}/assets/images/favicon.ico" />	
	<!-- MAIN CSS -->
	<link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/admin-forms.css" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700" />
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/theme.css" />	
	<script type="text/javascript"> 
		var $buoop = {vs:{i:10,f:-4,o:-4,s:7,c:-4},api:4}; 
		function $buo_f(){ 
		 var e = document.createElement("script"); 
		 e.src = "{ADM_THEME}/assets/js/update.min.js"; 
		 document.body.appendChild(e);
		};
		try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
		catch(e){window.attachEvent("onload", $buo_f)}
	</script>
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
                </div>
              </div>
			<form method="post" action="{ADMIN}">
                <div class="panel-body bg-light p30">
                  <div class="row">
                    <div class="col-sm-7 pr30">                    
                      <div class="section">
                        <label for="username" class="field-label text-muted fs18 mb10">[alang:username]</label>
                        <label for="username" class="field prepend-icon">
                          <input id="username" type="text" name="nick" placeholder="[alang:pre_username]" class="gui-input" />
                          <label for="username" class="field-icon"><i class="fa fa-user"></i></label>
                        </label>
                      </div>
                      <div class="section">
                        <label for="username" class="field-label text-muted fs18 mb10">[alang:password]</label>
                        <label for="password" class="field prepend-icon">
                          <input id="password" type="password" name="password" placeholder="[alang:pre_password]" class="gui-input" />
                          <label for="password" class="field-icon"><i class="fa fa-lock"></i></label>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-5 br-l br-grey pl30">
                      <h3 class="mb25">[alang:control_panel]</h3>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang_old:_APLOGIN_LINE_1]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang_old:_APLOGIN_LINE_2]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang_old:_APLOGIN_LINE_3]</p>
                      <p class="mb15"><span class="fa fa-check text-success pr5"></span> [alang_old:_APLOGIN_LINE_4]</p>
                    </div>
                  </div>
                </div>
                <div class="panel-footer clearfix p10 ph15">
                  <button type="submit" class="button btn-primary mr10 pull-right">[alang:signin]</button>
                  <label class="switch ib switch-primary pull-left input-align mt10">
                    <input id="remember" type="checkbox" name="remember" checked />
                    <label for="remember" data-on="[alang:yes]" data-off="[alang:no]"></label> <span>[alang:remember]</span>
                  </label>
                </div>
              </form>
            </div>
			<div class="login-links">             
              <p class="text-center">{LICENSE}</p>
            </div>
          </div>		  
        </section>	
      </section>	 
    </div>
    <script src="{ADM_THEME}/assets/js/core.min.js" type="text/javascript"></script>
    <script src="{ADM_THEME}/assets/js/utility.js" type="text/javascript"></script>
    <script src="{ADM_THEME}/assets/js/demo.js" type="text/javascript"></script>
    <script src="{ADM_THEME}/assets/js/main.js" type="text/javascript"></script>
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
      });
    </script>
  </body>
</html>