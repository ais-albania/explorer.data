<?php

class Zend_View_Helper_CleanURLText extends Zend_View_Helper_Abstract
{
    public function cleanURLText($toclean,$clsspace=1) {
		if ($clsspace==1){
			$toclean = ereg_replace (" ", "-", $toclean );
		}
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
		$toclean = ereg_replace ( "�", "Euro", $toclean );
		$toclean = ereg_replace ( "�", "&euml;", $toclean );
		$toclean= trim($toclean);		
		return $toclean;
	}
    
}
