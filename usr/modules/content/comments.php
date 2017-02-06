<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

$db->query("UPDATE `" . DB_PREFIX . "_content` SET `comments` = `comments`" . $do . "1 WHERE `id` = $id");