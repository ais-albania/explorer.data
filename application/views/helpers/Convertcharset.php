<?php

class Zend_View_Helper_Convertcharset extends Zend_View_Helper_Abstract
{
    
    public function convertcharset($toclean)
    {
        $toclean = ereg_replace ( "รซ", "e", $toclean );
		$toclean = ereg_replace ( "๋", "e", $toclean );
		$toclean = ereg_replace ( "้", "e", $toclean );
		$toclean = ereg_replace ( "ห", "E", $toclean );		
		$toclean = ereg_replace ( "\+", "-", $toclean );		
		$toclean = ereg_replace ( "รง", "c", $toclean );
		$toclean = ereg_replace ( "\"", "", $toclean );
		$toclean = ereg_replace ( "\?", "", $toclean );
		$toclean = ereg_replace ( ",", "", $toclean );
		$toclean = ereg_replace ( ":", "", $toclean );
		$toclean = ereg_replace ( "“", "", $toclean );
		$toclean = ereg_replace ( "”", "", $toclean );
		$toclean = ereg_replace ( "\’", "", $toclean );
		$toclean = ereg_replace ( "\‘", "\'", $toclean );
		$toclean = ereg_replace ( "ว", "C", $toclean );
		$toclean = ereg_replace ( "็", "c", $toclean );
		$toclean = ereg_replace ( "–", "-", $toclean );
		$toclean = ereg_replace ( "…", "  ", $toclean );
		$toclean = ereg_replace ( "ึ", "O", $toclean );
		$toclean = ereg_replace ( "ü", "u", $toclean );
		$toclean = ereg_replace ( "แ", "a", $toclean );
		$toclean = ereg_replace ( "%", "", $toclean );
		$toclean = ereg_replace ( "'", "", $toclean );
		$toclean = ereg_replace ( "&nbsp;", "", $toclean );
		return $toclean= trim($toclean);
    	//return iconv("UTF-8","ISO-8859-1//IGNORE",$text);
    }
    
}
