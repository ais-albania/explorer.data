<?php

class Zend_View_Helper_PershkrimArtikull extends Zend_View_Helper_Abstract
{
    public function pershkrimArtikull($str, $length, $minword = 3)
	{
    $sub = '';
    $len = 0;
   
    foreach (explode(' ', $str) as $word)
    {
        $part = (($sub != '') ? ' ' : '') . $word;
        $sub .= $part;
        $len += strlen($part);
       
        if (strlen($word) > $minword && strlen($sub) >= $length)
        {
            break;
        }
    }
   
    return $sub . (($len < strlen($str)) ? '...' : '');
	}
}
