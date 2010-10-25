<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Controller_Plugin_Translator gives multilingual support
 *
 * @author miholeus
 */
class Soulex_Controller_Plugin_Translator extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // fetch the current user
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $lang = strtolower($identity->lang);
            $front = Zend_Controller_Front::getInstance();
            $translate = $front->getParam('bootstrap')
                        ->getResource('modules')
                        ->offsetGet('admin')
                        ->getResource('translate');
            if($translate->isAvailable($lang)) {
                $translate->setLocale($lang);
            } else {
                $front->unregisterPlugin('Zend_Translate');
//                $translate->setOptions(array('clear' => true));
//                $translate->addTranslation(APPLICATION_PATH . '/modules/admin/languages', 'en');
//                $translate->setNoTranslation();
//                $translate->setLocale('en');
            }
        }
    }
}