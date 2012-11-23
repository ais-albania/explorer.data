<?php

class Zend_View_Helper_Convertcharset extends Zend_View_Helper_Abstract
{
    
    public function convertcharset($toclean)
    {
        $toclean = ereg_replace ( "ë", "e", $toclean );
		$toclean = ereg_replace ( "�", "e", $toclean );
		$toclean = ereg_replace ( "�", "e", $toclean );
		$toclean = ereg_replace ( "�", "E", $toclean );		
		$toclean = ereg_replace ( "\+", "-", $toclean );		
		$toclean = ereg_replace ( "ç", "c", $toclean );
		$toclean = ereg_replace ( "\"", "", $toclean );
		$toclean = ereg_replace ( "\?", "", $toclean );
		$toclean = ereg_replace ( ",", "", $toclean );
		$toclean = ereg_replace ( ":", "", $toclean );
		$toclean = ereg_replace ( "�", "", $toclean );
		$toclean = ereg_replace ( "�", "", $toclean );
		$toclean = ereg_replace ( "\�", "", $toclean );
		$toclean = ereg_replace ( "\�", "\'", $toclean );
		$toclean = ereg_replace ( "�", "C", $toclean );
		$toclean = ereg_replace ( "�", "c", $toclean );
		$toclean = ereg_replace ( "�", "-", $toclean );
		$toclean = ereg_replace ( "�", "  ", $toclean );
		$toclean = ereg_replace ( "�", "O", $toclean );
		$toclean = ereg_replace ( "�", "u", $toclean );
		$toclean = ereg_replace ( "�", "a", $toclean );
		$toclean = ereg_replace ( "%", "", $toclean );
		$toclean = ereg_replace ( "'", "", $toclean );
		$toclean = ereg_replace ( "&nbsp;", "", $toclean );
		return $toclean= trim($toclean);
    	//return iconv("UTF-8","ISO-8859-1//IGNORE",$text);
    }
    
}
