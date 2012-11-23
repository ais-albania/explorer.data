<?php
/**
 * Helper qe kthen nje link nga text ne link te klikueshem
 *
 */
class Zend_View_Helper_MakeClickable extends Zend_View_Helper_Abstract
{
	public function makeClickable($text) {
	  $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
	    '<a href="\\1" target="_blank">\\1</a>', $text);
	  $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
	    '\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
	  $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
	    '<a href="mailto:\\1">\\1</a>', $text);
	return $text;
	}
}