<?php
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
 
global $help;
$help = array();
$help['num'] = 5;