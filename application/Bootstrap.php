<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initAppAutoload() {
		$autoloader = new Zend_Application_Module_Autoloader ( array ('namespace' => 'App_', 'basePath' => dirname ( __FILE__ ) ) );
		return $autoloader;
	}

	//Layoute te vecante per cdo modul
	//Layout i cdo moduli eshte si emri i vete-modulit
	protected function _initLayoutHelper()
	{
	    $this->bootstrap('frontController');
	    $layout = Zend_Controller_Action_HelperBroker::addHelper(
	        new App_Controller_Action_Helper_LayoutLoader());
	}

	protected function _initDbconn(){
		$resource = $this->getPluginResource('db');
		$db = $resource->getDbAdapter();
		return $db;
	}

	protected function _initCache(){
		$resource = $this->getPluginResource('cache');
		$cache = $resource->getCache();
		Zend_Registry::set('cache', $cache);

		return $cache;
	}







}

