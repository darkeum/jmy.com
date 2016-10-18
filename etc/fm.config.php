<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $fm_conf;
$fm_conf = array();
$fm_conf['path'] = $_SERVER['DOCUMENT_ROOT'];