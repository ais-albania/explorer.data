<?php

class Zend_View_Helper_Keyword extends Zend_View_Helper_Abstract
{
    public function keyword($str, $length, $minword = 1)
	{
    $sub = '';
    $len = 0;
   
    foreach (explode(',', $str) as $word)
    {
        $part = (($sub != '') ? ' ' : '') . $word;
        $sub .= $part;
        $len += strlen($part);
       
        if (strlen($word) > $minword && strlen($sub) >= $length)
        {
            break;
        }
    }
   
    return ucfirst($sub . (($len < strlen($str)) ? '' : ''));
	}
}
