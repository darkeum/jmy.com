<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<base href="{%URL%}">
		<meta name="viewport" content="width=device-width, user-scalable=1, initial-scale=1, maximum-scale=1">
		<title>{%ERROR_TITLE%}</title>		
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/palette.1.css" id="skin">
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/main.css">
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/animate.min.css">
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/style.1.css" id="font">		
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<link rel="stylesheet" href="usr/tpl/admin/assets/css/panel.css">
		<script src="usr/tpl/admin/assets/js/modernizr.js"></script>
	</head>
	<body class="bg-white app-error">
		<div class="error-container text-center">
			<div class="error-number">404</div>
			<div class="mg-b-lg">{%ERROR%}</div>
			<p>{%TEXT%}</p>
			<ul class="mg-t-lg error-nav">
				<li><a href="{%URL%}">&copy; 2010 - {%D_YEAR%} {%SITE_NAME%}</a></li>
			</ul>
		</div>	
	</body>
</html>