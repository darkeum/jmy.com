
CREATE TABLE `jmy_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `url` varchar(255) NOT NULL,
  `pub_id` varchar(255) DEFAULT NULL,
  `mod` varchar(55) NOT NULL,
  `downloads` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pub_id` (`pub_id`),
  KEY `url` (`url`),
  KEY `pub_id_2` (`pub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_blocks_types` (
  `title` varchar(55) DEFAULT NULL,
  `type` varchar(55) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `jmy_blocks_types` VALUES ('Левые блоки', 'left');INSERT INTO `jmy_blocks_types` VALUES ('Правые блоки', 'right');INSERT INTO `jmy_blocks_types` VALUES ('Баннер сверху', 'bannertop');
CREATE TABLE `jmy_blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `comments` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `ratingUsers` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bid` (`bid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_blog_readers` (
  `bid` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `altname` varchar(55) NOT NULL,
  `description` text NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `posts` int(11) NOT NULL,
  `readersNum` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `lastUpdate` int(11) NOT NULL,
  `admins` varchar(255) NOT NULL,
  `readers` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_board_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `description` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `type` varchar(3) NOT NULL,
  `active` smallint(1) NOT NULL,
  `open` smallint(1) NOT NULL,
  `threads` int(11) NOT NULL,
  `posts` int(11) NOT NULL,
  `lastPost` varchar(55) NOT NULL,
  `lastPoster` varchar(255) NOT NULL,
  `lastTid` int(11) NOT NULL,
  `lastSubject` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rulestitle` varchar(255) NOT NULL,
  `rules` text NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_board_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `allowView` int(1) NOT NULL,
  `allowRead` int(1) NOT NULL,
  `allowCreate` int(1) NOT NULL,
  `allowReply` int(1) NOT NULL,
  `allowEdit` int(1) NOT NULL,
  `allowModer` int(1) NOT NULL,
  `allowAttach` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_board_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `message` text NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `files` text NOT NULL,
  `visible` varchar(1) NOT NULL,
  `editUser` varchar(55) NOT NULL,
  `editReason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

CREATE TABLE `jmy_board_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum` int(11) NOT NULL,
  `title` varchar(55) NOT NULL,
  `poster` int(11) NOT NULL,
  `startTime` varchar(55) NOT NULL,
  `lastTime` varchar(55) NOT NULL,
  `lastPoster` varchar(55) NOT NULL,
  `views` int(11) NOT NULL,
  `replies` int(11) NOT NULL,
  `important` int(1) NOT NULL DEFAULT '0',
  `closed` int(1) NOT NULL DEFAULT '0',
  `score` float(6,3) NOT NULL,
  `votes` smallint(5) NOT NULL,
  `icon` varchar(44) NOT NULL,
  `closetime` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

CREATE TABLE `jmy_board_users` (
  `uid` int(11) NOT NULL,
  `thanks` int(11) NOT NULL,
  `messages` int(11) NOT NULL,
  `specStatus` varchar(255) DEFAULT ' ',
  `lastUpdate` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `jmy_board_users` VALUES ('1', '0', '0', ' ', '0');
CREATE TABLE `jmy_categories` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `altname` varchar(55) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `module` varchar(55) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `position` smallint(5) NOT NULL,
  `parent_id` smallint(5) NOT NULL,
  PRIMARY KEY (`id`,`altname`),
  KEY `altname` (`altname`),
  KEY `parent_id` (`parent_id`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_com_subscribe` (
  `id` int(11) NOT NULL,
  `module` varchar(55) NOT NULL,
  `uid` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`,`module`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_comments` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) NOT NULL,
  `post_id` smallint(5) NOT NULL,
  `module` varchar(55) NOT NULL,
  `text` text,
  `date` varchar(44) NOT NULL,
  `gemail` varchar(55) NOT NULL,
  `gname` varchar(55) NOT NULL,
  `gurl` varchar(55) NOT NULL,
  `parent` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `post_id` (`post_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_comments` VALUES ('1', '0', '2', 'news', 'dawdawdawdawd', '1472921966', 'vaneka97@ya.ru', 'Гость - awdawd', '', '0', '1');
CREATE TABLE `jmy_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `translate` varchar(255) NOT NULL,
  `cat` varchar(200) NOT NULL,
  `keywords` varchar(55) NOT NULL,
  `active` int(1) NOT NULL,
  `date` varchar(55) DEFAULT NULL,
  `comments` int(11) NOT NULL,
  `theme` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_gallery_albums` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `trans` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `views` int(11) NOT NULL,
  `nums` int(11) NOT NULL,
  `last_update` varchar(250) NOT NULL,
  `last_author` varchar(250) NOT NULL,
  `last_image` varchar(250) NOT NULL,
  `watermark` int(1) DEFAULT NULL,
  `sizes` text NOT NULL,
  `gropups_allow` int(11) NOT NULL,
  `dir` varchar(255) NOT NULL,
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_gallery_photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `author` varchar(250) NOT NULL,
  `add_date` varchar(250) NOT NULL,
  `photo_date` varchar(250) NOT NULL,
  `photos` text NOT NULL,
  `tech` text NOT NULL,
  `views` int(11) NOT NULL,
  `gets` int(11) NOT NULL,
  `comments` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `ratings` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `groups_allow` int(11) NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guest` int(1) NOT NULL,
  `user` int(1) NOT NULL,
  `moderator` int(1) NOT NULL,
  `admin` int(1) NOT NULL,
  `banned` int(1) NOT NULL,
  `showHide` int(1) NOT NULL,
  `showAttach` int(1) NOT NULL,
  `loadAttach` int(1) NOT NULL,
  `addPost` int(1) NOT NULL,
  `addComment` int(1) NOT NULL,
  `allowRating` int(1) NOT NULL,
  `maxWidth` int(11) NOT NULL,
  `maxPms` int(11) NOT NULL,
  `control` text NOT NULL,
  `icon` varchar(255) NOT NULL,
  `color` varchar(55) NOT NULL,
  `points` int(11) NOT NULL,
  `protect` int(1) NOT NULL,
  `special` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_groups` VALUES ('1', 'Администраторы', '1', '1', '1', '1', '0', '0', '1', '1', '1', '1', '1', '100', '50', '', 'media/groups/administrator.png', 'red', '0', '1', '0');INSERT INTO `jmy_groups` VALUES ('3', 'Гости', '1', '0', '0', '0', '0', '0', '1', '0', '1', '1', '1', '100', '50', '', 'media/groups/3.png', '', '0', '0', '0');INSERT INTO `jmy_groups` VALUES ('4', 'Боты', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', 'media/groups/4.png', '', '0', '1', '0');INSERT INTO `jmy_groups` VALUES ('2', 'Пользователи', '1', '1', '0', '0', '0', '0', '1', '0', '1', '1', '1', '100', '50', '', 'media/groups/user.png', 'blue', '0', '1', '0');INSERT INTO `jmy_groups` VALUES ('5', 'Забаненые', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '0', '0', '0');
CREATE TABLE `jmy_guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `date` varchar(55) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `website` varchar(75) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `comment` text NOT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_langs` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `postId` varchar(255) DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short` text,
  `full` text,
  `lang` varchar(255) NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_langs` VALUES ('1', '1', 'news', 'Этапы строительства', '<p><audio src=\"http://jmy.com/files/news/1/Alex%20Clare%20-%20Too%20Close%20(DJ%20Sergey%20Fisun%20remix).mp3\" controls=\"controls\">Alex Clare - Too Close (DJ Sergey Fisun remix)</audio></p>\r\n<p><a title=\"Your idea is not bad\" href=\"http://jmy.com/files/news/1/Your%20idea%20is%20not%20bad.docx\">Your idea is not bad</a></p>', '', 'ru');INSERT INTO `jmy_langs` VALUES ('2', '2', 'news', 'dfvdfv', '<p>вам</p>', '', 'ru');
CREATE TABLE `jmy_logs` (
  `time` int(5) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `uid` int(5) NOT NULL,
  `history` varchar(255) NOT NULL,
  `level` smallint(1) NOT NULL,
  PRIMARY KEY (`time`),
  KEY `level` (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `jmy_logs` VALUES ('1472889342', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1472889395', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1472989308', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1473099332', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1473508396', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1473693673', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1473782322', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1474178343', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1474571222', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1474733329', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1474996476', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1474996483', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475125156', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475425860', '127.0.0.1', '1', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475526139', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475526332', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475526392', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475526560', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475526635', '127.0.0.1', '0', 'Неудчаный вход admin9 в панель управления. Вводимый пароль 7365...', '2');INSERT INTO `jmy_logs` VALUES ('1475526925', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475527182', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475594890', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');INSERT INTO `jmy_logs` VALUES ('1475656859', '127.0.0.1', '0', 'Вход admin в панель управления.', '1');
CREATE TABLE `jmy_news` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author` varchar(55) NOT NULL,
  `date` int(11) DEFAULT NULL,
  `tags` varchar(255) NOT NULL,
  `cat` varchar(200) DEFAULT NULL,
  `altname` varchar(55) NOT NULL,
  `keywords` text,
  `description` text,
  `allow_comments` int(1) NOT NULL,
  `allow_rating` int(1) NOT NULL,
  `allow_index` int(1) NOT NULL,
  `score` float(6,3) DEFAULT NULL,
  `votes` smallint(5) NOT NULL,
  `views` smallint(5) NOT NULL,
  `comments` smallint(5) NOT NULL,
  `fields` text NOT NULL,
  `groups` varchar(55) NOT NULL,
  `fixed` int(1) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `altname` (`altname`),
  KEY `active` (`active`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_news` VALUES ('1', 'admin', '1457385120', '', ',0,', 'jetapy_stroitelstva', '', 'Array', '1', '1', '1', '0.000', '0', '26', '0', '', ',0,', '0', '1');INSERT INTO `jmy_news` VALUES ('2', 'admin', '1471245360', '', ',0,', 'dfvdfv', '', 'Array', '1', '1', '1', '0.000', '0', '14', '1', '', ',0,', '0', '1');
CREATE TABLE `jmy_online` (
  `uid` int(11) NOT NULL,
  `time` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `group` int(11) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `jmy_online` VALUES ('1', '1476206816', '127.0.0.1', '1', '/news');
CREATE TABLE `jmy_plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `content` text NOT NULL,
  `file` varchar(55) NOT NULL,
  `priority` tinyint(2) unsigned NOT NULL,
  `type` varchar(55) DEFAULT NULL,
  `service` varchar(44) NOT NULL,
  `showin` varchar(255) NOT NULL,
  `unshow` varchar(255) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `free` tinyint(1) unsigned NOT NULL,
  `template` text,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_plugins` VALUES ('1', 'blog', 'Блог', '', '0', NULL, 'modules', '', 'bannertop', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('2', 'board', 'Форум', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('3', 'content', 'Страницы', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('5', 'feedback', 'Обратная связь', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('6', 'gallery', 'Галерея', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('7', 'news', 'Новости', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('8', 'pm', 'Личные сообщения', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('9', 'profile', 'Профиль', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('10', 'search', 'Поиск', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('11', 'guestbook', 'Гостевая книга', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('12', 'sitemap', 'Карта сайта', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');INSERT INTO `jmy_plugins` VALUES ('13', 'feed', 'Лента нововстей', '', '0', NULL, 'modules', '', '', '', '0', NULL, '1');
CREATE TABLE `jmy_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `toid` int(11) NOT NULL,
  `fromid` int(11) NOT NULL,
  `message` text,
  `time` varchar(55) NOT NULL,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_poll_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `variant` varchar(55) NOT NULL,
  `position` smallint(5) NOT NULL,
  `vote` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_poll_voting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `ip` varchar(55) NOT NULL,
  `time` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `votes` int(5) NOT NULL,
  `max` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_ratings` (
  `_` int(11) NOT NULL AUTO_INCREMENT,
  `id` smallint(5) NOT NULL,
  `uid` smallint(5) NOT NULL,
  `mod` varchar(55) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` varchar(55) NOT NULL,
  PRIMARY KEY (`_`),
  KEY `mod` (`mod`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_sitemap` VALUES ('1', 'Главная страница', 'http://jmy.com/');INSERT INTO `jmy_sitemap` VALUES ('2', 'Блог', 'http://jmy.com/blog');INSERT INTO `jmy_sitemap` VALUES ('3', 'Форум', 'http://jmy.com/board');INSERT INTO `jmy_sitemap` VALUES ('4', 'Страницы', 'http://jmy.com/content');INSERT INTO `jmy_sitemap` VALUES ('5', 'Обратная связь', 'http://jmy.com/feedback');INSERT INTO `jmy_sitemap` VALUES ('6', 'Галерея', 'http://jmy.com/gallery');INSERT INTO `jmy_sitemap` VALUES ('7', 'Гостевая книга', 'http://jmy.com/guestbook');INSERT INTO `jmy_sitemap` VALUES ('8', 'Новости', 'http://jmy.com/news');INSERT INTO `jmy_sitemap` VALUES ('9', 'dfvdfv', 'http://jmy.com/news/dfvdfv.html');INSERT INTO `jmy_sitemap` VALUES ('10', 'Этапы строительства', 'http://jmy.com/news/jetapy_stroitelstva.html');INSERT INTO `jmy_sitemap` VALUES ('11', 'Карта сайта', 'http://jmy.com/sitemap');
CREATE TABLE `jmy_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(55) NOT NULL,
  `module` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_user_carma` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `do` varchar(5) DEFAULT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_user_friends` (
  `who_invite` int(9) NOT NULL,
  `whom_invite` int(9) NOT NULL,
  `confirmed` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_user_visitors` (
  `id` int(9) NOT NULL,
  `visitor` int(9) NOT NULL,
  `time` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jmy_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tail` varchar(55) NOT NULL,
  `email` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `social_id` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `icq` varchar(55) NOT NULL,
  `skype` varchar(55) NOT NULL,
  `surname` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `ochestvo` varchar(55) NOT NULL,
  `place` varchar(255) NOT NULL,
  `age` int(3) NOT NULL,
  `sex` int(1) NOT NULL,
  `birthday` varchar(55) NOT NULL,
  `hobby` varchar(255) NOT NULL,
  `signature` text,
  `points` int(11) DEFAULT '0',
  `carma` int(11) NOT NULL,
  `user_comments` int(11) NOT NULL,
  `user_news` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `exgroup` int(3) NOT NULL,
  `last_visit` int(11) NOT NULL,
  `regdate` int(11) NOT NULL,
  `active` int(1) NOT NULL,
  `ip` varchar(55) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick_2` (`nick`),
  KEY `nick` (`nick`),
  KEY `ip` (`ip`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `jmy_users` VALUES ('1', 'admin', 'e73e95900a30a26af54e3392febd6b5c', '83p4ak43pt', '', '', '', NULL, '', '', '', '', '', '', '0', '0', '', '', NULL, '20', '0', '0', '2', '1', '0', '1475526561', '0', '1', '127.0.0.1', '');INSERT INTO `jmy_users` VALUES ('2', 'zzverr', '21d6d027dde7b0e8c0771a111905cd1f', 'zwpbkp6a', 'vaneka97@yandex.ru', '', '', NULL, '', 'admin', '', '', '', '', '0', '0', '', '', 'wad', '0', '0', '0', '0', '2', '0', '1472922410', '1472922015', '1', '127.0.0.1', '');
CREATE TABLE `jmy_xfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` smallint(1) NOT NULL,
  `content` text NOT NULL,
  `to_user` int(1) NOT NULL,
  `module` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
