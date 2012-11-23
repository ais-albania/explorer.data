<?php 
class App_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $bootstrap = $this->getActionController()
                         ->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $module = $this->getRequest()->getModuleName();
	    $this->getActionController()
    		 ->getHelper('layout')
             ->setLayout($module);
    }
    
}
