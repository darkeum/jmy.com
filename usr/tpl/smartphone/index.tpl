
<!DOCTYPE html>
<html class="no-js">
  <head>
	{%META%}
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:100,300,400,500" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animsition/3.5.2/css/animsition.css">
    <link rel="stylesheet" type="text/css" href="{%THEME%}/assest/css/sweetalert.css">
    <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.2/material.indigo-pink.min.css">  
    <link rel="stylesheet" href="{%THEME%}/assest/css/swipebox.min.css">
    <link rel="stylesheet" href="{%THEME%}/assest/css/style.css">
	<link rel="stylesheet" href="{%THEME%}/assest/css/theme.css">
		<link rel="stylesheet" href="{%THEME%}/assest/css/engine.css">
 </head>
  <body>
    <div class="animsition">
      <!-- Header -->
      <div class="mdl-layout mdl-js-layout mdl-layout--overlay-drawer-button">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
          <div class="mdl-layout__header-row">
            <!-- Title -->
            <span class="mdl-layout-title">{%SITE_NAME%}</span>
            <!-- Spacer -->
            <div class="mdl-layout-spacer"></div>
            <!-- Right Menu -->
            <button id="top-header-menu" class="mdl-button mdl-js-button mdl-button--icon">
              <i class="material-icons">more_vert</i>
            </button>
          </div>
        </header>
        <!-- Top-right Dropdown Menu -->
        <div class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="top-header-menu">
		[guest]	
          <a href="{%URL_LOGIN%}" class="animsition-link"><span class="mdl-menu__item">Вход</span></a>
		  <a href="{%URL_REG%}" class="animsition-link"><span class="mdl-menu__item">Регистрация</span></a>
		[/guest]
		[user]
			<a href="{%URL_PROFIL%}" class="animsition-link"><span class="mdl-menu__item">Профиль</span></a>
			<a href="{%URL_PM%}" class="animsition-link"><span class="mdl-menu__item mdl-badge" data-badge="{%NEW_PM%}">Сообщения</span></a>         
			<a href="{%URL_LOGOUT%}" class="animsition-link"><span class="mdl-menu__item">Выход</span></a>
		[/user]
        </div>
        <!-- Sidebar -->
        <div class="mdl-layout__drawer">
          <!-- Top -->
          <div class="mdl-card mdl-shadow--2dp mdl-color--primary mdl-color-text--blue-grey-50 drawer-profile">
            <div class="mdl-card__title user">
           
              <span class="user-name">{%SITE_NAME%}</span>
              <span class="user-mail">Добро пожаловать!</span>
              <button id="user-menu" class="mdl-button mdl-js-button mdl-button--icon">
                <i class="material-icons">arrow_drop_down</i>
              </button>
            </div>            
          </div>
          <!-- Dropdown Menu -->
          <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="user-menu">
            <li class="mdl-menu__item"><a href="{%URL_PDA%}" class="animsition-link"><span class="mdl-menu__item">Полная версия</span></a></li>
          </ul>
          <!-- Main Navigation -->
          <nav class="mdl-navigation">
            <a class="mdl-navigation__link animsition-link" href="/"><i class="material-icons">home</i><span>Главная</span></a>
			<div class="mdl-collapse">
                <a class="mdl-navigation__link mdl-collapse__button"><i class="material-icons">library_books</i>
                    <i class="material-icons mdl-collapse__icon mdl-animation--default">expand_more</i>
                    <span>Новости</span>
                </a>
                <div class="mdl-collapse__content-wrapper">
                  <div class="mdl-collapse__content mdl-animation--default" style="margin-top: -156px;">
                    <a class="mdl-navigation__link animsition-link" href="{%URL_NEWS%}">Все новости</a>
                    <a class="mdl-navigation__link animsition-link" href="#">Первая категория</a>
                  </div>
                </div>
            </div>
            <a class="mdl-navigation__link animsition-link" href="{%URL_GUEST%}"><i class="material-icons">account_circle</i><span>Гостевая книга</span></a>
            <div class="mdl-collapse">
                <a class="mdl-navigation__link mdl-collapse__button"><i class="material-icons">image</i>
                    <i class="material-icons mdl-collapse__icon mdl-animation--default">expand_more</i>
                    <span>Галерея</span>
                </a>
                <div class="mdl-collapse__content-wrapper">
                  <div class="mdl-collapse__content mdl-animation--default" style="margin-top: -156px;">
                    <a class="mdl-navigation__link animsition-link" href="#">Лучшие фотографии</a>
                    <a class="mdl-navigation__link animsition-link" href="#">Все альбомы</a>
                  </div>
                </div>
            </div>
			<a class="mdl-navigation__link animsition-link" href="/"><i class="material-icons">feedback</i><span>Контакты</span></a>
          </nav>
        </div>

        <!-- Page Content -->
        <main class="mdl-layout__content">
          <!-- cards grid -->
          <div class="mdl-grid">
            <!-- bold card -->
			{%MODULE%}
           </div>         
          <!-- load more -->          
        </main>
      </div>
    </div>

    <script src="{%THEME%}/assest/js/jquery-2.1.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animsition/3.5.2/js/jquery.animsition.js"></script>
    <script src="{%THEME%}/assest/js/sweetalert.min.js"></script> 
    <script src="https://storage.googleapis.com/code.getmdl.io/1.0.2/material.min.js"></script>
    <script src="{%THEME%}/assest/js/jquery.swipebox.min.js"></script>
    <script src="{%THEME%}/assest/js/function.js"></script>
  </body>
</html>