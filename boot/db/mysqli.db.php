<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


class controlDB
{
	
    public $resource = false;
    public $timeQuery = 0;
    public $timeQueries = 0;
    public $numQueries = 0;
    public $listQueries = array();
	
	public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist = false)
	{		
		if($dbpersist)
		{
			$this->resource = mysqli_connect('p:'.$dbhost, $dbuser, $dbpass);
        }
		else
		{
			$this->resource = mysqli_connect($dbhost, $dbuser, $dbpass);
		}		
		if (!mysqli_connect_errno()) 
		{
			mysqli_set_charset($this->resource, 'utf8');
            if (!mysqli_select_db($this->resource, $dbname)) 
			{
				if(file_exists('install.php')) if(!file_exists('install/lock.install')) { Header('Location: /install.php'); }
                mysqlFatalError('Ошибка в базе данных MySQL', 'База данных ' . $dbname . ' не найдена', '');
				
            }			
        } 
		else 
		{
			if(file_exists('install.php')) if(!file_exists('install/lock.install')) { Header('Location: /install.php'); }
			mysqlFatalError($lang['no'].'Ошибка в базе данных MySQL', 'Нет подключения к ' . $dbhost, '');
			exit();
		}
	}
	
	function safesql($str)
	{
		return mysqli_real_escape_string($this->resource, $str);
	}
	
	public function doQuery($str, $ignoreError, $prefix)
	{
        $timer = microtime(1);
		
		if($ignoreError)
		{
			$result = mysqli_query($this->resource, $str) or writeInLog('[Ошибка в базе данных] - запрос: ' .  str_replace($prefix, '', $str), 'db_query');
		}
		else
		{
			$result = mysqli_query($this->resource, $str) or mysqlFatalError('Ошибка выполнения запроса в DB', "Не удалось выполнить запрос '" . str_replace($prefix, '', $str) . "' <br />Ответ с сервера: " . str_replace($prefix, '', mysqli_error($this->resource)), str_replace($prefix, '', $str));
		}
		
		$this->timeQuery += microtime(1) - $timer;
        $this->timeQueries += $this->timeQuery;
		$this->numQueries++;

        if(DEBUG) $this->listQueries[$this->numQueries] = array($str, $this->timeQuery);

		return $result;
	}
	
	public function freeResult($resource)
	{
        if ($resource instanceof mysqli_result) 
		{
            return mysqli_free_result($resource);
        } 
		else 
		{
            return false;
        }
	}
	
	public function getRow($resource)
	{
        if ($resource instanceof mysqli_result) 
		{
            return @mysqli_fetch_assoc($resource);
        } 
		else 
		{
            return false;
        }
	}	
	
	public function fetchRow($resource)
	{
        if ($resource instanceof mysqli_result) 
		{
            return @mysqli_fetch_array($resource);
        } 
		else 
		{
            return false;
        }
	}	
	
	public function numRows($resource)
	{
		if ($resource instanceof mysqli_result) 
		{
            return mysqli_num_rows($resource);
        } 
		else 
		{
            return false;
        }
	}
	
	public function info()
	{
        return mysqli_info($this->resource);
	}
}