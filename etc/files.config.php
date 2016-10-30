<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $files_conf;
$files_conf = array();
$files_conf['imgFormats'] = "jpg,jpeg,png,gif,bmp,tiff,svg";
$files_conf['videoFormats'] = "mov, mpeg, m4v, mp4, avi, mpg, wma, flv, webm";
$files_conf['audioFormats'] = "mp3, m4a, ac3, aiff, mid, ogg, wav";
$files_conf['filesFormats'] = "doc, docx, rtf, pdf, xls, xlsx, txt, csv, psd, ppt, pptx, odt, ots, ott, odb, odg, otp, otg, odf, ods, ai";
$files_conf['archiveFormats'] = "zip, rar, gz, tar, iso, dmg";
$files_conf['max_size'] = "10";
$files_conf['thumb_width'] = "600";
$files_conf['quality'] = "100";
$files_conf['watermark'] = "1";
$files_conf['watermark_text'] = "JMY CMS";
$files_conf['watermark_image'] = "";
$files_conf['watermark_valign'] = "bottom";
$files_conf['watermark_halign'] = "left";
$files_conf['lang'] = "ru";

