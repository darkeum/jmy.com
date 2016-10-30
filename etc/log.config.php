<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $log_conf;
$log_conf = array();
$log_conf['phpError'] = "0";
$log_conf['queryError'] = "1";
$log_conf['dbError'] = "0";
$log_conf['accesError'] = "1";
$log_conf['compressSize'] = "204800";

