<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $news_conf;
$news_conf = array();
$news_conf['num'] = "5";
$news_conf['comments_num'] = "10";
$news_conf['fullLink'] = "on";
$news_conf['noModer'] = "1";
$news_conf['preModer'] = "5";
$news_conf['related_news'] = "5";
$news_conf['addNews'] = "on";
$news_conf['showCat'] = "on";
$news_conf['subLoad'] = "on";
$news_conf['catCols'] = "3";
$news_conf['showBreadcumb'] = "on";
$news_conf['tags'] = "on";
$news_conf['tags_num'] = "3";
$news_conf['tagIll'] = "on";
$news_conf['illFormat'] = "<b>{tag}</b>";
$news_conf['limitStar'] = "5";
$news_conf['starStyle'] = "";
$news_conf['carma_rate'] = "on";
$news_conf['carma_summ'] = "on";
$news_conf['fileEditor'] = "on";
$news_conf['imgFormats'] = "jpg,gif,png";
$news_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$news_conf['max_size'] = "1024000000000000000";
$news_conf['thumb_width'] = "600";
$news_conf['admin_url_1'] = "on";
$news_conf['admin_url_2'] = "";
$news_conf['admin_url_3'] = "";
$news_conf['admin_url_4'] = "";

