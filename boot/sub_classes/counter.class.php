<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

class Counter {
    var $origin_arr;
    var $modif_arr;
    var $min_word_length = 3;
 
	function explode_str_on_words($text) {
	    $search = array ("'ё'",
	                     "'<script[^>]*?>.*?</script>'si",  
	                     "'<[\/\!]*?[^<>]*?>'si",           
	                     "'([\r\n])[\s]+'",                 
	                     "'&(quot|#34);'i",                
	                     "'&(amp|#38);'i",
	                     "'&(lt|#60);'i",
	                     "'&(gt|#62);'i",
	                     "'&(nbsp|#160);'i",
	                     "'&(iexcl|#161);'i",
	                     "'&(cent|#162);'i",
	                     "'&(pound|#163);'i",
	                     "'&(copy|#169);'i",
	                     "'&#(\d+);'e");
	    $replace = array ("е",
	                      " ",
	                      " ",
	                      "\\1 ",
	                      "\" ",
	                      " ",
	                      " ",
	                      " ",
	                      " ",
	                      chr(161),
	                      chr(162),
	                      chr(163),
	                      chr(169),
	                      "chr(\\1)");
	    $text = preg_replace ($search, $replace, $text);
	    $del_symbols = array(",", ".", ";", ":", "\"", "#", "\$", "%", "^",
	                         "!", "@", "`", "~", "*", "-", "=", "+", "\\",
	                         "|", "/", ">", "<", "(", ")", "&", "?", "?", "\t",
	                         "\r", "\n", "{","}","[","]", "'", "“", "”", "•",
	                         ""._C_1."", ""._C_2."", ""._C_3."", ""._C_4."", ""._C_5."", ""._C_6."",
	                         ""._C_7."", ""._C_8."", ""._C_9."", ""._C_10."", ""._C_11."", ""._C_12."",
	                         ""._C_13."", ""._C_14."", ""._C_15."", ""._C_16."", ""._C_17."", ""._C_18."",
	                         ""._C_19."", ""._C_20."", ""._C_21."", ""._C_22."", ""._C_23."", ""._C_24."",
	                         ""._C_25."", ""._C_26."", ""._C_27."", ""._C_28."", ""._C_29."", ""._C_30."",
	                         "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
	                         );
	    $text = str_replace($del_symbols, array(" "), $text);
	    $this->origin_arr = explode(" ", trim($text));
	    return $this->origin_arr;
	}
	 
	function count_words() {
	    $tmp_arr = array();
	    foreach ($this->origin_arr as $val)
	    {
	        if (mb_strlen($val)>=$this->min_word_length)
	        {
	            $val = mb_strtolower($val);
	            if (array_key_exists($val, $tmp_arr))
	            {
	                $tmp_arr[$val]++;
	            }
	            else
	            {
	                $tmp_arr[$val] = 1;
	            }
	        }
	    }
	    arsort ($tmp_arr);
	    $this->modif_arr = $tmp_arr;
	}
	 
	function get_keywords($text) {
	    $this->explode_str_on_words($text);
	    $this->count_words();
	    $arr = array_slice($this->modif_arr, 0, 30);
	    $str = "";
	    foreach ($arr as $key=>$val)
	    {
	       $str .= $key . ", ";
	    }
	    return trim(mb_substr($str, 0, mb_strlen($str)-2));
	}
}