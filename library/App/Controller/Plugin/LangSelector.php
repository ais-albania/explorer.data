<?php
/**
 * Description of LangSelector
 *
 * @author jon
 */
class App_Controller_Plugin_LangSelector
    extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $lang = $request->getParam('lang','');
        if ($lang !== 'en' && $lang !== 'sq')
            $request->setParam('lang','en');
        
        $lang = $request->getParam('lang');
        if ($lang == 'en')
            $locale = 'en_US';
        else
            $locale = 'sq_AL';

        $zl = new Zend_Locale();
        $zl->setLocale($locale);
        Zend_Registry::set('Zend_Locale', $zl);
        
        $translate = new Zend_Translate('csv', APPLICATION_PATH . '/lang/'. $lang . '.csv' , $lang);
        Zend_Registry::set('Zend_Translate', $translate);

    }

}
